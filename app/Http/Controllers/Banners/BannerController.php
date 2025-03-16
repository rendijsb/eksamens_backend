<?php

declare(strict_types=1);

namespace App\Http\Controllers\Banners;

use App\Enums\Images\ImageTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Images\ImageController;
use App\Http\Requests\Banners\CreateBannerRequest;
use App\Http\Requests\Banners\DeleteBannerRequest;
use App\Http\Requests\Banners\EditBannerRequest;
use App\Http\Requests\Banners\GetAllBannersRequest;
use App\Http\Requests\Banners\GetBannerByIdRequest;
use App\Http\Resources\Banners\BannerResource;
use App\Http\Resources\Banners\BannerResourceCollection;
use App\Models\Banners\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function __construct(
        private readonly ImageController $imageController,
    )
    {
    }

    public function getAllBanners(GetAllBannersRequest $request): BannerResourceCollection
    {
        $query = Banner::query();

        if ($request->getSearch()) {
            $searchTerm = $request->getSearch();
            $query->where(function($q) use ($searchTerm) {
                $q->where(Banner::BUTTON_LINK, 'like', "%{$searchTerm}%")
                    ->orWhere(Banner::BUTTON_TEXT, 'like', "%{$searchTerm}%")
                    ->orWhere(Banner::SUBTITLE, 'like', "%{$searchTerm}%")
                    ->orWhere(Banner::TITLE, 'like', "%{$searchTerm}%");
            });
        }

        if ($request->getStatus() !== null) {
            $query->where(Banner::IS_ACTIVE, $request->getStatus());
        }

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        $query->orderBy($sortField, $sortDirection);

        $banners = $query->paginate(10);

        return new BannerResourceCollection($banners);
    }

    public function createBanner(CreateBannerRequest $request): BannerResource
    {
        $banner = Banner::create([
            Banner::TITLE => $request->getTitle(),
            Banner::SUBTITLE => $request->getSubtitle(),
            Banner::IS_ACTIVE => $request->getIsActive(),
            Banner::BUTTON_TEXT => $request->getButtonText(),
            Banner::BUTTON_LINK => $request->getButtonLink(),
        ]);

        $this->imageController->handleSingleImageUpload(
            relatedId: $banner->getId(),
            imageFile: $request->getImage(),
            type: ImageTypeEnum::BANNER->value
        );

        return new BannerResource($banner);
    }

    public function getBannerById(GetBannerByIdRequest $request): BannerResource
    {
        $banner = Banner::findOrFail(
            $request->getBannerId()
        );

        return new BannerResource($banner);
    }

    public function editBanner(EditBannerRequest $request): BannerResource
    {
        $banner = Banner::findOrFail(
            $request->getBannerId()
        );

        $banner->update([
            Banner::TITLE => $request->getTitle(),
            Banner::SUBTITLE => $request->getSubtitle(),
            Banner::IS_ACTIVE => $request->getIsActive(),
            Banner::BUTTON_TEXT => $request->getButtonText(),
            Banner::BUTTON_LINK => $request->getButtonLink(),
        ]);

        if ($request->getImage()) {
        $this->imageController->handleSingleImageUpload(
            relatedId: $banner->getId(),
            imageFile: $request->getImage(),
            type: ImageTypeEnum::BANNER->value
        );
        }

        return new BannerResource($banner);
    }

    public function deleteBanner(DeleteBannerRequest $request): JsonResponse
    {
        $banner = Banner::findOrFail(
            $request->getBannerId()
        );

        $banner->delete();

        return new JsonResponse([], 204);
    }

    public function getAllActiveBanners(): BannerResourceCollection
    {
        $query = Banner::query();

        $banners = $query->where(Banner::IS_ACTIVE, true)
            ->paginate(10);

        return new BannerResourceCollection($banners);
    }
}
