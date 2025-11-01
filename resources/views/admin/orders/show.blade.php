@extends('admin.layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order: ' . $order->order_reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
            </div>
            <div class="p-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="pb-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="pb-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center">
                                        @if($item->product && $item->product->primary_image_url)
                                            <img src="{{ asset($item->product->primary_image_url) }}" alt="" class="w-12 h-12 rounded object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->product_type === 'digital' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($item->product_type) }}
                                    </span>
                                </td>
                                <td class="py-4 text-right text-sm text-gray-900">₦{{ number_format($item->price, 2) }}</td>
                                <td class="py-4 text-center text-sm text-gray-900">{{ $item->quantity }}</td>
                                <td class="py-4 text-right text-sm text-gray-900">₦{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300">
                            <td colspan="4" class="pt-4 text-right font-medium text-gray-900">Subtotal:</td>
                            <td class="pt-4 text-right font-medium text-gray-900">₦{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="pt-2 text-right text-gray-700">Delivery Fee:</td>
                            <td class="pt-2 text-right text-gray-700">₦{{ number_format($order->delivery_fee, 2) }}</td>
                        </tr>
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="pt-3 text-right text-lg font-bold text-gray-900">Total:</td>
                            <td class="pt-3 text-right text-lg font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Delivery Information -->
        @if($order->delivery_option)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Delivery Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery_full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery_phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Delivery Option</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->delivery_option) }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $order->delivery_address }}<br>
                                {{ $order->delivery_city }}, {{ $order->delivery_lga }}<br>
                                {{ $order->delivery_state }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        @endif

        <!-- Transaction Information -->
        @if($order->transaction)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Transaction Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Transaction Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->transaction->reference }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paystack Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->transaction->paystack_reference ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Channel</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->transaction->payment_channel ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paid At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->transaction->paid_at ? $order->transaction->paid_at->format('d M, Y h:i A') : 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Order Summary -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Reference</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $order->order_reference }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d M, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->user->name }}</dd>
                    <dd class="mt-1 text-sm text-gray-500">{{ $order->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">Payment Status</dt>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Update Order Status -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Update Status</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($order->hasPhysicalProducts() && !$order->isCancelled())
                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? Stock will be restored.');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Cancel Order
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
