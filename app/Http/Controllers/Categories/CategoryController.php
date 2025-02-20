<?php

declare(strict_types=1);

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\DeleteCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Http\Requests\Categories\GetCategoryByIdRequest;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\CategoryResourceCollection;
use App\Models\Categories\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function getAllCategories(): CategoryResourceCollection
    {
        $categories = Category::paginate(15);

        return new CategoryResourceCollection($categories);
    }
    public function createCategory(CreateCategoryRequest $request): CategoryResource
    {
        $slugValue = Str::kebab(Str::squish($request->getName()));

        $category = Category::create([
            Category::NAME => $request->getName(),
            Category::DESCRIPTION => $request->getDescription(),
            Category::SLUG => $slugValue,
        ]);

        return new CategoryResource($category);
    }

    public function getCategoryById(GetCategoryByIdRequest $request): CategoryResource
    {
        $category = Category::findOrFail(
            $request->getCategoryId()
        );

        return new CategoryResource($category);
    }

    public function editCategory(EditCategoryRequest $request): CategoryResource
    {
        $category = Category::findOrFail(
            $request->getCategoryId()
        );

        $slugValue = Str::kebab(Str::squish($request->getName()));

        $category->update([
            Category::NAME => $request->getName(),
            Category::DESCRIPTION => $request->getDescription(),
            Category::SLUG => $slugValue,
        ]);

        return new CategoryResource($category);
    }

    public function deleteCategory(DeleteCategoryRequest $request): JsonResponse
    {
        $category = Category::findOrFail(
            $request->getCategoryId()
        );

        $category->delete();

        return new JsonResponse([], 204);
    }
}
