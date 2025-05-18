@extends('layouts.client-dashboard')

@section('title', 'New Service Request to ' . $provider->name)
@section('page-title', 'Service Request')

@push('styles')
<style>
    .form-container {
        background-color: var(--card-bg-light);
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    }
    body.dark-mode .form-container {
        background-color: var(--card-bg-dark);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3), 0 2px 4px -1px rgba(0,0,0,0.2);
    }

    .form-label {
        display: block;
        font-weight: 500; /* medium */
        font-size: 0.875rem; /* text-sm */
        margin-bottom: 0.5rem; /* mb-2 */
        color: var(--text-dark);
    }
    body.dark-mode .form-label {
        color: var(--text-light);
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.65rem 0.9rem; /* Adjusted padding for inputs to match PDF */
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem; /* rounded-md */
        font-size: 0.875rem;
        background-color: #F3F4F6; /* Light gray background for inputs like PDF (form-accent-bg-light) */
        color: var(--text-dark);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-input::placeholder,
    .form-textarea::placeholder {
        color: var(--text-muted-light, #9CA3AF); /* text-gray-400 */
    }

    body.dark-mode .form-input,
    body.dark-mode .form-select,
    body.dark-mode .form-textarea {
        background-color: #374151; /* Tailwind gray-700 for input bg in dark */
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    body.dark-mode .form-input::placeholder,
    body.dark-mode .form-textarea::placeholder {
        color: var(--text-muted-dark, #6B7280); /* text-gray-500 */
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2);
    }
    body.dark-mode .form-input:focus,
    body.dark-mode .form-select:focus,
    body.dark-mode .form-textarea:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2);
    }
    .form-textarea {
        min-height: 120px; /* Adjust as needed for Details field */
    }
    .form-grid-cols-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5rem; /* gap-6 */
    }
    .form-group {
        margin-bottom: 1.25rem; /* mb-5 */
    }
    .btn-submit-request {
        background-color: var(--primary-color);
        color: white;
        padding: 0.6rem 1.5rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .btn-submit-request:hover {
        background-color: var(--primary-hover-color);
    }
    body.dark-mode .btn-submit-request {
        background-color: var(--secondary-color);
        color: var(--text-dark); /* Contrast on lighter secondary button */
    }
     body.dark-mode .btn-submit-request:hover {
        background-color: #5E7CB6; /* Darker shade of secondary */
    }
</style>
@endpush

@section('content')
    <div class="form-container max-w-3xl mx-auto">
        <h2 class="text-xl font-semibold mb-2 text-gray-700 dark:text-gray-300">
            Requesting service from: <span class="text-primary-color dark:text-secondary-color">{{ $provider->name }}</span>
        </h2>
        @if($service)
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                Regarding service: <strong class="font-medium">{{ $service->title }}</strong>
            </p>
        @endif

        <form action="{{ route('client.request.service.store', $provider) }}" method="POST">
            @csrf
            {{-- If a specific service was selected, pass its ID --}}
            @if($service)
                <input type="hidden" name="service_id" value="{{ $service->id }}">
            @endif

            <div class="form-grid-cols-2 mb-5">
                <div class="form-group">
                    <label for="client_name" class="form-label">Name</label>
                    <input type="text" id="client_name" name="client_name" class="form-input" placeholder="Input text" value="{{ old('client_name', $client->name) }}" required>
                     @error('client_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="client_surname" class="form-label">Surname</label> {{-- Assuming surname is part of client_name --}}
                    <input type="text" id="client_surname" name="client_surname" class="form-input" placeholder="Input text" value="{{ old('client_surname', explode(' ', $client->name, 2)[1] ?? '') }}">
                    @error('client_surname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address <span class="text-red-500">*</span></label>
                <input type="text" id="address" name="address" class="form-input" placeholder="Add address" value="{{ old('address', $client->address) }}" required>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid-cols-2 mb-5">
                <div class="form-group">
                    <label for="city" class="form-label">City <span class="text-red-500">*</span></label>
                    <input type="text" id="city" name="city" class="form-input" placeholder="Select city" value="{{ old('city', $client->city) }}" required>
                     @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="state_province" class="form-label">State/Province</label> {{-- Not directly in PDF for ServiceRequest model, but common --}}
                    <input type="text" id="state_province" name="state_province" class="form-input" placeholder="Select province" value="{{ old('state_province') }}">
                    @error('state_province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid-cols-2 mb-5">
                <div class="form-group">
                    <label for="zip_postal_code" class="form-label">Zip/Postal code</label>
                    <input type="text" id="zip_postal_code" name="zip_postal_code" class="form-input" placeholder="Input code" value="{{ old('zip_postal_code') }}">
                     @error('zip_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="proposed_budget" class="form-label">Budget (Optional)</label>
                    <input type="number" id="proposed_budget" name="proposed_budget" class="form-input" placeholder="10" value="{{ old('proposed_budget') }}" step="0.01">
                    @error('proposed_budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid-cols-2 mb-5">
                <div class="form-group">
                    <label for="category_id" class="form-label">Category <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', ($selectedCategory && $selectedCategory->id == $category->id) ? $category->id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                     @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="service_title_display" class="form-label">Service (Optional)</label>
                    <input type="text" id="service_title_display" name="service_title_display" class="form-input" placeholder="e.g., Leaky Faucet Repair" value="{{ old('service_title_display', $service->title ?? '') }}" {{ $service ? 'readonly' : '' }}>
                     {{-- If not selecting a specific service_id, a title might be manually entered or derived --}}
                     @if(!$service) <small class="text-xs text-gray-500 dark:text-gray-400">Specify service if not choosing from provider's list.</small>@endif
                </div>
            </div>

             <div class="form-group">
                <label for="desired_date_time" class="form-label">Desired Date (Optional)</label>
                <input type="datetime-local" id="desired_date_time" name="desired_date_time" class="form-input" value="{{ old('desired_date_time') }}">
                 @error('desired_date_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Details <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="6" class="form-textarea" placeholder="Input text" required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn-submit-request">
                    Submit
                </button>
            </div>
        </form>
    </div>
@endsection
