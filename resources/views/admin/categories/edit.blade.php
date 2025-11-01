@extends('admin.layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category: ' . $category->name)

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Category Information</h3>
            </div>

            <div class="p-6 space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Category -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None (Make this a parent category)</option>
                        @foreach($parentCategories as $parentCategory)
                            @if($parentCategory->id !== $category->id)
                                <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                    {{ $parentCategory->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Leave blank to make this a parent category.</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <!-- Display Order -->
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $category->display_order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                @if($category->products_count > 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    This category has <strong>{{ $category->products_count }}</strong> product(s). Deleting it will require reassigning products first.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Category</button>
            </div>
        </div>
    </form>
</div>
@endsection
