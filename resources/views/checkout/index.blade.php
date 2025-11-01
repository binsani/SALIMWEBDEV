@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                @if($hasPhysicalProducts)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Delivery Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', auth()->user()->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('full_name') border-red-500 @enderror">
                                @error('full_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                                @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="street_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                <textarea name="street_address" id="street_address" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('street_address') border-red-500 @enderror">{{ old('street_address') }}</textarea>
                                @error('street_address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City/Town</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('city') border-red-500 @enderror">
                                @error('city')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <select name="state_id" id="state_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('state_id') border-red-500 @enderror" onchange="loadLgas()">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="lga_id" class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                                <select name="lga_id" id="lga_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md @error('lga_id') border-red-500 @enderror">
                                    <option value="">Select LGA</option>
                                </select>
                                @error('lga_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Delivery Option</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="delivery_option" value="lagos" required class="mr-3" {{ old('delivery_option') === 'lagos' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-medium">Lagos Delivery</span>
                                        <span class="ml-2 text-gray-600">(₦{{ number_format($deliveryFees['lagos'], 2) }})</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="delivery_option" value="nationwide" required class="mr-3" {{ old('delivery_option') === 'nationwide' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-medium">Nationwide Delivery</span>
                                        <span class="ml-2 text-gray-600">(₦{{ number_format($deliveryFees['nationwide'], 2) }})</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="delivery_option" value="pickup" required class="mr-3" {{ old('delivery_option') === 'pickup' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-medium">Pickup</span>
                                        <span class="ml-2 text-green-600">(Free)</span>
                                    </div>
                                </label>
                            </div>
                            @error('delivery_option')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow-md p-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="terms" required class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-sm text-gray-700">I agree to the terms and conditions</span>
                    </label>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                                <span class="text-gray-900">₦{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-4 space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>₦{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Delivery Fee:</span>
                            <span id="deliveryFee">₦0.00</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>Total:</span>
                            <span id="orderTotal">₦{{ number_format($cartTotal, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition">
                        Pay Now
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadLgas() {
    const stateId = document.getElementById('state_id').value;
    const lgaSelect = document.getElementById('lga_id');

    lgaSelect.innerHTML = '<option value="">Loading...</option>';

    if (!stateId) {
        lgaSelect.innerHTML = '<option value="">Select LGA</option>';
        return;
    }

    fetch(`{{ route('checkout.lgas', ':state') }}`.replace(':state', stateId))
        .then(response => response.json())
        .then(data => {
            lgaSelect.innerHTML = '<option value="">Select LGA</option>';
            data.forEach(lga => {
                const option = document.createElement('option');
                option.value = lga.id;
                option.textContent = lga.name;
                lgaSelect.appendChild(option);
            });
        });
}

// Update delivery fee when option changes
const deliveryOptions = document.querySelectorAll('input[name="delivery_option"]');
const deliveryFees = {
    lagos: {{ $deliveryFees['lagos'] }},
    nationwide: {{ $deliveryFees['nationwide'] }},
    pickup: 0
};
const cartTotal = {{ $cartTotal }};

deliveryOptions.forEach(option => {
    option.addEventListener('change', function() {
        const fee = deliveryFees[this.value] || 0;
        document.getElementById('deliveryFee').textContent = '₦' + fee.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('orderTotal').textContent = '₦' + (cartTotal + fee).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    });
});
</script>
@endpush
@endsection
