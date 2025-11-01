@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-blue-600">Shop</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.category', $product->category->slug) }}" class="hover:text-blue-600">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-4">
                @if($product->images->count() > 0)
                    <img id="mainImage" src="{{ asset($product->primary_image_url) }}" alt="{{ $product->name }}" class="w-full h-96 object-contain mb-4">
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images as $image)
                                <img src="{{ asset($image->image_path) }}" alt="" class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75" onclick="changeImage('{{ asset($image->image_path) }}')">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-96 bg-gray-200 rounded flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                @if($product->sku)
                    <p class="text-sm text-gray-500 mb-4">SKU: {{ $product->sku }}</p>
                @endif

                <div class="mb-4">
                    <span class="text-4xl font-bold text-blue-600">₦{{ number_format($product->price, 2) }}</span>
                </div>

                <!-- Stock Status -->
                @if($product->isPhysical())
                    @if($product->inStock())
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                In Stock ({{ $product->stock_quantity }} available)
                            </span>
                        </div>
                    @else
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        </div>
                    @endif
                @else
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                            Digital Product - Instantly Available
                        </span>
                    </div>
                @endif

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="text-gray-700 mb-6">{{ $product->short_description }}</p>
                @endif

                <!-- Add to Cart Form -->
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-6">
                    @csrf
                    @if($product->isPhysical() && $product->inStock())
                        <div class="flex items-center mb-4">
                            <label for="quantity" class="mr-4 text-gray-700 font-medium">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-20 px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    @elseif($product->isDigital())
                        <input type="hidden" name="quantity" value="1">
                    @endif

                    @if(($product->isPhysical() && $product->inStock()) || $product->isDigital())
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to Cart
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-400 text-white px-6 py-3 rounded-md font-semibold cursor-not-allowed">
                            Out of Stock
                        </button>
                    @endif
                </form>

                <!-- Product Type Info -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-700">Category:</span>
                        <a href="{{ route('shop.category', $product->category->slug) }}" class="text-blue-600 hover:underline">{{ $product->category->name }}</a>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-700">Type:</span>
                        <span class="font-medium">{{ ucfirst($product->product_type) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="mt-12 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Description</h2>
        <div class="prose max-w-none text-gray-700">
            {!! nl2br(e($product->description)) !!}
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="{{ route('products.show', $related->slug) }}">
                            <div class="h-48 bg-gray-200">
                                @if($related->primary_image_url)
                                    <img src="{{ asset($related->primary_image_url) }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $related->name }}</h3>
                                <span class="text-xl font-bold text-blue-600">₦{{ number_format($related->price, 2) }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}
</script>
@endpush
@endsection
