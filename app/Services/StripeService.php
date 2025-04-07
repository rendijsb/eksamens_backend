<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Orders\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService
{
    private StripeClient $stripeClient;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripeClient = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * @throws ApiErrorException
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $amount = (int)($order->getTotalAmount() * 100);

            $paymentIntent = $this->stripeClient->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'order_id' => $order->getId(),
                    'order_number' => $order->getOrderNumber(),
                ],
            ]);

            return [
                'clientSecret' => $paymentIntent->client_secret,
                'id' => $paymentIntent->id,
            ];
        } catch (Exception $e) {
            Log::error('Stripe payment intent creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->stripeClient->paymentIntents->retrieve($paymentIntentId);
        } catch (Exception $e) {
            Log::error('Stripe payment intent retrieval failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function handleWebhookEvent(string $payload, string $sigHeader): array
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $webhookSecret
            );

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    return [
                        'success' => true,
                        'type' => $event->type,
                        'paymentIntent' => $paymentIntent,
                    ];

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    return [
                        'success' => false,
                        'type' => $event->type,
                        'paymentIntent' => $paymentIntent,
                    ];

                default:
                    return [
                        'success' => true,
                        'type' => $event->type,
                    ];
            }
        } catch (Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
