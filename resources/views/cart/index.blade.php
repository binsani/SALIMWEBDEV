@extends('layouts.app')

@section('title', 'Shopping Cart - ' . config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    @php
        $cart = session('cart', []);
        $total = 0;
    @endphp

    @if(count($cart) > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cart as $id => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if(isset($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded mr-4">
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $item['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $item['type'] ?? 'Physical' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900">₦{{ number_format($item['price'], 2) }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['max_quantity'] ?? 99 }}" class="w-20 px-2 py-1 border border-gray-300 rounded-md" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">₦{{ number_format($subtotal, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cart Summary -->
        <div class="mt-8 flex justify-between items-start">
            <div>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Clear Cart</button>
                </form>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal:</span>
                        <span>₦{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Delivery fee will be calculated at checkout
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Cart Total:</span>
                        <span>₦{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition">
                    Proceed to Checkout
                </a>
                <a href="{{ route('shop.index') }}" class="block w-full text-center mt-3 text-blue-600 hover:text-blue-700">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-md">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-gray-500">Start shopping to add items to your cart.</p>
            <div class="mt-6">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Browse Products
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
