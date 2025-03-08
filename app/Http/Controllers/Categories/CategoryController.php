<?php

declare(strict_types=1);

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\DeleteCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Http\Requests\Categories\GetAllCategoriesRequest;
use App\Http\Requests\Categories\GetCategoryByIdRequest;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\CategoryResourceCollection;
use App\Models\Categories\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function getAllCategories(GetAllCategoriesRequest $request): CategoryResourceCollection
    {
        $query = Category::query();

        if ($request->getSearch()) {
            $searchTerm = $request->getSearch();
            $query->where(function($q) use ($searchTerm) {
                $q->where(Category::NAME, 'like', "%{$searchTerm}%")
                    ->orWhere(Category::DESCRIPTION, 'like', "%{$searchTerm}%")
                    ->orWhere(Category::SLUG, 'like', "%{$searchTerm}%");
            });
        }

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        $query->orderBy($sortField, $sortDirection);

        $categories = $query->paginate(15);

        return new CategoryResourceCollection($categories);
    }

    public function createCategory(CreateCategoryRequest $request): CategoryResource|JsonResponse
    {
        $slugValue = Str::kebab(Str::squish($request->getName()));

        if (Category::where(Category::SLUG, $slugValue)->exists()) {
            return response()->json(['message' => 'Category already exists'], 422);
        }

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

    public function editCategory(EditCategoryRequest $request): CategoryResource|JsonResponse
    {
        $category = Category::findOrFail(
            $request->getCategoryId()
        );

        $slugValue = Str::kebab(Str::squish($request->getName()));

        $slugExists = Category::where(Category::SLUG, $slugValue)
            ->where(Category::ID, '!=', $category->getId())
            ->exists();

        if ($slugExists) {
            return response()->json(['message' => 'Kategorija ar šo nosaukumu jau eksistē'], 422);
        }

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
