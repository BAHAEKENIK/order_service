@extends('layouts.admin-dashboard')

@section('title', 'My Admin Profile')
@section('page-title', 'Edit My Profile')

@push('styles')
<style>
    .profile-form-container {
        background-color: var(--card-bg-light);
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        max-width: 700px;
        margin: 0 auto;
    }
    body.dark-mode .profile-form-container {
        background-color: var(--card-bg-dark);
    }

    .form-section-heading {
        font-size: 1.125rem;
        font-weight: 600;
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
        background-color: #F9FAFB;
        color: var(--text-dark);
    }
    body.dark-mode .form-input, body.dark-mode .form-file-input {
        background-color: var(--border-color-dark);
        border-color: #4B5563;
        color: var(--text-light);
    }
    .form-input:focus, .form-file-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2);
    }
    body.dark-mode .form-input:focus, body.dark-mode .form-file-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2);
    }
    .form-group { margin-bottom: 1.5rem; }
    .profile-photo-preview {
        width: 100px; height: 100px;
        border-radius: 50%; object-fit: cover;
        margin-bottom: 1rem;
        border: 2px solid var(--border-color-light);
    }
    body.dark-mode .profile-photo-preview { border-color: var(--border-color-dark); }

    .btn-save-profile {
        background-color: var(--primary-color); color: white !important;
        padding: 0.6rem 1.5rem; border-radius: 0.375rem;
        border: none; cursor: pointer; font-weight: 500;
    }
    .btn-save-profile:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-save-profile {
        background-color: var(--secondary-color); color: var(--text-dark) !important;
    }
</style>
@endpush

@section('content')
    <div class="profile-form-container">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h3 class="form-section-heading">Update Your Profile Information</h3>

            <div class="form-group">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <img id="photoPreview" src="{{ $admin->profile_photo_path ? Storage::url($admin->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($admin->name).'&color=FFFFFF&background=4A55A2&size=100' }}" alt="Profile Preview" class="profile-photo-preview">
                <input type="file" name="profile_photo" id="profile_photo" class="form-file-input" accept="image/*" onchange="previewAdminPhoto(event)">
                @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $admin->name) }}" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $admin->email) }}" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>


            <h3 class="form-section-heading mt-8">Change Password (Optional)</h3>
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password" placeholder="Leave blank to keep current password">
                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" autocomplete="new-password" placeholder="At least 8 characters">
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
@endsection

@push('scripts')
<script>
    function previewAdminPhoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('photoPreview');
            output.src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
