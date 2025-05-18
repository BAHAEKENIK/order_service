@extends('layouts.provider-dashboard')

@section('title', 'Add New Service')
@section('page-title', 'Create New Service')

@push('styles')
<style>
    .form-container { /* Shared style with profile edit */
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
    .img-preview-create-service { max-width: 200px; max-height: 150px; margin-top: 0.5rem; border-radius: 0.25rem; border: 1px solid var(--border-color-light); }
    body.dark-mode .img-preview-create-service { border-color: var(--border-color-dark); }
</style>
@endpush

@section('content')
    <a href="{{ route('provider.services.index') }}" class="inline-block mb-6 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to My Services
    </a>

    <div class="form-container">
        <form action="{{ route('provider.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title" class="form-label">Service Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g., Expert Plumbing Leak Repair">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="category_id" class="form-label">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div class="form-group">
                    <label for="base_price" class="form-label">Base Price ($) (Optional)</label>
                    <input type="number" name="base_price" id="base_price" class="form-input" value="{{ old('base_price') }}" placeholder="e.g., 75.50" >
                    @error('base_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="5" class="form-textarea" required placeholder="Detailed description of the service you offer...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="address" class="form-label">Service Address (Optional, uses your profile address if blank)</label>
                    <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $provider->address) }}" placeholder="e.g., 123 Main St">
                </div>
                <div class="form-group">
                    <label for="city" class="form-label">Service City (Optional, uses your profile city if blank)</label>
                    <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $provider->city) }}" placeholder="e.g., Anytown">
                </div>
            </div>

            <div class="form-group">
                <label for="image_path" class="form-label">Service Image (Optional)</label>
                <input type="file" name="image_path" id="image_path" class="form-file-input" accept="image/*" onchange="previewImage(event, 'imagePreviewCreate')">
                 <img id="imagePreviewCreate" src="#" alt="Image Preview" class="img-preview-create-service mt-2" style="display:none;"/>
                @error('image_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available (Visible to Clients)</option>
                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable (Hidden from Clients)</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8">
                <button type="submit" class="btn-submit-form">
                    <i class="fas fa-plus-circle mr-2"></i>Create Service
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
</script>
@endpush
