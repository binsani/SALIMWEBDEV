<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('primaryImage', 'category')
            ->active()
            ->featured()
            ->inStock()
            ->take(8)
            ->get();

        $latestProducts = Product::with('primaryImage', 'category')
            ->active()
            ->inStock()
            ->latest()
            ->take(12)
            ->get();

        $categories = Category::parents()
            ->active()
            ->withCount('products')
            ->orderBy('display_order')
            ->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }
}