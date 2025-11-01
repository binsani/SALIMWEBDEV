<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('primaryImage', 'category')
            ->active()
            ->inStock();

        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by product type
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(16);
        $categories = Category::parents()->active()->withCount('products')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        
        $products = Product::with('primaryImage', 'category')
            ->active()
            ->inStock()
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(16);

        $categories = Category::parents()->active()->withCount('products')->get();

        return view('shop.category', compact('products', 'category', 'categories'));
    }
}