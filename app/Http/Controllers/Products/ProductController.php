<?php

declare(strict_types=1);

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Products\Product;

class ProductController extends Controller
{
    public function getAllProducts(): ProductResourceCollection
    {
        $products = Product::paginate(15);

        return new ProductResourceCollection($products);
    }
}
