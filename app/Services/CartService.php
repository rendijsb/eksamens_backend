<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(int $userId): Cart
    {
        $cart = Cart::where(Cart::USER_ID, $userId)->first();

        if ($cart) {
            return $cart;
        }

        return Cart::create([
            Cart::USER_ID => $userId,
        ]);
    }

    public function getCart(int $userId): ?Cart
    {
        return Cart::where(Cart::USER_ID, $userId)
            ->with('items.product')
            ->first();
    }

    public function addItem(Cart $cart, int $productId, int $quantity = 1): ?CartItem
    {
        $product = Product::find($productId);

        if (!$product || $product->getStock() < $quantity) {
            return null;
        }

        return DB::transaction(function () use ($cart, $product, $quantity) {
            $cartItem = CartItem::where(CartItem::CART_ID, $cart->getId())
                ->where(CartItem::PRODUCT_ID, $product->getId())
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->getQuantity() + $quantity;
                if ($newQuantity > $product->getStock()) {
                    $newQuantity = $product->getStock();
                }

                $cartItem->update([
                    CartItem::QUANTITY => $newQuantity,
                    CartItem::PRICE => $product->getPrice(),
                    CartItem::SALE_PRICE => $product->isSaleActive() ? $product->getSalePrice() : null,
                ]);
            } else {
                $safeQuantity = min($quantity, $product->getStock());

                $cartItem = CartItem::create([
                    CartItem::CART_ID => $cart->getId(),
                    CartItem::PRODUCT_ID => $product->getId(),
                    CartItem::QUANTITY => $safeQuantity,
                    CartItem::PRICE => $product->getPrice(),
                    CartItem::SALE_PRICE => $product->isSaleActive() ? $product->getSalePrice() : null,
                    CartItem::TOTAL_PRICE => $product->isSaleActive()
                        ? (float) $product->getSalePrice() * $safeQuantity
                        : (float) $product->getPrice() * $safeQuantity,
                ]);
            }

            return $cartItem;
        });
    }

    public function updateItemQuantity(Cart $cart, int $itemId, int $quantity): ?CartItem
    {
        $cartItem = CartItem::where(CartItem::ID, $itemId)
            ->where(CartItem::CART_ID, $cart->getId())
            ->first();

        if (!$cartItem) {
            return null;
        }

        $product = $cartItem->product;

        if (!$product || $product->getStock() < $quantity) {
            return null;
        }

        if ($quantity <= 0) {
            $cartItem->delete();
            return null;
        }

        $cartItem->update([
            CartItem::QUANTITY => $quantity,
        ]);

        return $cartItem;
    }

    public function removeItem(Cart $cart, int $itemId): bool
    {
        return DB::transaction(function() use ($cart, $itemId) {
            $item = CartItem::where(CartItem::ID, $itemId)
                ->where(CartItem::CART_ID, $cart->getId())
                ->first();

            if (!$item) {
                return true;
            }

            return (bool) $item->delete();
        });
    }

    public function clearCart(Cart $cart): bool
    {
        return DB::transaction(function() use ($cart) {
            return (bool) CartItem::where(CartItem::CART_ID, $cart->getId())->delete();
        });
    }
}
