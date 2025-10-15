@extends('layouts.app')

@section('title', 'Shop - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white p-6 rounded-lg shadow-md sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>
                <form action="{{ route('shop.index') }}" method="GET">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Categories -->
                    @if($categories->count() > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Product Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="type[]" value="physical" {{ in_array('physical', request('type', [])) ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm">Physical</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="type[]" value="digital" {{ in_array('digital', request('type', [])) ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm">Digital</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range (₦)</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 mb-2">Apply Filters</button>
                    @if(request()->hasAny(['search', 'category', 'type', 'min_price', 'max_price']))
                        <a href="{{ route('shop.index') }}" class="block w-full text-center bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400">Clear All</a>
                    @endif
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Sort and Count -->
            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-600">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                <form action="{{ route('shop.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <div class="h-48 bg-gray-200">
                                    @if($product->primary_image_url)
                                        <img src="{{ asset($product->primary_image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                                    <h3 class="text-lg font-semibold text-gray-900 truncate mt-1">{{ $product->name }}</h3>
                                    @if($product->short_description)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $product->short_description }}</p>
                                    @endif
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-xl font-bold text-blue-600">₦{{ number_format($product->price, 2) }}</span>
                                        @if($product->isPhysical())
                                            @if($product->inStock())
                                                <span class="text-sm text-green-600">In Stock</span>
                                            @else
                                                <span class="text-sm text-red-600 font-semibold">Out of Stock</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search terms.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
