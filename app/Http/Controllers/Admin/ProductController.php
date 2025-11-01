<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category', 'subcategory', 'primaryImage');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by product type
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::parents()->active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::parents()->active()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products',
            'short_description' => 'nullable|string|max:200',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'product_type' => 'required|in:physical,digital',
            'stock_quantity' => 'required_if:product_type,physical|integer|min:0',
            'digital_file' => 'required_if:product_type,digital|file|max:102400', // 100MB max
            'download_limit' => 'nullable|integer|min:1',
            'download_expiry_hours' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB per image
        ]);

        DB::beginTransaction();
        try {
            // Generate slug
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }

            // Handle digital file upload
            if ($request->product_type === 'digital' && $request->hasFile('digital_file')) {
                $file = $request->file('digital_file');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('digital_products', $filename, 'local');
                $validated['digital_file_path'] = $path;
                $validated['digital_file_size'] = $file->getSize();
            }

            $product = Product::create($validated);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->uploadProductImages($product, $request->file('images'));
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'subcategory', 'images', 'orderItems.order');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::parents()->active()->get();
        $subcategories = [];
        if ($product->category_id) {
            $subcategories = Category::where('parent_id', $product->category_id)->active()->get();
        }
        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string|max:200',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'product_type' => 'required|in:physical,digital',
            'stock_quantity' => 'required_if:product_type,physical|integer|min:0',
            'digital_file' => 'nullable|file|max:102400',
            'download_limit' => 'nullable|integer|min:1',
            'download_expiry_hours' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Update slug if name changed
            if ($product->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);
                $originalSlug = $validated['slug'];
                $count = 1;
                while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $count++;
                }
            }

            // Handle digital file upload
            if ($request->hasFile('digital_file')) {
                // Delete old file
                if ($product->digital_file_path && Storage::disk('local')->exists($product->digital_file_path)) {
                    Storage::disk('local')->delete($product->digital_file_path);
                }

                $file = $request->file('digital_file');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('digital_products', $filename, 'local');
                $validated['digital_file_path'] = $path;
                $validated['digital_file_size'] = $file->getSize();
            }

            $product->update($validated);

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $this->uploadProductImages($product, $request->file('images'));
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete digital file
            if ($product->digital_file_path && Storage::disk('local')->exists($product->digital_file_path)) {
                Storage::disk('local')->delete($product->digital_file_path);
            }

            // Delete product images
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Delete a product image.
     */
    public function deleteImage(ProductImage $image)
    {
        try {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    /**
     * Upload product images.
     */
    private function uploadProductImages(Product $product, array $images)
    {
        $manager = new ImageManager(new Driver());
        $isFirst = $product->images()->count() === 0;

        foreach ($images as $index => $image) {
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = 'products/' . $filename;

            // Resize and optimize image
            $img = $manager->read($image);
            $img->scale(width: 800);
            
            Storage::disk('public')->put($path, (string) $img->encode());

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $isFirst && $index === 0,
                'display_order' => $index,
            ]);
        }
    }
}
