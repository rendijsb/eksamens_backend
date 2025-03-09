<?php

declare(strict_types=1);

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\DeleteProductImageRequest;
use App\Http\Requests\Products\GetProductImagesRequest;
use App\Http\Requests\Products\SetPrimaryImageRequest;
use App\Http\Requests\Products\UploadProductImagesRequest;
use App\Http\Resources\Products\ProductImageResource;
use App\Http\Resources\Products\ProductImageResourceCollection;
use App\Models\Products\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    public function getProductImages(GetProductImagesRequest $request): ProductImageResourceCollection
    {
        $productId = $request->getProductId();

        $images = ProductImage::where(ProductImage::PRODUCT_ID, $productId)
            ->orderBy(ProductImage::IS_PRIMARY, 'desc')
            ->get();

        return new ProductImageResourceCollection($images);
    }

    public function uploadImages(UploadProductImagesRequest $request): ProductImageResourceCollection
    {
        $productId = $request->getProductId();
        $newImages = [];

        $isPrimaryRequired = !ProductImage::where(ProductImage::PRODUCT_ID, $productId)
            ->where(ProductImage::IS_PRIMARY, true)
            ->exists();

        foreach ($request->getImages() as $index => $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/products', $filename);

            $isPrimary = $isPrimaryRequired && $index === 0;

            $productImage = ProductImage::create([
                ProductImage::PRODUCT_ID => $productId,
                ProductImage::IMAGE => $filename,
                ProductImage::IS_PRIMARY => $isPrimary
            ]);

            $newImages[] = $productImage;
        }

        return new ProductImageResourceCollection(collect($newImages));
    }

    public function setPrimaryImage(SetPrimaryImageRequest $request): ProductImageResource
    {
        $imageId = $request->getImageId();
        $image = ProductImage::findOrFail($imageId);

        ProductImage::where(ProductImage::PRODUCT_ID, $image->getProductId())
            ->update([ProductImage::IS_PRIMARY => false]);

        $image->update([ProductImage::IS_PRIMARY => true]);

        return new ProductImageResource($image);
    }

    public function deleteImage(DeleteProductImageRequest $request): JsonResponse
    {
        $imageId = $request->getImageId();
        $image = ProductImage::findOrFail($imageId);
        $productId = $image->getProductId();
        $isPrimary = $image->getIsPrimary();

        Storage::delete('public/products/' . $image->getImage());

        $image->delete();

        if ($isPrimary) {
            $nextImage = ProductImage::where(ProductImage::PRODUCT_ID, $productId)->first();
            if ($nextImage) {
                $nextImage->update([ProductImage::IS_PRIMARY => true]);
            }
        }

        return response()->json([
            'message' => 'Image deleted successfully'
        ], 204);
    }
}
