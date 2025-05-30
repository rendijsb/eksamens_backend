<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\AddToCartRequest;
use App\Http\Requests\Carts\UpdateCartItemRequest;
use App\Http\Resources\Carts\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    public function getCart(Request $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $cart = $this->cartService->getCart($userId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json(['data' => new CartResource($cart)]);
    }

    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $cart = $this->cartService->getOrCreateCart($userId);
        $cartItem = $this->cartService->addItem($cart, $request->getProductId(), $request->getQuantity());

        if (!$cartItem) {
            return response()->json([
                'message' => 'Nepietiekams daudzums noliktavā vai produkts nav pieejams',
                'success' => false
            ], 400);
        }

        return response()->json([
            'data' => new CartResource($cart->fresh(['items.product'])),
            'success' => true
        ]);
    }

    public function updateCartItem(UpdateCartItemRequest $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $cart = $this->cartService->getOrCreateCart($userId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $quantity = $request->getQuantity();

        if ($quantity <= 0) {
            $this->cartService->removeItem($cart, $request->getItemId());
            return response()->json(['data' => new CartResource($cart->fresh(['items.product']))]);
        }

        $cartItem = $this->cartService->updateItemQuantity($cart, $request->getItemId(), $quantity);

        if (!$cartItem && $quantity > 0) {
            return response()->json([
                'message' => 'Nepietiekams daudzums noliktavā vai produkts nav pieejams',
                'success' => false
            ], 400);
        }

        return response()->json([
            'data' => new CartResource($cart->fresh(['items.product'])),
            'success' => true
        ]);
    }

    public function removeFromCart(int $itemId, Request $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $cart = $this->cartService->getOrCreateCart($userId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $removed = $this->cartService->removeItem($cart, $itemId);

        if (!$removed) {
            return response()->json(['message' => 'Failed to remove item from cart'], 400);
        }

        return response()->json(['data' => new CartResource($cart->fresh(['items.product']))]);
    }

    public function clearCart(Request $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $cart = $this->cartService->getOrCreateCart($userId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cleared = $this->cartService->clearCart($cart);

        if (!$cleared) {
            return response()->json(['message' => 'Failed to clear cart'], 400);
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
