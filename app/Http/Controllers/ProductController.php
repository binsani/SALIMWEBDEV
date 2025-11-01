<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['images', 'category', 'subcategory'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Get related products from same category
        $relatedProducts = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}