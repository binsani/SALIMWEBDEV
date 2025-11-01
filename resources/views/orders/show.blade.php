@extends('layouts.app')

@section('title', 'Order Details - ' . config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700">
            ← Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_reference }}</h1>
                <p class="text-gray-500">Placed on {{ $order->created_at->format('d M, Y h:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($order->status === 'delivered') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-lg font-semibold mb-4">Order Items</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center">
                        @if($item->product && $item->product->primary_image_url)
                            <img src="{{ asset($item->product->primary_image_url) }}" alt="{{ $item->product_name }}" class="w-20 h-20 object-cover rounded mr-4">
                        @endif
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                            <p class="text-sm text-gray-500">{{ ucfirst($item->product_type) }} Product</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">₦{{ number_format($item->subtotal, 2) }}</p>

                            @if($item->product_type === 'digital' && $order->isPaid() && $item->download)
                                <div class="mt-2">
                                    @if($item->download->isValid())
                                        <a href="{{ route('download', $item->download->download_token) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $item->download->downloads_count }}/{{ $item->download_limit ?? '∞' }} downloads
                                        </p>
                                        @if($item->download->expires_at)
                                            <p class="text-xs text-gray-500">
                                                Expires: {{ $item->download->expires_at->format('d M, Y') }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-xs text-red-600">
                                            @if($item->download->isExpired())
                                                Download expired
                                            @else
                                                Download limit reached
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="border-t border-gray-200 pt-6 mt-6">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Subtotal:</span>
                        <span class="text-gray-900">₦{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Delivery Fee:</span>
                        <span class="text-gray-900">₦{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                        <span class="font-semibold text-gray-900">Total:</span>
                        <span class="font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    @if($order->delivery_option)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Delivery Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium">{{ $order->delivery_full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">{{ $order->delivery_phone }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Address</p>
                    <p class="font-medium">
                        {{ $order->delivery_address }}<br>
                        {{ $order->delivery_city }}, {{ $order->delivery_lga }}<br>
                        {{ $order->delivery_state }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Delivery Option</p>
                    <p class="font-medium">{{ ucfirst($order->delivery_option) }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
