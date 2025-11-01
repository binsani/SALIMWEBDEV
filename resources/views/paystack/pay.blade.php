@extends('layouts.app')

@section('title', 'Payment - ' . config('app.name'))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
            <p class="text-gray-600">Order: {{ $order->order_reference }}</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700">Order Total:</span>
                <span class="text-2xl font-bold text-gray-900">₦{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="text-sm text-gray-500">
                Payment will be processed securely via Paystack
            </div>
        </div>

        <button onclick="payWithPaystack()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition">
            Pay Now with Paystack
        </button>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                By clicking "Pay Now", you will be redirected to Paystack to complete your payment securely.
            </p>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack() {
    const handler = PaystackPop.setup({
        key: '{{ $paystackPublicKey }}',
        email: '{{ auth()->user()->email }}',
        amount: {{ $transaction->amount }}, // Amount in kobo
        currency: '{{ $transaction->currency }}',
        ref: '{{ $transaction->reference }}',
        callback: function(response) {
            // Payment successful
            window.location.href = '{{ route('paystack.callback') }}?reference=' + response.reference;
        },
        onClose: function() {
            // User closed the payment modal
            alert('Payment was not completed. You can try again anytime.');
        }
    });

    handler.openIframe();
}

// Auto-trigger payment on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        payWithPaystack();
    }, 500);
});
</script>
@endsection
