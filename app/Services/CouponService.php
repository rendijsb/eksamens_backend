<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupons\Coupon;
use App\Models\Coupons\CouponUsage;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function validateCoupon(string $code, float $orderAmount, int $userId): array
    {
        $coupon = Coupon::where(Coupon::CODE, strtoupper($code))->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Kupons nav atrasts'
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'valid' => false,
                'message' => 'Kupons nav aktīvs vai ir beidzies derības termiņš'
            ];
        }

        if ($coupon->isUsageLimitReached()) {
            return [
                'valid' => false,
                'message' => 'Kupona izmantošanas limits ir sasniegts'
            ];
        }

        if ($coupon->hasUserExceededLimit($userId)) {
            return [
                'valid' => false,
                'message' => 'Jūs jau esat izmantojis šo kuponu maksimālo skaitu reižu'
            ];
        }

        if (!$coupon->canBeAppliedToAmount($orderAmount)) {
            $minAmount = $coupon->getMinOrderAmount();
            return [
                'valid' => false,
                'message' => "Minimālā pasūtījuma summa šim kuponam ir €{$minAmount}"
            ];
        }

        $discount = $coupon->calculateDiscount($orderAmount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount
        ];
    }

    public function applyCouponToOrder(Order $order, string $couponCode, int $userId): array
    {
        return DB::transaction(function () use ($order, $couponCode, $userId) {
            $validation = $this->validateCoupon($couponCode, $order->getSubtotal(), $userId);

            if (!$validation['valid']) {
                return $validation;
            }

            $coupon = $validation['coupon'];
            $discount = $validation['discount'];

            $order->update([
                Order::COUPON_ID => $coupon->getId(),
                Order::COUPON_CODE => $coupon->getCode(),
                Order::COUPON_DISCOUNT => $discount,
            ]);

            CouponUsage::create([
                CouponUsage::COUPON_ID => $coupon->getId(),
                CouponUsage::USER_ID => $userId,
                CouponUsage::ORDER_ID => $order->getId(),
                CouponUsage::DISCOUNT_AMOUNT => $discount,
            ]);

            $coupon->increment(Coupon::USED_COUNT);

            return [
                'success' => true,
                'coupon' => $coupon,
                'discount' => $discount
            ];
        });
    }

    public function removeCouponFromOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            if (!$order->hasCoupon()) {
                return false;
            }

            $coupon = $order->coupon;

            CouponUsage::where(CouponUsage::ORDER_ID, $order->getId())->delete();

            $order->update([
                Order::COUPON_ID => null,
                Order::COUPON_CODE => null,
                Order::COUPON_DISCOUNT => 0,
            ]);

            if ($coupon) {
                $coupon->decrement(Coupon::USED_COUNT);
            }

            return true;
        });
    }

    public function getCouponUsageStats(int $couponId): array
    {
        $coupon = Coupon::findOrFail($couponId);

        $totalUsage = CouponUsage::where(CouponUsage::COUPON_ID, $couponId)->count();
        $totalDiscount = CouponUsage::where(CouponUsage::COUPON_ID, $couponId)
            ->sum(CouponUsage::DISCOUNT_AMOUNT);

        $uniqueUsers = CouponUsage::where(CouponUsage::COUPON_ID, $couponId)
            ->distinct()
            ->count(CouponUsage::USER_ID);

        return [
            'total_usage' => $totalUsage,
            'total_discount_given' => $totalDiscount,
            'unique_users' => $uniqueUsers,
            'remaining_uses' => $coupon->getTotalUses() ?
                max(0, $coupon->getTotalUses() - $totalUsage) : null,
        ];
    }
}
