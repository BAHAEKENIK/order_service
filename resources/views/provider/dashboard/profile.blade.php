@extends('layouts.provider-dashboard')

@section('title', 'My Profile & Settings')
@section('page-title', 'Profile & Professional Settings')

@push('styles')
<style>
    /* Reusing styles from client profile for consistency, adjust as needed */
    .profile-form-container, .delete-account-container, .certificates-container {
        background-color: var(--card-bg-light); padding: 2rem; border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        margin-bottom: 2rem;
    }
    body.dark-mode .profile-form-container, body.dark-mode .delete-account-container, body.dark-mode .certificates-container {
        background-color: var(--card-bg-dark);
    }
    .form-section-heading { font-size: 1.25rem; font-weight: 600; padding-bottom: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color-light); }
    body.dark-mode .form-section-heading { border-bottom-color: var(--border-color-dark); }
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
    .profile-photo-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 2px solid var(--border-color-light); }
    body.dark-mode .profile-photo-preview { border-color: var(--border-color-dark); }

    .btn-save-profile { background-color: var(--primary-color); color: white !important; padding: 0.6rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer; }
    .btn-save-profile:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-save-profile { background-color: var(--secondary-color); color: var(--text-dark) !important; }

    .btn-delete-account { background-color: #EF4444; color: white; padding: 0.6rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer; }
    .btn-delete-account:hover { background-color: #DC2626; }
    body.dark-mode .btn-delete-account { background-color: #F87171; color: var(--text-dark); }

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: var(--card-bg-light); margin: 10% auto; padding: 2rem; border-radius: 0.5rem; width: 90%; max-width: 450px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
    body.dark-mode .modal-content { background-color: var(--card-bg-dark); }
    .modal-close-btn { color: #aaa; position: absolute; top: 10px; right: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
    body.dark-mode .modal-close-btn { color: #777; }
    .modal-close-btn:hover, .modal-close-btn:focus { color: var(--text-dark); text-decoration: none; }
    body.dark-mode .modal-close-btn:hover, body.dark-mode .modal-close-btn:focus { color: var(--text-light); }
    .modal-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
    .modal-text { font-size: 0.9rem; margin-bottom: 1.5rem; color: var(--text-muted-light); }
    body.dark-mode .modal-text { color: var(--text-muted-dark); }
    .modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top:1.5rem; }
    .modal-btn { padding: 0.5rem 1rem; border-radius:0.375rem; border:none; cursor:pointer; font-weight: 500; }
    .modal-btn-cancel { background-color: var(--border-color-light); color: var(--text-dark); }
    body.dark-mode .modal-btn-cancel { background-color: var(--border-color-dark); color: var(--text-light); }
    .modal-btn-confirm { background-color: #EF4444; color: white; }
    body.dark-mode .modal-btn-confirm { background-color: #F87171; color: var(--text-dark); }

    .certificate-list li { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color-light); }
    body.dark-mode .certificate-list li { border-bottom-color: var(--border-color-dark); }
    .certificate-list li:last-child { border-bottom: none; }
    .certificate-list a { color: var(--primary-color); text-decoration: underline; }
    body.dark-mode .certificate-list a { color: var(--secondary-color); }
</style>
@endpush

@section('content')
    <div class="profile-form-container">
        <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- @method('PUT') --}} {{-- Use POST as per typical form handling --}}

            <h3 class="form-section-heading">Personal Information</h3>
            <div class="form-group">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <img id="photoPreview" src="{{ $provider->profile_photo_path ? Storage::url($provider->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($provider->name).'&color=4A55A2&background=E0E7FF&size=100' }}" alt="Profile Preview" class="profile-photo-preview">
                <input type="file" name="profile_photo" id="profile_photo" class="form-file-input" accept="image/*" onchange="previewPhoto(event)">
                @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Full Name / Business Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $provider->name) }}" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $provider->email) }}" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" class="form-input" value="{{ old('phone_number', $provider->phone_number) }}">
                    @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="address" class="form-label">Primary Business Address</label>
                <input type="text" id="address" name="address" class="form-input" value="{{ old('address', $provider->address) }}">
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="city" class="form-label">Primary Business City</label>
                <input type="text" id="city" name="city" class="form-input" value="{{ old('city', $provider->city) }}">
                 @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <h3 class="form-section-heading mt-8">Professional Details</h3>
            <div class="form-group">
                <label for="professional_description" class="form-label">Professional Description / About You</label>
                <textarea name="professional_description" id="professional_description" rows="5" class="form-textarea" placeholder="Tell clients about your expertise and services...">{{ old('professional_description', $provider->providerDetail->professional_description ?? '') }}</textarea>
                 @error('professional_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
             <div class="form-group">
                <label for="is_available" class="form-label">Availability Status</label>
                <select name="is_available" id="is_available" class="form-select">
                    <option value="1" {{ old('is_available', $provider->providerDetail->is_available ?? true) == true ? 'selected' : '' }}>Available for new requests</option>
                    <option value="0" {{ old('is_available', $provider->providerDetail->is_available ?? true) == false ? 'selected' : '' }}>Not currently available</option>
                </select>
            </div>

            <div class="certificates-container">
                <h4 class="text-md font-semibold mb-3">Manage Certifications/Licenses (Optional)</h4>
                @if($provider->providerDetail && $provider->providerDetail->certificates && count($provider->providerDetail->certificates) > 0)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current files:</p>
                    <ul class="list-disc pl-5 mb-3 text-sm certificate-list">
                        @foreach($provider->providerDetail->certificates as $certificate)
                            <li>
                                <a href="{{ $certificate['file_url'] ?? '#' }}" target="_blank">{{ $certificate['name'] ?? 'Certificate File' }}</a>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" name="certificates_to_remove[]" value="{{ $certificate['file_url'] ?? '' }}" class="form-checkbox h-4 w-4 text-red-600">
                                    <span class="ml-2 text-red-600 text-xs">Remove</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="form-group">
                    <label for="new_certificates" class="form-label">Upload New Files (select multiple if needed):</label>
                    <input type="file" name="new_certificates[]" id="new_certificates" class="form-file-input" multiple accept=".pdf,.jpg,.jpeg,.png">
                    @error('new_certificates.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>


            <h3 class="form-section-heading mt-8">Change Password (Optional)</h3>
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password">
                 @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" autocomplete="new-password">
                     @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input" autocomplete="new-password">
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="btn-save-profile">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="delete-account-container">
        <h3 class="form-section-heading text-red-600 dark:text-red-400">Delete Account</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Once your account is deleted, all of its resources, services, and data will be permanently erased. Before deleting your account, please download any data or information that you wish to retain. This action cannot be undone.
        </p>
        <button type="button" class="btn-delete-account" onclick="openProviderDeleteModal()">
            Delete My Provider Account
        </button>
    </div>

    <!-- Delete Account Modal for Provider -->
    <div id="deleteProviderAccountModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeProviderDeleteModal()">×</span>
            <h4 class="modal-title">Confirm Account Deletion</h4>
            <p class="modal-text">Are you absolutely sure you want to delete your provider account? This action cannot be undone and will remove all your services and data. Please enter your current password to confirm.</p>
            <form method="POST" action="{{ route('provider.profile.destroy') }}"> {{-- Assuming you'll add this route --}}
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <label for="password_delete_provider" class="form-label">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_delete_provider" id="password_delete_provider" class="form-input" required>
                    @if(session('errors') && session('errors')->hasBag('default') && session('errors')->getBag('default')->has('password_delete_provider'))
                        <p class="text-red-500 text-xs mt-1">{{ session('errors')->getBag('default')->first('password_delete_provider') }}</p>
                    @endif
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeProviderDeleteModal()">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewPhoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('photoPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    const deleteProviderModal = document.getElementById('deleteProviderAccountModal');
    function openProviderDeleteModal() {
        if(deleteProviderModal) deleteProviderModal.style.display = "block";
        const errorElement = deleteProviderModal.querySelector('.text-red-500.text-xs.mt-1'); // More specific selector
        if(errorElement) errorElement.remove(); // Clear previous specific password errors from modal
    }
    function closeProviderDeleteModal() {
        if(deleteProviderModal) deleteProviderModal.style.display = "none";
        const passwordInput = document.getElementById('password_delete_provider');
        if(passwordInput) passwordInput.value = '';
    }
    window.onclick = function(event) {
        if (event.target == deleteProviderModal) {
            closeProviderDeleteModal();
        }
    }

    @if(session('show_delete_provider_modal')) // To re-open modal on password error
        openProviderDeleteModal();
    @endif
</script>
@endpush
