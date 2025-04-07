<?php

declare(strict_types=1);

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\Orders\OrderResource;
use App\Http\Resources\Orders\OrderResourceCollection;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getUserOrders(Request $request): OrderResourceCollection|JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = (int) $request->input('per_page', 10);

            $ordersData = $this->orderService->getUserOrders($user, $perPage);

            return new OrderResourceCollection($ordersData['orders']);
        } catch (\Exception $e) {
            Log::error('Failed to get user orders: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGuestOrders(Request $request): OrderResourceCollection|JsonResponse
    {
        try {
            $token = $request->cookie('cart_session_id');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guest token not found'
                ], 400);
            }

            $perPage = (int) $request->input('per_page', 10);
            $ordersData = $this->orderService->getGuestOrders($token, $perPage);

            return new OrderResourceCollection($ordersData['orders']);
        } catch (\Exception $e) {
            Log::error('Failed to get guest orders: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderById(int $orderId, Request $request): OrderResource|JsonResponse
    {
        try {
            $user = $request->user();
            $order = $this->orderService->getOrderById($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($user && $order->getUserId() !== $user->getId() && !$user->relatedRole?->getName() == 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this order'
                ], 403);
            }

            if (!$user && $order->getGuestToken() !== $request->cookie('cart_session_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this order'
                ], 403);
            }

            return new OrderResource($order);
        } catch (\Exception $e) {
            Log::error('Failed to get order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderByNumber(string $orderNumber, Request $request): OrderResource|JsonResponse
    {
        try {
            $user = $request->user();
            $order = $this->orderService->getOrderByNumber($orderNumber);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($user && $order->getUserId() !== $user->getId() && !$user->relatedRole?->getName() == 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this order'
                ], 403);
            }

            if (!$user && $order->getGuestToken() !== $request->cookie('cart_session_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this order'
                ], 403);
            }

            return new OrderResource($order);
        } catch (\Exception $e) {
            Log::error('Failed to get order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelOrder(int $orderId, Request $request): OrderResource|JsonResponse
    {
        try {
            $user = $request->user();
            $order = $this->orderService->getOrderById($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($user && $order->getUserId() !== $user->getId() && !$user->relatedRole?->getName() == 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this order'
                ], 403);
            }

            if (!$user && $order->getGuestToken() !== $request->cookie('cart_session_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this order'
                ], 403);
            }

            $cancelledOrder = $this->orderService->cancelOrder($order);

            return new OrderResource($cancelledOrder);
        } catch (\Exception $e) {
            Log::error('Failed to cancel order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }
}
