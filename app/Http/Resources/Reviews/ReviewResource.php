<?php

declare(strict_types=1);

namespace App\Http\Resources\Reviews;

use App\Models\Reviews\Review;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public $resource = Review::class;

    public function toArray($request): array
    {
        if (!$this->resource->relationLoaded('user')) {
            $this->resource->load('user');
        }

        $user = $this->resource->user;

        return [
            'id' => $this->resource->getId(),
            'product_id' => $this->resource->getProductId(),
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->resource->product->getId(),
                    'name' => $this->resource->product->getName(),
                    'slug' => $this->resource->product->getSlug(),
                ];
            }),
            'user_id' => $this->resource->getUserId(),
            'user' => [
                'id' => $user ? $user->getId() : $this->resource->getUserId(),
                'name' => $user ? $user->getName() : 'Anonymous',
                'profile_image' => $user && $user->getProfileImage()
                    ? url('/profile/image/' . $user->getProfileImage())
                    : null,
            ],
            'rating' => $this->resource->getRating(),
            'review_text' => $this->resource->getReviewText(),
            'is_approved' => $this->resource->getIsApproved(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
