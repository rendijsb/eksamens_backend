<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wishlists;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Wishlists\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function getWishlist(Request $request): ProductResourceCollection
    {
        $userId = $request->user()->getId();

        $wishlistItems = WishlistItem::where(WishlistItem::USER_ID, $userId)
            ->with('product')
            ->get();

        $products = $wishlistItems->map(function ($item) {
            return $item->product;
        })->filter(function ($product) {
            return $product !== null;
        });

        return new ProductResourceCollection($products);
    }

    public function addToWishlist(Request $request): JsonResponse
    {
        $userId = $request->user()->getId();
        $productId = $request->input('product_id');

        $wishlistItem = WishlistItem::firstOrCreate([
            WishlistItem::USER_ID => $userId,
            WishlistItem::PRODUCT_ID => $productId,
        ]);

        return response()->json([
            'message' => 'Product added to wishlist',
            'success' => true
        ]);
    }

    public function removeFromWishlist(Request $request, int $productId): JsonResponse
    {
        $userId = $request->user()->getId();

        $deleted = WishlistItem::where([
            WishlistItem::USER_ID => $userId,
            WishlistItem::PRODUCT_ID => $productId
        ])->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Product removed from wishlist',
                'success' => true
            ]);
        }

        return response()->json([
            'message' => 'Product not found in wishlist',
            'success' => false
        ], 404);
    }

    public function checkInWishlist(Request $request, int $productId): JsonResponse
    {
        $userId = $request->user()->getId();

        $exists = WishlistItem::where([
            WishlistItem::USER_ID => $userId,
            WishlistItem::PRODUCT_ID => $productId
        ])->exists();

        return response()->json([
            'in_wishlist' => $exists
        ]);
    }

    public function clearWishlist(Request $request): JsonResponse
    {
        $userId = $request->user()->getId();

        WishlistItem::where(WishlistItem::USER_ID, $userId)->delete();

        return response()->json([
            'message' => 'Wishlist cleared',
            'success' => true
        ]);
    }
}
