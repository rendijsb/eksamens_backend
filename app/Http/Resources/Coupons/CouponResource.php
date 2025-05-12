<?php

declare(strict_types=1);

namespace App\Http\Resources\Coupons;

use App\Models\Coupons\Coupon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public $resource = Coupon::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'code' => $this->resource->getCode(),
            'type' => $this->resource->getType(),
            'value' => $this->resource->getValue(),
            'min_order_amount' => $this->resource->getMinOrderAmount(),
            'max_discount_amount' => $this->resource->getMaxDiscountAmount(),
            'uses_per_user' => $this->resource->getUsesPerUser(),
            'total_uses' => $this->resource->getTotalUses(),
            'used_count' => $this->resource->getUsedCount(),
            'remaining_uses' => $this->resource->getTotalUses() ?
                max(0, $this->resource->getTotalUses() - $this->resource->getUsedCount()) : null,
            'starts_at' => $this->resource->getStartsAt()->format('Y-m-d H:i:s'),
            'expires_at' => $this->resource->getExpiresAt()->format('Y-m-d H:i:s'),
            'is_active' => $this->resource->getIsActive(),
            'is_valid' => $this->resource->isValid(),
            'is_expired' => $this->resource->getExpiresAt()->isPast(),
            'description' => $this->resource->getDescription(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'usage_stats' => $this->when($this->isAdminRequest(), function () {
                return [
                    'unique_users' => $this->resource->users()->count(),
                    'total_discount_given' => $this->resource->couponUsages()->sum('discount_amount'),
                ];
            }),
        ];
    }

    private function isAdminRequest(): bool
    {
        $user = request()->user();

        if (!$user || !$user->relatedRole) {
            return false;
        }

        return in_array($user->relatedRole->getName(), ['admin', 'moderator']);
    }
}
