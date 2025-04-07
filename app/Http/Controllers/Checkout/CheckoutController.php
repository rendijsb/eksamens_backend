<?php

declare(strict_types=1);

namespace App\Http\Controllers\Checkout;

use App\Enums\Payments\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\InitiateCheckoutRequest;
use App\Http\Requests\Checkout\ProcessPaymentRequest;
use App\Http\Resources\Orders\OrderResource;
use App\Models\Orders\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    private CartService $cartService;
    private OrderService $orderService;
    private StripeService $stripeService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        StripeService $stripeService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
    }

    public function initiateCheckout(InitiateCheckoutRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()?->getId();
            $sessionId = $request->cookie('cart_session_id');

            $cart = $this->cartService->getCart($userId, $sessionId);

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            $order = $this->orderService->createOrderFromCart($cart, $request->validated());

            $paymentIntent = $this->stripeService->createPaymentIntent($order);

            return response()->json([
                'success' => true,
                'order' => new OrderResource($order),
                'payment' => [
                    'client_secret' => $paymentIntent['clientSecret'],
                    'payment_intent_id' => $paymentIntent['id'],
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Checkout initiation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        try {
            $paymentIntentId = $request->getPaymentIntentId();
            $orderId = $request->getOrderId();

            $order = $this->orderService->getOrderById($orderId);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $updatedOrder = $this->orderService->updateOrderPayment(
                    $order,
                    $paymentIntentId,
                    PaymentStatusEnum::PAID->value,
                    [
                        'payment_method_type' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                        'amount_received' => $paymentIntent->amount_received / 100,
                        'status' => $paymentIntent->status,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'order' => new OrderResource($updatedOrder)
                ]);
            } else {
                $this->orderService->updateOrderPayment(
                    $order,
                    $paymentIntentId,
                    PaymentStatusEnum::FAILED->value,
                    [
                        'payment_method_type' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                        'status' => $paymentIntent->status,
                        'error' => $paymentIntent->last_payment_error ?? 'Payment failed'
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . ($paymentIntent->last_payment_error ? $paymentIntent->last_payment_error->message : 'Unknown error')
                ], 400);
            }
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleStripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->handleWebhookEvent($payload, $sigHeader);

            if ($event['success'] && $event['type'] === 'payment_intent.succeeded') {
                $paymentIntent = $event['paymentIntent'];
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order) {
                        $this->orderService->updateOrderPayment(
                            $order,
                            $paymentIntent->id,
                            PaymentStatusEnum::PAID->value,
                            [
                                'payment_method_type' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                                'amount_received' => $paymentIntent->amount_received / 100,
                                'status' => $paymentIntent->status,
                            ]
                        );
                    }
                }
            }

            if ($event['type'] === 'payment_intent.payment_failed') {
                $paymentIntent = $event['paymentIntent'];
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order) {
                        $this->orderService->updateOrderPayment(
                            $order,
                            $paymentIntent->id,
                            PaymentStatusEnum::FAILED->value,
                            [
                                'payment_method_type' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                                'status' => $paymentIntent->status,
                                'error' => $paymentIntent->last_payment_error ?? 'Payment failed'
                            ]
                        );
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Webhook handling failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Webhook handling failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
