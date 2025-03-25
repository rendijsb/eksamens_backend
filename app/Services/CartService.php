<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateCart(?int $userId, ?string $sessionId): Cart
    {
        if ($userId) {
            $cart = Cart::where(Cart::USER_ID, $userId)->first();

            if ($cart) {
                return $cart;
            }

            if ($sessionId) {
                $sessionCart = Cart::where(Cart::SESSION_ID, $sessionId)->first();

                if ($sessionCart) {
                    $sessionCart->update([Cart::USER_ID => $userId]);
                    return $sessionCart;
                }
            }
        } elseif ($sessionId) {
            $cart = Cart::where(Cart::SESSION_ID, $sessionId)->first();

            if ($cart) {
                return $cart;
            }
        }

        $newSessionId = $sessionId ?? Str::uuid()->toString();

        return Cart::create([
            Cart::USER_ID => $userId,
            Cart::SESSION_ID => $newSessionId,
        ]);
    }

    public function getCart(?int $userId, ?string $sessionId): ?Cart
    {
        $query = Cart::query();

        if ($userId) {
            $query->where(Cart::USER_ID, $userId);
        } elseif ($sessionId) {
            $query->where(Cart::SESSION_ID, $sessionId);
        } else {
            return null;
        }

        return $query->with('items.product')->first();
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
                $cartItem = CartItem::create([
                    CartItem::CART_ID => $cart->getId(),
                    CartItem::PRODUCT_ID => $product->getId(),
                    CartItem::QUANTITY => $quantity,
                    CartItem::PRICE => $product->getPrice(),
                    CartItem::SALE_PRICE => $product->isSaleActive() ? $product->getSalePrice() : null,
                    CartItem::TOTAL_PRICE => $product->isSaleActive()
                        ? (float) $product->getSalePrice() * $quantity
                        : (float) $product->getPrice() * $quantity,
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

    public function migrateCart(string $sessionId, int $userId): ?Cart
    {
        $sessionCart = Cart::where(Cart::SESSION_ID, $sessionId)->first();

        if (!$sessionCart) {
            return null;
        }

        $userCart = Cart::where(Cart::USER_ID, $userId)->first();

        if (!$userCart) {
            $sessionCart->update([Cart::USER_ID => $userId]);
            return $sessionCart;
        }

        return DB::transaction(function () use ($sessionCart, $userCart) {
            foreach ($sessionCart->items as $sessionItem) {
                $userItem = CartItem::where(CartItem::CART_ID, $userCart->getId())
                    ->where(CartItem::PRODUCT_ID, $sessionItem->getProductId())
                    ->first();

                if ($userItem) {
                    $product = $sessionItem->product;
                    $newQuantity = $userItem->getQuantity() + $sessionItem->getQuantity();

                    if ($product && $newQuantity > $product->getStock()) {
                        $newQuantity = $product->getStock();
                    }

                    $userItem->update([
                        CartItem::QUANTITY => $newQuantity,
                    ]);

                    $sessionItem->delete();
                } else {
                    $sessionItem->update([CartItem::CART_ID => $userCart->getId()]);
                }
            }

            $sessionCart->delete();

            return $userCart;
        });
    }
}
