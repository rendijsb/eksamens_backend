<?php

declare(strict_types=1);

namespace App\Http\Controllers\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupons\CreateCouponRequest;
use App\Http\Requests\Coupons\UpdateCouponRequest;
use App\Http\Requests\Coupons\GetAllCouponsRequest;
use App\Http\Requests\Coupons\GetCouponByIdRequest;
use App\Http\Requests\Coupons\DeleteCouponRequest;
use App\Http\Requests\Coupons\ValidateCouponRequest;
use App\Http\Resources\Coupons\CouponResource;
use App\Http\Resources\Coupons\CouponResourceCollection;
use App\Mail\CouponNotification;
use App\Models\Coupons\Coupon;
use App\Models\Users\User;
use App\Services\CouponService;
use App\Services\EmailDispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService        $couponService,
        private readonly EmailDispatchService $emailDispatchService
    )
    {
    }

    public function getAllCoupons(GetAllCouponsRequest $request): CouponResourceCollection
    {
        $query = Coupon::query();

        if ($request->getSearch()) {
            $searchTerm = $request->getSearch();
            $query->where(function ($q) use ($searchTerm) {
                $q->where(Coupon::CODE, 'like', "%{$searchTerm}%")
                    ->orWhere(Coupon::DESCRIPTION, 'like', "%{$searchTerm}%");
            });
        }

        if ($request->getType()) {
            $query->where(Coupon::TYPE, $request->getType());
        }

        if ($request->getStatus() !== null) {
            $query->where(Coupon::IS_ACTIVE, $request->getStatus());
        }

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        $query->orderBy($sortField, $sortDirection);

        $coupons = $query->paginate(10);

        return new CouponResourceCollection($coupons);
    }

    public function createCoupon(CreateCouponRequest $request): CouponResource|JsonResponse
    {
        try {
            $coupon = Coupon::create([
                Coupon::CODE => strtoupper($request->getCode()),
                Coupon::TYPE => $request->getType(),
                Coupon::VALUE => $request->getValue(),
                Coupon::MIN_ORDER_AMOUNT => $request->getMinOrderAmount(),
                Coupon::MAX_DISCOUNT_AMOUNT => $request->getMaxDiscountAmount(),
                Coupon::USES_PER_USER => $request->getUsesPerUser(),
                Coupon::TOTAL_USES => $request->getTotalUses(),
                Coupon::STARTS_AT => $request->getStartsAt(),
                Coupon::EXPIRES_AT => $request->getExpiresAt(),
                Coupon::IS_ACTIVE => $request->getIsActive(),
                Coupon::DESCRIPTION => $request->getDescription(),
            ]);

            if ($request->getIsActive()) {
                $this->sendCouponToAllUsers($coupon);
            }

            return new CouponResource($coupon);
        } catch (\Exception $e) {
            Log::error('Failed to create coupon: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCouponById(GetCouponByIdRequest $request): CouponResource
    {
        $coupon = Coupon::findOrFail($request->getCouponId());
        return new CouponResource($coupon);
    }

    public function updateCoupon(UpdateCouponRequest $request): CouponResource|JsonResponse
    {
        try {
            $coupon = Coupon::findOrFail($request->getCouponId());

            $coupon->update([
                Coupon::CODE => strtoupper($request->getCode()),
                Coupon::TYPE => $request->getType(),
                Coupon::VALUE => $request->getValue(),
                Coupon::MIN_ORDER_AMOUNT => $request->getMinOrderAmount(),
                Coupon::MAX_DISCOUNT_AMOUNT => $request->getMaxDiscountAmount(),
                Coupon::USES_PER_USER => $request->getUsesPerUser(),
                Coupon::TOTAL_USES => $request->getTotalUses(),
                Coupon::STARTS_AT => $request->getStartsAt(),
                Coupon::EXPIRES_AT => $request->getExpiresAt(),
                Coupon::IS_ACTIVE => $request->getIsActive(),
                Coupon::DESCRIPTION => $request->getDescription(),
            ]);

            return new CouponResource($coupon);
        } catch (\Exception $e) {
            Log::error('Failed to update coupon: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCoupon(DeleteCouponRequest $request): JsonResponse
    {
        try {
            $coupon = Coupon::findOrFail($request->getCouponId());

            if ($coupon->getUsedCount() > 0) {
                return response()->json([
                    'message' => 'Cannot delete coupon that has been used'
                ], 422);
            }

            $coupon->delete();

            return response()->json([], 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete coupon: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function validateCoupon(ValidateCouponRequest $request): JsonResponse
    {
        try {
            $code = $request->getCode();
            $orderAmount = $request->getOrderAmount();
            $userId = $request->user()->getId();

            $result = $this->couponService->validateCoupon($code, $orderAmount, $userId);

            if (!$result['valid']) {
                return response()->json([
                    'valid' => false,
                    'message' => $result['message']
                ], 422);
            }

            return response()->json([
                'valid' => true,
                'coupon' => new CouponResource($result['coupon']),
                'discount' => $result['discount']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to validate coupon: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Failed to validate coupon'
            ], 500);
        }
    }

    public function getActiveCoupons(Request $request): CouponResourceCollection
    {
        $coupons = Coupon::where(Coupon::IS_ACTIVE, true)
            ->where(Coupon::STARTS_AT, '<=', now())
            ->where(Coupon::EXPIRES_AT, '>=', now())
            ->orderBy(Coupon::CREATED_AT, 'desc')
            ->paginate(10);

        return new CouponResourceCollection($coupons);
    }

    public function sendCouponToAllUsers(Coupon $coupon): void
    {
        $users = User::where('role_id', '!=', 1)
            ->whereNotNull('email')
            ->get();

        foreach ($users as $user) {
            $this->emailDispatchService->sendEmail(
                $user->getEmail(),
                new CouponNotification($coupon, $user),
                $user,
                'promotional'
            );
        }
    }
}
