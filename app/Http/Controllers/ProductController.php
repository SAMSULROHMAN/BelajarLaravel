<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->search($request->q)
            ->category($request->category_id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::select('id','name')->get();
        return view('products.index', compact('products','categories'));
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }
}
