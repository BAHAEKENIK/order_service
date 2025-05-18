@extends('layouts.provider-dashboard')

@section('title', 'Edit Service: ' . $service->title)
@section('page-title', 'Edit Service')

@push('styles')
<style>
    /* Styles are similar to create-service, could be shared */
    .form-container {
        background-color: var(--card-bg-light); padding: 2rem; border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        max-width: 800px; margin: 0 auto;
    }
    body.dark-mode .form-container { background-color: var(--card-bg-dark); }
    .form-label { display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 0.5rem; }
    .form-input, .form-select, .form-textarea, .form-file-input {
        width: 100%; padding: 0.65rem 0.9rem; border: 1px solid var(--border-color-light);
        border-radius: 0.375rem; font-size: 0.875rem; background-color: #F3F4F6; color: var(--text-dark);
    }
    body.dark-mode .form-input, body.dark-mode .form-select, body.dark-mode .form-textarea, body.dark-mode .form-file-input {
        background-color: #374151; border-color: var(--border-color-dark); color: var(--text-light);
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus, .form-file-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
    body.dark-mode .form-input:focus, body.dark-mode .form-select:focus, body.dark-mode .form-textarea:focus, body.dark-mode .form-file-input:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
    .form-group { margin-bottom: 1.5rem; }
    .btn-submit-form {
        background-color: var(--primary-color); color: white !important; padding: 0.75rem 1.5rem;
        border-radius: 0.375rem; font-weight: 500; cursor:pointer; border: none;
    }
    .btn-submit-form:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-submit-form { background-color: var(--secondary-color); color: var(--text-dark) !important;}
    .img-preview-edit-service { max-width: 200px; max-height: 150px; margin-top: 0.5rem; border-radius: 0.25rem; border: 1px solid var(--border-color-light); }
    body.dark-mode .img-preview-edit-service { border-color: var(--border-color-dark); }
</style>
@endpush

@section('content')
    <a href="{{ route('provider.services.index') }}" class="inline-block mb-6 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to My Services
    </a>

    <div class="form-container">
        <h2 class="text-xl font-semibold mb-6 text-gray-700 dark:text-gray-300">Editing: {{ $service->title }}</h2>

        <form action="{{ route('provider.services.update', $service) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label">Service Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $service->title) }}" required>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="category_id" class="form-label">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="base_price" class="form-label">Base Price ($) (Optional)</label>
                    <input type="number" name="base_price" id="base_price" class="form-input" value="{{ old('base_price', $service->base_price) }}" step="0.01" min="0">
                     @error('base_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="5" class="form-textarea" required>{{ old('description', $service->description) }}</textarea>
                 @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

             <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="address" class="form-label">Service Address</label>
                    <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $service->address) }}">
                </div>
                <div class="form-group">
                    <label for="city" class="form-label">Service City</label>
                    <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $service->city) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="new_image_path" class="form-label">New Service Image (Optional)</label>
                @if($service->image_path)
                <div class="mb-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Image:</p>
                    <img src="{{ Storage::url($service->image_path) }}" alt="Current Service Image" class="img-preview-edit-service">
                    <label class="inline-flex items-center mt-1 text-xs">
                        <input type="checkbox" name="remove_existing_image" value="1" class="form-checkbox h-4 w-4 text-primary-color">
                        <span class="ml-2 text-gray-600 dark:text-gray-400">Remove current image</span>
                    </label>
                </div>
                @endif
                <input type="file" name="new_image_path" id="new_image_path" class="form-file-input" accept="image/*" onchange="previewImage(event, 'imagePreviewEdit')">
                 <img id="imagePreviewEdit" src="#" alt="New Image Preview" class="img-preview-edit-service mt-2" style="display:none;"/>
                 @error('new_image_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available" {{ old('status', $service->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $service->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                 @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8">
                <button type="submit" class="btn-submit-form">
                    <i class="fas fa-save mr-2"></i>Update Service
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(event, previewElementId) {
        const reader = new FileReader();
        const output = document.getElementById(previewElementId);
        reader.onload = function(){
            output.src = reader.result;
            output.style.display = 'block';
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
             output.src = '#';
             output.style.display = 'none';
        }
    }
     // Trigger preview if an old value for image was there but validation failed
    @if(old('new_image_path'))
        // This part is tricky if old file path is not directly available on frontend after POST
        // It's better to rely on backend to re-display current image if new upload fails validation
    @elseif($service->image_path && !old('remove_existing_image') && !$errors->has('new_image_path'))
        // If there is an existing image, and no 'remove' checked, and no errors related to new image
        // The current image is already shown via src attribute in HTML directly
    @endif
</script>
@endpush
