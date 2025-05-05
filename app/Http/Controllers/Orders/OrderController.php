<?php

declare(strict_types=1);

namespace App\Http\Controllers\Orders;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Orders\OrderResource;
use App\Http\Resources\Orders\OrderResourceCollection;
use App\Models\Orders\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            $perPage = (int)$request->input('per_page', 10);

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

            if ($order->getUserId() !== $user->getId() && !$user->relatedRole?->getName() == 'admin') {
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

            if ($order->getUserId() !== $user->getId() && !$user->relatedRole?->getName() == 'admin') {
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

            if ($order->getUserId() !== $user->getId() && $user->relatedRole?->getName() != 'admin') {
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

    public function getAllOrders(Request $request): OrderResourceCollection
    {
        $query = Order::query();

        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_name', 'like', "%{$searchTerm}%")
                    ->orWhere('customer_email', 'like', "%{$searchTerm}%")
                    ->orWhere('transaction_id', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $query->with(['items.product']);

        $orders = $query->paginate(10);

        return new OrderResourceCollection($orders);
    }

    public function updateOrderStatus(int $orderId, Request $request): OrderResource|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,processing,completed,cancelled,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::with(['items.product'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $newStatus = $request->input('status');
        $currentStatus = $order->getStatus()->value;

        Log::info('Order status change attempt', [
            'order_id' => $orderId,
            'current_status' => $currentStatus,
            'new_status' => $newStatus
        ]);

        if ($currentStatus === OrderStatusEnum::STATUS_CANCELLED->value &&
            $newStatus !== OrderStatusEnum::STATUS_CANCELLED->value) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status of cancelled order'
            ], 422);
        }

        if ($currentStatus === OrderStatusEnum::STATUS_COMPLETED->value &&
            $newStatus !== OrderStatusEnum::STATUS_COMPLETED->value) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status of completed order'
            ], 422);
        }

        if ($newStatus === OrderStatusEnum::STATUS_COMPLETED->value &&
            $order->getPaymentStatus()->value !== PaymentStatusEnum::PAID->value) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark as completed when payment is not successful'
            ], 422);
        }

        if ($currentStatus === OrderStatusEnum::STATUS_PROCESSING->value &&
            $newStatus === OrderStatusEnum::STATUS_PENDING->value) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot revert order from processing to pending'
            ], 422);
        }
        if ($newStatus === OrderStatusEnum::STATUS_CANCELLED->value) {
            $updatedOrder = $this->orderService->cancelOrder($order);
        } else {
            $order->update([
                'status' => $newStatus
            ]);

            $updatedOrder = $order->fresh(['items.product']);
        }

        return new OrderResource($updatedOrder);
    }
}
