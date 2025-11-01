@extends('admin.layouts.admin')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction: ' . $transaction->reference)

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Transaction Summary -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Transaction Summary</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Transaction Reference</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->reference }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Paystack Reference</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->paystack_reference ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">₦{{ number_format($transaction->amount / 100, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Currency</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->currency }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($transaction->status === 'success') bg-green-100 text-green-800
                            @elseif($transaction->status === 'failed') bg-red-100 text-red-800
                            @elseif($transaction->status === 'abandoned') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Channel</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->payment_channel ? ucfirst($transaction->payment_channel) : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('d M, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Paid At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->paid_at ? $transaction->paid_at->format('d M, Y h:i A') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Webhook Processed</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->webhook_processed ? 'Yes' : 'No' }}</dd>
                </div>
                @if($transaction->authorization_code)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Authorization Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->authorization_code }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->user->phone ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Associated Order -->
    @if($transaction->order)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Associated Order</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Reference</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->order->order_reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($transaction->order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($transaction->order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($transaction->order->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <a href="{{ route('admin.orders.show', $transaction->order->id) }}" class="text-blue-600 hover:text-blue-900">
                            View Order Details →
                        </a>
                    </div>
                </dl>
            </div>
        </div>
    @endif

    <!-- Paystack Response -->
    @if($transaction->paystack_response)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Paystack API Response</h3>
            </div>
            <div class="p-6">
                <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs font-mono">{{ json_encode($transaction->paystack_response, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-end">
        <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Back to Transactions
        </a>
    </div>
</div>
@endsection
