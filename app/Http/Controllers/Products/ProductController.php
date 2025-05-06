<?php

declare(strict_types=1);

namespace App\Http\Controllers\Products;

use App\Enums\Products\ProductEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\DeleteProductRequest;
use App\Http\Requests\Products\EditProductRequest;
use App\Http\Requests\Products\GetAllProductsRequest;
use App\Http\Requests\Products\GetAllSearchableProductsRequest;
use App\Http\Requests\Products\GetProductByIdRequest;
use App\Http\Requests\Products\GetProductBySlugRequest;
use App\Http\Requests\Products\GetRelatedProductsRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $products = $query->paginate(10);

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
            Product::SALE_ENDS_AT => $request->getSaleEndsAt(),
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
            Product::SALE_ENDS_AT => $request->getSaleEndsAt(),
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
            ->where(Product::STOCK, '>', 0)
            ->orderBy(Product::SOLD, 'desc')
            ->paginate(10);

        return new ProductResourceCollection($products);
    }

    public function getAllSearchableProducts(GetAllSearchableProductsRequest $request): ProductResourceCollection
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

        if ($request->getMinPrice() !== null) {
            $query->where(Product::PRICE, '>=', $request->getMinPrice());
        }

        if ($request->getMaxPrice() !== null) {
            $query->where(Product::PRICE, '<=', $request->getMaxPrice());
        }

        if ($request->has('in_stock')) {
            if ($request->get('in_stock')) {
                $query->where(Product::STOCK, '>', 0);
            }
        }

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        $query->orderBy($sortField, $sortDirection);

        $query->where(Product::STATUS, ProductEnum::ACTIVE->value);

        $products = $query->paginate($request->getPerPage());

        return new ProductResourceCollection($products);
    }

    public function getProductBySlug(GetProductBySlugRequest $request): ProductResource|JsonResponse
    {
        $product = Product::where(Product::SLUG, $request->getSlug())->first();

        if ($product->getStatus() !== ProductEnum::ACTIVE->value) {
            return response()->json(['message' => 'Product with this slug is not active'], 422);
        }

        return new ProductResource($product);
    }

    public function getRelatedProducts(GetRelatedProductsRequest $request): ProductResourceCollection
    {
        $query = Product::query();

        $query->where(Product::CATEGORY_ID, $request->getCategoryId());

        if ($request->getExcludeId()) {
            $query->where(Product::ID, '!=', $request->getExcludeId());
        }

        $query->where(Product::STATUS, ProductEnum::ACTIVE->value);

        $query->orderBy(Product::SOLD, 'desc')
            ->orderBy(Product::CREATED_AT, 'desc');

        $products = $query->limit($request->getLimit())->get();

        return new ProductResourceCollection($products);
    }

    public function getSaleProducts(Request $request): ProductResourceCollection
    {
        $query = Product::query();

        $query->where(Product::STATUS, ProductEnum::ACTIVE->value);

        $query->whereNotNull(Product::SALE_PRICE)
            ->where(function($q) {
                $q->whereNull(Product::SALE_ENDS_AT)
                    ->orWhere(Product::SALE_ENDS_AT, '>', Carbon::now());
            });

        if ($request->has('per_page')) {
            $perPage = (int) $request->get('per_page');
        } else {
            $perPage = 12;
        }

        if ($request->has('sort_by')) {
            $sortField = $request->get('sort_by');
            $sortDirection = $request->get('sort_dir', 'desc');
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderByRaw('(price - sale_price) / price DESC');
        }

        $products = $query->paginate($perPage);

        return new ProductResourceCollection($products);
    }
}
