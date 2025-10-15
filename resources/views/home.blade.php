@extends('layouts.app')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<div class="bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to {{ config('app.name') }}</h1>
            <p class="text-xl mb-8">Your trusted Nigerian e-commerce platform for physical and digital products</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-md font-semibold hover:bg-gray-100 transition">
                Shop Now
            </a>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($featuredProducts as $product)
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
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                        @if($product->short_description)
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $product->short_description }}</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-blue-600">₦{{ number_format($product->price, 2) }}</span>
                            @if($product->isPhysical() && !$product->inStock())
                                <span class="text-sm text-red-600 font-semibold">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No featured products available.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Latest Products -->
<div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($latestProducts as $product)
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
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                            @if($product->short_description)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xl font-bold text-blue-600">₦{{ number_format($product->price, 2) }}</span>
                                @if($product->isPhysical() && !$product->inStock())
                                    <span class="text-sm text-red-600 font-semibold">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">No products available yet.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('shop.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-md font-semibold hover:bg-blue-700 transition">
                View All Products
            </a>
        </div>
    </div>
</div>
@endsection
