<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Orders\PaymentTransaction;
use App\Models\Products\Product;
use App\Models\Users\Address;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createOrderFromCart(Cart $cart, array $orderData): Order
    {
        return DB::transaction(function () use ($cart, $orderData) {
            $insufficientStockItems = [];
            foreach ($cart->items as $cartItem) {
                $product = Product::find($cartItem->getProductId());
                if (!$product || $product->getStock() < $cartItem->getQuantity()) {
                    $insufficientStockItems[] = [
                        'product_name' => $product ? $product->getName() : 'Unknown Product',
                        'requested_quantity' => $cartItem->getQuantity(),
                        'available_stock' => $product ? $product->getStock() : 0
                    ];
                }
            }

            if (!empty($insufficientStockItems)) {
                $errorMessages = [];
                foreach ($insufficientStockItems as $item) {
                    $errorMessages[] = "Nepietiek noliktavā: {$item['product_name']} - Pieprasīts: {$item['requested_quantity']}, Pieejams: {$item['available_stock']}";
                }
                throw new Exception(implode("\n", $errorMessages));
            }

            $orderNumber = $this->generateOrderNumber();

            $shippingAddressDetails = $this->formatAddressDetails($orderData['shipping_address']);
            $billingAddressDetails = $orderData['same_billing_address']
                ? $shippingAddressDetails
                : $this->formatAddressDetails($orderData['billing_address']);

            $order = Order::create([
                Order::USER_ID => $cart->getUserId(),
                Order::ORDER_NUMBER => $orderNumber,
                Order::TOTAL_AMOUNT => $cart->getTotalPrice(),
                Order::STATUS => OrderStatusEnum::STATUS_PENDING->value,
                Order::PAYMENT_METHOD => $orderData['payment_method'],
                Order::PAYMENT_STATUS => PaymentStatusEnum::PENDING->value,
                Order::SHIPPING_ADDRESS_ID => $orderData['shipping_address_id'] ?? null,
                Order::BILLING_ADDRESS_ID => $orderData['billing_address_id'] ?? null,
                Order::CUSTOMER_NAME => $orderData['customer_name'],
                Order::CUSTOMER_EMAIL => $orderData['customer_email'],
                Order::CUSTOMER_PHONE => $orderData['customer_phone'] ?? null,
                Order::SHIPPING_ADDRESS_DETAILS => $shippingAddressDetails,
                Order::BILLING_ADDRESS_DETAILS => $billingAddressDetails,
                Order::NOTES => $orderData['notes'] ?? null,
            ]);

            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    OrderItem::ORDER_ID => $order->getId(),
                    OrderItem::PRODUCT_ID => $cartItem->getProductId(),
                    OrderItem::PRODUCT_NAME => $cartItem->product->getName(),
                    OrderItem::PRODUCT_PRICE => $cartItem->getPrice(),
                    OrderItem::PRODUCT_SALE_PRICE => $cartItem->getSalePrice(),
                    OrderItem::QUANTITY => $cartItem->getQuantity(),
                    OrderItem::TOTAL_PRICE => $cartItem->getTotalPrice(),
                ]);
            }

            return $order;
        });
    }

    public function updateOrderPayment(
        Order $order,
        string $transactionId,
        string $status,
        array $paymentDetails = []
    ): Order {
        return DB::transaction(function () use ($order, $transactionId, $status, $paymentDetails) {
            $order->update([
                Order::TRANSACTION_ID => $transactionId,
                Order::PAYMENT_STATUS => $status,
                Order::STATUS => $status === PaymentStatusEnum::PAID->value
                    ? OrderStatusEnum::STATUS_PROCESSING->value
                    : ($status === PaymentStatusEnum::FAILED->value
                        ? OrderStatusEnum::STATUS_FAILED->value
                        : $order->getStatus()),
            ]);

            PaymentTransaction::create([
                PaymentTransaction::ORDER_ID => $order->getId(),
                PaymentTransaction::TRANSACTION_ID => $transactionId,
                PaymentTransaction::AMOUNT => $order->getTotalAmount(),
                PaymentTransaction::PAYMENT_METHOD => $order->getPaymentMethod(),
                PaymentTransaction::STATUS => $status,
                PaymentTransaction::PAYMENT_DETAILS => $paymentDetails,
            ]);

            if ($status === PaymentStatusEnum::PAID->value) {
                $user = User::find($order->getUserId());
                if ($user) {
                    $cart = Cart::where(Cart::USER_ID, $user->getId())->first();
                    if ($cart) {
                        CartItem::where(CartItem::CART_ID, $cart->getId())->delete();
                    }
                }

                $this->updateProductInventory($order);
            }

            return $order;
        });
    }

    public function getOrderById(int $orderId): ?Order
    {
        return Order::with(['items', 'transactions', 'shippingAddress', 'billingAddress'])->find($orderId);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::with(['items', 'transactions'])
            ->where(Order::ORDER_NUMBER, $orderNumber)
            ->first();
    }

    public function getUserOrders(User $user, int $perPage = 10): array
    {
        $orders = Order::with(['items'])
            ->where(Order::USER_ID, $user->getId())
            ->orderBy(Order::CREATED_AT, 'desc')
            ->paginate($perPage);

        return [
            'orders' => $orders->items(),
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'per_page' => $orders->perPage(),
            'last_page' => $orders->lastPage(),
        ];
    }

    public function cancelOrder(Order $order): Order
    {
        if ($order->getStatus() === OrderStatusEnum::STATUS_PENDING->value ||
            $order->getStatus() === OrderStatusEnum::STATUS_PROCESSING->value) {

            $order->update([
                Order::STATUS => OrderStatusEnum::STATUS_CANCELLED->value
            ]);
        }

        return $order;
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $timestamp . '-' . $random;
    }

    private function formatAddressDetails($address): string
    {
        if ($address instanceof Address) {
            return json_encode([
                'name' => $address->getName(),
                'phone' => $address->getPhone(),
                'street_address' => $address->getStreetAddress(),
                'apartment' => $address->getApartment(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'postal_code' => $address->getPostalCode(),
                'country' => $address->getCountry(),
            ]);
        }

        return json_encode($address);
    }

    private function updateProductInventory(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $newStock = max(0, $product->getStock() - $item->getQuantity());
                $product->update([
                    'stock' => $newStock,
                    'sold' => $product->getSold() + $item->getQuantity()
                ]);
            }
        }
    }
}
