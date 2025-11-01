@extends('admin.layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create New Category')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Category Information</h3>
            </div>

            <div class="p-6 space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Category -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None (Make this a parent category)</option>
                        @foreach($parentCategories as $category)
                            <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Leave blank to create a parent category, or select a parent to create a subcategory.</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Optional description for this category.</p>
                </div>

                <!-- Display Order -->
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Lower numbers appear first. Default is 0.</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Category</button>
            </div>
        </div>
    </form>
</div>
@endsection
