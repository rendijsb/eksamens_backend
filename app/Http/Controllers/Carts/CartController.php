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
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        $cart = $this->cartService->getCart($userId, $sessionId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json(['data' => new CartResource($cart)]);
    }

    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);
        $cartItem = $this->cartService->addItem($cart, $request->getProductId(), $request->getQuantity());

        if (!$cartItem) {
            return response()->json(['message' => 'Failed to add item to cart'], 400);
        }

        $cartResource = new CartResource($cart->fresh(['items.product']));
        $response = response()->json(['data' => $cartResource]);

        if (!$userId && !$request->cookie('cart_session_id')) {
            $response->cookie('cart_session_id', $cart->getSessionId(), 60 * 24 * 30, '/', null, null, false, false, 'Lax');
        }

        return $response;
    }

    public function updateCartItem(UpdateCartItemRequest $request): JsonResponse
    {
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItem = $this->cartService->updateItemQuantity($cart, $request->getItemId(), $request->getQuantity());

        if ($request->getQuantity() <= 0) {
            return response()->json(['data' => new CartResource($cart->fresh(['items.product']))]);
        }

        if (!$cartItem) {
            return response()->json(['message' => 'Failed to update item in cart'], 400);
        }

        return response()->json(['data' => new CartResource($cart->fresh(['items.product']))]);
    }

    public function removeFromCart(int $itemId, Request $request): JsonResponse
    {
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);

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
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cleared = $this->cartService->clearCart($cart);

        if (!$cleared) {
            return response()->json(['message' => 'Failed to clear cart'], 400);
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    public function migrateCart(Request $request): JsonResponse
    {
        $userId = $request->user()?->getId();
        $sessionId = $request->cookie('cart_session_id');

        if (!$userId || !$sessionId) {
            return response()->json(['message' => 'User ID or session ID missing'], 400);
        }

        $cart = $this->cartService->migrateCart($sessionId, $userId);

        if (!$cart) {
            return response()->json(['message' => 'No cart to migrate'], 404);
        }

        $cartResource = new CartResource($cart->fresh(['items.product']));
        $response = response()->json(['data' => $cartResource]);
        $response->cookie('cart_session_id', '', -1);

        return $response;
    }
}
