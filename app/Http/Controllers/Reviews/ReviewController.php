<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\CreateReviewRequest;
use App\Http\Requests\Reviews\GetProductReviewsRequest;
use App\Http\Requests\Reviews\UpdateReviewStatusRequest;
use App\Http\Resources\Reviews\ReviewResource;
use App\Http\Resources\Reviews\ReviewResourceCollection;
use App\Models\Reviews\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function getProductReviews(GetProductReviewsRequest $request): ReviewResourceCollection
    {
        $productId = $request->getProductId();
        $query = Review::query()->where(Review::PRODUCT_ID, $productId);

        if (!$request->user() || !in_array($request->user()->relatedRole?->getName(), ['admin', 'moderator'])) {
            $query->where(Review::IS_APPROVED, true)
                ->Orwhere(Review::USER_ID, $request->user()->id);
        }

        $reviews = $query->with('user')->orderBy(Review::CREATED_AT, 'desc')->get();

        return new ReviewResourceCollection($reviews);
    }

    public function getUserReviews(Request $request): ReviewResourceCollection
    {
        $userId = Auth::id();
        $reviews = Review::where(Review::USER_ID, $userId)
            ->with(['user', 'product'])
            ->orderBy(Review::CREATED_AT, 'desc')
            ->get();

        return new ReviewResourceCollection($reviews);
    }

    public function createReview(CreateReviewRequest $request): ReviewResource|JsonResponse
    {
        $existingReview = Review::where(Review::PRODUCT_ID, $request->getProductId())
            ->where(Review::USER_ID, Auth::id())
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Jūs jau esat novērtējis šo produktu'], 422);
        }

        $review = Review::create([
            Review::PRODUCT_ID => $request->getProductId(),
            Review::USER_ID => Auth::id(),
            Review::RATING => $request->getRating(),
            Review::REVIEW_TEXT => $request->getReviewText(),
            Review::IS_APPROVED => in_array($request->user()->relatedRole?->getName(), ['admin', 'moderator']),
        ]);

        return new ReviewResource($review);
    }

    public function getAllReviews(): ReviewResourceCollection
    {
        $reviews = Review::with(['user', 'product'])
            ->orderBy(Review::CREATED_AT, 'desc')
            ->get();

        return new ReviewResourceCollection($reviews);
    }

    public function getPendingReviews(): ReviewResourceCollection
    {
        $reviews = Review::where(Review::IS_APPROVED, false)
            ->with(['user', 'product'])
            ->orderBy(Review::CREATED_AT, 'desc')
            ->get();

        return new ReviewResourceCollection($reviews);
    }

    public function updateReviewStatus(UpdateReviewStatusRequest $request): ReviewResource
    {
        $review = Review::findOrFail($request->getReviewId());

        $review->update([
            Review::IS_APPROVED => $request->getIsApproved(),
        ]);

        return new ReviewResource($review->fresh(['user', 'product']));
    }

    public function deleteReview(int $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);

        $review->delete();

        return response()->json([], 204);
    }
}
