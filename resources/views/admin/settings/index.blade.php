@extends('admin.layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Site Settings')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- General Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Nigerian phone format (e.g., 08012345678)</p>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Site Logo</label>
                    @if(isset($settings['logo_path']) && $settings['logo_path'])
                        <div class="mt-2 mb-3">
                            <img src="{{ asset($settings['logo_path']) }}" alt="Current Logo" class="h-20">
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Max 2MB. JPG, PNG, or WebP.</p>
                </div>
            </div>
        </div>

        <!-- Delivery Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Delivery Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="delivery_fee_lagos" class="block text-sm font-medium text-gray-700">Lagos Delivery Fee (₦)</label>
                    <input type="number" name="delivery_fee_lagos" id="delivery_fee_lagos" value="{{ old('delivery_fee_lagos', $settings['delivery_fee_lagos'] ?? '2000') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Fee for deliveries within Lagos State</p>
                </div>

                <div>
                    <label for="delivery_fee_nationwide" class="block text-sm font-medium text-gray-700">Nationwide Delivery Fee (₦)</label>
                    <input type="number" name="delivery_fee_nationwide" id="delivery_fee_nationwide" value="{{ old('delivery_fee_nationwide', $settings['delivery_fee_nationwide'] ?? '4000') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Fee for deliveries outside Lagos</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-700"><strong>Pickup:</strong> Always free (₦0)</p>
                </div>
            </div>
        </div>

        <!-- Stock Management Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Stock Management</h3>
            </div>
            <div class="p-6">
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Alert Threshold</label>
                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? '5') }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Alert when stock reaches or falls below this number</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
