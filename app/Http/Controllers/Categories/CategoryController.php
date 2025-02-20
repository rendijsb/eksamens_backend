<?php

declare(strict_types=1);

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Resources\Categories\CategoryResource;
use App\Models\Categories\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function createCategory(CreateCategoryRequest $request): CategoryResource
    {
        $slugValue = Str::kebab(Str::squish($request->getName()));

        $category = Category::create([
            Category::NAME => $request->getName(),
            Category::DESCRIPTION => $request->getEmail(),
            Category::SLUG => $slugValue,
        ]);

        return new CategoryResource($category);
    }
}
