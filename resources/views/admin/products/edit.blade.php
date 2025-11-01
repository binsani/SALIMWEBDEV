@extends('admin.layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Product Information</h3>
            </div>

            <div class="p-6 space-y-6">
                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('sku') border-red-500 @enderror">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category and Subcategory -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($parentCategories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subcategory_id" class="block text-sm font-medium text-gray-700">Subcategory</label>
                        <select name="subcategory_id" id="subcategory_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subcategory_id') border-red-500 @enderror">
                            <option value="">None</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                    <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="200" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Max 200 characters. Used in product listings.</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="6" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Type <span class="text-red-500">*</span></label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="product_type" value="physical" {{ old('product_type', $product->product_type) === 'physical' ? 'checked' : '' }} class="form-radio text-blue-600" onchange="toggleProductTypeFields()">
                            <span class="ml-2">Physical Product</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="product_type" value="digital" {{ old('product_type', $product->product_type) === 'digital' ? 'checked' : '' }} class="form-radio text-blue-600" onchange="toggleProductTypeFields()">
                            <span class="ml-2">Digital Product</span>
                        </label>
                    </div>
                </div>

                <!-- Physical Product Fields -->
                <div id="physical-fields" class="space-y-4 {{ $product->product_type === 'physical' ? '' : 'hidden' }}">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Digital Product Fields -->
                <div id="digital-fields" class="space-y-4 {{ $product->product_type === 'digital' ? '' : 'hidden' }}">
                    @if($product->product_type === 'digital' && $product->digital_file_path)
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm text-gray-700">Current File: <span class="font-medium">{{ basename($product->digital_file_path) }}</span></p>
                            <p class="text-sm text-gray-500">Size: {{ number_format($product->digital_file_size / 1024 / 1024, 2) }} MB</p>
                        </div>
                    @endif

                    <div>
                        <label for="digital_file" class="block text-sm font-medium text-gray-700">Replace Digital File</label>
                        <input type="file" name="digital_file" id="digital_file" accept=".pdf,.zip,.mp3,.mp4,.epub,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">Leave blank to keep existing file. Max 500MB.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="download_limit" class="block text-sm font-medium text-gray-700">Download Limit</label>
                            <select name="download_limit" id="download_limit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="" {{ old('download_limit', $product->download_limit) === null ? 'selected' : '' }}>Unlimited</option>
                                <option value="1" {{ old('download_limit', $product->download_limit) == 1 ? 'selected' : '' }}>1 download</option>
                                <option value="3" {{ old('download_limit', $product->download_limit) == 3 ? 'selected' : '' }}>3 downloads</option>
                                <option value="5" {{ old('download_limit', $product->download_limit) == 5 ? 'selected' : '' }}>5 downloads</option>
                                <option value="10" {{ old('download_limit', $product->download_limit) == 10 ? 'selected' : '' }}>10 downloads</option>
                            </select>
                        </div>

                        <div>
                            <label for="download_expiry_hours" class="block text-sm font-medium text-gray-700">Download Expiry</label>
                            <select name="download_expiry_hours" id="download_expiry_hours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="" {{ old('download_expiry_hours', $product->download_expiry_hours) === null ? 'selected' : '' }}>Unlimited</option>
                                <option value="24" {{ old('download_expiry_hours', $product->download_expiry_hours) == 24 ? 'selected' : '' }}>24 hours</option>
                                <option value="168" {{ old('download_expiry_hours', $product->download_expiry_hours) == 168 ? 'selected' : '' }}>7 days</option>
                                <option value="720" {{ old('download_expiry_hours', $product->download_expiry_hours) == 720 ? 'selected' : '' }}>30 days</option>
                                <option value="2160" {{ old('download_expiry_hours', $product->download_expiry_hours) == 2160 ? 'selected' : '' }}>90 days</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Existing Product Images -->
                @if($product->images->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($product->images as $image)
                                <div class="relative">
                                    <img src="{{ asset($image->image_path) }}" alt="" class="w-full h-32 object-cover rounded">
                                    <button type="button" onclick="deleteImage({{ $image->id }})" class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full hover:bg-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    @if($image->is_primary)
                                        <span class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Primary</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add New Images -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Max 5MB per image.</p>
                </div>

                <!-- Status Toggles -->
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Product</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleProductTypeFields() {
    const productType = document.querySelector('input[name="product_type"]:checked').value;
    const physicalFields = document.getElementById('physical-fields');
    const digitalFields = document.getElementById('digital-fields');

    if (productType === 'physical') {
        physicalFields.classList.remove('hidden');
        digitalFields.classList.add('hidden');
        document.getElementById('stock_quantity').required = true;
    } else {
        physicalFields.classList.add('hidden');
        digitalFields.classList.remove('hidden');
        document.getElementById('stock_quantity').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleProductTypeFields);

// Handle subcategory loading
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');

    subcategorySelect.innerHTML = '<option value="">Loading...</option>';

    if (!categoryId) {
        subcategorySelect.innerHTML = '<option value="">None</option>';
        return;
    }

    fetch(`/admin/categories/${categoryId}/subcategories`)
        .then(response => response.json())
        .then(data => {
            subcategorySelect.innerHTML = '<option value="">None</option>';
            data.forEach(subcategory => {
                const option = document.createElement('option');
                option.value = subcategory.id;
                option.textContent = subcategory.name;
                subcategorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading subcategories:', error);
            subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
        });
});

// Delete image function
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    fetch(`/admin/products/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting image');
    });
}
</script>
@endpush
@endsection
