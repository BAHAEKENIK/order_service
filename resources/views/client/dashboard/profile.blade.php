@extends('layouts.client-dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile Settings')

@push('styles')
<style>
    .profile-form-container, .delete-account-container {
        background-color: var(--card-bg-light);
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        margin-bottom: 2rem;
    }
    body.dark-mode .profile-form-container, body.dark-mode .delete-account-container {
        background-color: var(--card-bg-dark);
    }
    .form-section-heading {
        font-size: 1.25rem; /* text-xl */
        font-weight: 600; /* semibold */
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .form-section-heading {
        border-bottom-color: var(--border-color-dark);
    }
    .form-label { display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 0.5rem; }
    .form-input, .form-file-input {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        background-color: #F3F4F6; /* Light gray background */
        color: var(--text-dark);
    }
    body.dark-mode .form-input, body.dark-mode .form-file-input {
        background-color: #374151; /* Tailwind gray-700 */
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    .form-input:focus, .form-file-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
    body.dark-mode .form-input:focus, body.dark-mode .form-file-input:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
    .form-group { margin-bottom: 1.5rem; }
    .profile-photo-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 2px solid var(--border-color-light); }
    body.dark-mode .profile-photo-preview { border-color: var(--border-color-dark); }

    .btn-save-profile { background-color: var(--primary-color); color: white; padding: 0.6rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer; }
    .btn-save-profile:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-save-profile { background-color: var(--secondary-color); color: var(--text-dark); }

    .btn-delete-account { background-color: #EF4444; color: white; padding: 0.6rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer; }
    .btn-delete-account:hover { background-color: #DC2626; }
    body.dark-mode .btn-delete-account { background-color: #F87171; color: var(--text-dark); }

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content {
        background-color: var(--card-bg-light);
        margin: 10% auto;
        padding: 2rem;
        border-radius: 0.5rem;
        width: 90%;
        max-width: 500px;
        position: relative;
    }
    body.dark-mode .modal-content { background-color: var(--card-bg-dark); }
    .modal-close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    body.dark-mode .modal-close-btn { color: #777; }
    .modal-close-btn:hover, .modal-close-btn:focus { color: var(--text-dark); text-decoration: none; }
    body.dark-mode .modal-close-btn:hover, body.dark-mode .modal-close-btn:focus { color: var(--text-light); }
    .modal-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; }
    .modal-text { font-size: 0.9rem; margin-bottom: 1.5rem; color: var(--text-muted-light); }
    body.dark-mode .modal-text { color: var(--text-muted-dark); }
    .modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top:1.5rem; }
    .modal-btn-cancel { background-color: #D1D5DB; color: var(--text-dark); padding: 0.5rem 1rem; border-radius:0.375rem; border:none; cursor:pointer;}
    body.dark-mode .modal-btn-cancel { background-color: #4B5563; color: var(--text-light); }
</style>
@endpush

@section('content')
    <div class="profile-form-container">
        <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- @method('PATCH') --}} {{-- Or PUT depending on your preference, POST is fine too --}}

            <h3 class="form-section-heading">Personal Information</h3>

            <div class="form-group">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <img id="photoPreview" src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=4A55A2&background=E0E7FF&size=100' }}" alt="Profile Preview" class="profile-photo-preview">
                <input type="file" name="profile_photo" id="profile_photo" class="form-file-input" accept="image/*" onchange="previewPhoto(event)">
                @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="form-label">First Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', explode(' ', $user->name)[0]) }}" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                 <div class="form-group">
                    <label for="surname" class="form-label">Last Name</label>
                    <input type="text" id="surname" name="surname" class="form-input" value="{{ old('surname', count(explode(' ', $user->name)) > 1 ? explode(' ', $user->name, 2)[1] : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-input" value="{{ old('phone_number', $user->phone_number) }}">
                @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
             <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-input" value="{{ old('address', $user->address) }}">
                 @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
             <div class="form-group">
                <label for="city" class="form-label">City</label>
                <input type="text" id="city" name="city" class="form-input" value="{{ old('city', $user->city) }}">
                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

            <div class="mt-6">
                <button type="submit" class="btn-save-profile">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="delete-account-container">
        <h3 class="form-section-heading text-red-600 dark:text-red-400">Delete Account</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Once your account is deleted, all of its resources and data will be permanently erased. Before deleting your account, please download any data or information that you wish to retain.
        </p>
        <button type="button" class="btn-delete-account" onclick="openDeleteModal()">
            Delete Account
        </button>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeDeleteModal()">×</span>
            <h4 class="modal-title">Confirm Account Deletion</h4>
            <p class="modal-text">Are you sure you want to delete your account? This action cannot be undone. To confirm, please enter your current password.</p>
            <form method="POST" action="{{ route('client.profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <label for="password_delete" class="form-label">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_delete" id="password_delete" class="form-input" required>
                    @if(session('errors') && session('errors')->hasBag('default') && session('errors')->getBag('default')->has('password_delete'))
                        <p class="text-red-500 text-xs mt-1">{{ session('errors')->getBag('default')->first('password_delete') }}</p>
                    @endif
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-delete-account">Delete Account</button>
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

    const deleteModal = document.getElementById('deleteAccountModal');
    function openDeleteModal() {
        deleteModal.style.display = "block";
         // If there was an error previously and the modal was re-shown by the controller, clear old error.
        const errorElement = deleteModal.querySelector('.text-red-500');
        if(errorElement) errorElement.remove();
    }
    function closeDeleteModal() {
        deleteModal.style.display = "none";
        const passwordInput = document.getElementById('password_delete');
        if(passwordInput) passwordInput.value = ''; // Clear password input
    }
    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }

    // Check if controller wants to re-show modal due to password error
    @if(session('show_delete_modal'))
        openDeleteModal();
    @endif
</script>
@endpush
