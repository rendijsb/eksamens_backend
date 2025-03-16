<?php

declare(strict_types=1);

namespace App\Http\Controllers\Products;

use App\Enums\Products\ProductEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\DeleteProductRequest;
use App\Http\Requests\Products\EditProductRequest;
use App\Http\Requests\Products\GetAllProductsRequest;
use App\Http\Requests\Products\GetProductByIdRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Products\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function getAllProducts(GetAllProductsRequest $request): ProductResourceCollection
    {
        $query = Product::query();

        if ($request->getSearch()) {
            $searchTerm = $request->getSearch();
            $query->where(function($q) use ($searchTerm) {
                $q->where(Product::NAME, 'like', "%{$searchTerm}%")
                    ->orWhere(Product::DESCRIPTION, 'like', "%{$searchTerm}%")
                    ->orWhere(Product::SLUG, 'like', "%{$searchTerm}%");
            });
        }

        if ($request->getCategoryId()) {
            $query->where(Product::CATEGORY_ID, $request->getCategoryId());
        }

        if ($request->getStatus()) {
            $query->where(Product::STATUS, $request->getStatus());
        }

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(15);

        return new ProductResourceCollection($products);
    }

    public function createProduct(CreateProductRequest $request): ProductResource|JsonResponse
    {
        $slugValue = Str::kebab(Str::squish($request->getName()));

        if (Product::where(Product::SLUG, $slugValue)->exists()) {
            return response()->json(['message' => 'Product with this name already exists'], 422);
        }

        $product = Product::create([
            Product::CATEGORY_ID => $request->getCategoryId(),
            Product::NAME => $request->getName(),
            Product::DESCRIPTION => $request->getDescription(),
            Product::SLUG => $slugValue,
            Product::PRICE => $request->getPrice(),
            Product::SALE_PRICE => $request->getSalePrice(),
            Product::STOCK => $request->getStock(),
            Product::SPECIFICATIONS => $request->getSpecifications(),
            Product::ADDITIONAL_INFO => $request->getAdditionalInfo(),
            Product::STATUS => $request->getStatus() ?: ProductEnum::INACTIVE->value,
        ]);

        return new ProductResource($product);
    }

    public function getProductById(GetProductByIdRequest $request): ProductResource
    {
        $product = Product::findOrFail(
            $request->getProductId()
        );

        return new ProductResource($product);
    }

    public function editProduct(EditProductRequest $request): ProductResource|JsonResponse
    {
        $product = Product::findOrFail(
            $request->getProductId()
        );

        $slugValue = Str::kebab(Str::squish($request->getName()));

        $slugExists = Product::where(Product::SLUG, $slugValue)
            ->where('id', '!=', $product->getId())
            ->exists();

        if ($slugExists) {
            return response()->json(['message' => 'Product with this name already exists'], 422);
        }

        $product->update([
            Product::CATEGORY_ID => $request->getCategoryId(),
            Product::NAME => $request->getName(),
            Product::DESCRIPTION => $request->getDescription(),
            Product::SLUG => $slugValue,
            Product::PRICE => $request->getPrice(),
            Product::SALE_PRICE => $request->getSalePrice(),
            Product::STOCK => $request->getStock(),
            Product::SPECIFICATIONS => $request->getSpecifications(),
            Product::ADDITIONAL_INFO => $request->getAdditionalInfo(),
            Product::STATUS => $request->getStatus(),
        ]);

        return new ProductResource($product);
    }

    public function deleteProduct(DeleteProductRequest $request): JsonResponse
    {
        $product = Product::findOrFail(
            $request->getProductId()
        );

        $product->delete();

        return new JsonResponse([], 204);
    }

    public function getAllPopularActiveProducts(): ProductResourceCollection
    {
        $query = Product::query();

        $products = $query->where(Product::STATUS, ProductEnum::ACTIVE->value)
            ->orderBy(Product::SOLD, 'desc')
            ->paginate(15);

        return new ProductResourceCollection($products);
    }
}
