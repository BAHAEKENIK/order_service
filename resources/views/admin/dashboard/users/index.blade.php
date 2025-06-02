@extends('layouts.admin-dashboard')

@section('title', 'Manage Users')
@section('page-title', 'User Management')

@push('styles')
<style>
    .stats-overview-admin { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card-admin { background-color: var(--card-bg-light); padding: 1.25rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; }
    body.dark-mode .stat-card-admin { background-color: var(--card-bg-dark); }
    .stat-icon-admin { font-size: 1.5rem; margin-right: 1rem; color: var(--primary-color); width:40px; height:40px; display:flex; align-items:center; justify-content:center; background-color:rgba(74, 85, 162, 0.1); border-radius:50%;}
    body.dark-mode .stat-icon-admin { color: var(--secondary-color); background-color:rgba(120, 149, 203, 0.15); }
    .stat-card-admin .stat-number { font-size: 1.5rem; font-weight: bold; }
    .stat-card-admin .stat-label { font-size: 0.8rem; color: var(--text-muted-light); }
    body.dark-mode .stat-card-admin .stat-label { color: var(--text-muted-dark); }

    .user-filters-and-actions { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem;}
    .user-filters { display: flex; gap: 1rem; align-items: center; }
    .form-select-filter, .form-search-input { padding: 0.6rem 1rem; border: 1px solid var(--border-color-light); border-radius: 0.375rem; background-color: var(--card-bg-light); font-size: 0.875rem; color:var(--text-dark); }
    body.dark-mode .form-select-filter, body.dark-mode .form-search-input { background-color: var(--card-bg-dark); border-color: var(--border-color-dark); color: var(--text-light); }
    .form-search-container { display: flex; }
    .form-search-input { border-top-right-radius: 0; border-bottom-right-radius: 0; width: 250px; }
    .btn-search-filter { padding: 0.6rem 1rem; background-color: var(--primary-color); color: white; border: 1px solid var(--primary-color); border-top-left-radius:0; border-bottom-left-radius:0; cursor:pointer; }
    body.dark-mode .btn-search-filter { background-color: var(--secondary-color); color:var(--text-dark); border-color: var(--secondary-color);}
    .btn-bulk-delete { background-color: #DC2626; color: white; padding: 0.6rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border:none; cursor: pointer; }
    .btn-bulk-delete:hover { background-color: #B91C1C; }
    body.dark-mode .btn-bulk-delete { background-color: #F87171; color:var(--text-dark); }

    .users-table-container { background-color: var(--card-bg-light); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;}
    body.dark-mode .users-table-container { background-color: var(--card-bg-dark); }
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table th, .users-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color-light); font-size: 0.875rem; }
    body.dark-mode .users-table th, body.dark-mode .users-table td { border-bottom-color: var(--border-color-dark); }
    .users-table th { font-weight: 600; color: var(--text-muted-light); text-transform: uppercase; letter-spacing: 0.05em; }
    body.dark-mode .users-table th { color: var(--text-muted-dark); }
    .users-table td img.avatar-sm { width:32px; height:32px; border-radius:50%; margin-right:0.75rem; vertical-align: middle; object-fit:cover;}
    .users-table .user-actions a, .users-table .user-actions button {
        padding: 0.3rem 0.6rem; font-size: 0.75rem; border-radius: 0.25rem; text-decoration: none;
        margin-right: 0.25rem; margin-bottom: 0.25rem; border: 1px solid transparent; cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.25rem;
    }
    .btn-action-delete { background-color: #FEF2F2; color: #DC2626; border-color: #FEE2E2;} body.dark-mode .btn-action-delete { background-color: #450a0a; color: #FCA5A5; border-color:#7f1d1d;}
    .btn-action-view { background-color: #EFF6FF; color: #2563EB; border-color:#DBEAFE; } body.dark-mode .btn-action-view { background-color: #1E3A8A; color: #BFDBFE; border-color:#3B82F6;}
    .btn-action-chat { background-color: #F0FDF4; color: #16A34A; border-color:#DCFCE7; } body.dark-mode .btn-action-chat { background-color: #14532D; color: #86EFAC; border-color:#22C55E;}
    .pagination-links { margin-top: 1.5rem; }

    .modal {
        display: none; position: fixed; z-index: 1050; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto;
        background-color: rgba(0,0,0,0.6);
        align-items:center; justify-content:center;
        transition: opacity 0.3s ease;
    }
    .modal.active { display: flex; opacity: 1; }
    .modal-content {
        margin: auto; padding: 2rem; border-radius: 0.5rem;
        width: 90%; max-width: 450px; position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2), 0 5px 10px rgba(0,0,0,0.1);
        background-color: var(--content-bg-light);
        color: var(--text-dark);
        border: 1px solid var(--border-color-dark);
    }
    body.dark-mode .modal-content {
        background-color: var(--content-bg-dark);
        color: var(--text-light);
        border: 1px solid var(--border-color-light);
    }

    .modal-close-btn {
        color: #aaa; position: absolute; top: 0.75rem; right: 1rem;
        font-size: 1.75rem; font-weight: bold; cursor: pointer;
    }
    .modal-close-btn:hover, .modal-close-btn:focus { color: var(--text-dark); text-decoration: none; }
    body.dark-mode .modal-close-btn { color: #777; }
    body.dark-mode .modal-close-btn:hover, body.dark-mode .modal-close-btn:focus { color: var(--text-light); }

    .modal-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
    .modal-text { font-size: 0.9rem; margin-bottom: 1.5rem; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top:1.5rem; }

    .modal-btn { padding: 0.6rem 1rem; border-radius:0.375rem; border:none; cursor:pointer; font-weight: 500; transition: background-color 0.2s, opacity 0.2s; }
    .modal-btn-cancel {
        background-color: var(--border-color-light);
        color: var(--text-dark);
        border: 1px solid var(--border-color-dark);
    }
    .modal-btn-cancel:hover { opacity:0.8; }
    body.dark-mode .modal-btn-cancel {
        background-color: var(--border-color-dark);
        color: var(--text-light);
        border: 1px solid var(--border-color-light);
    }
    .modal-btn-confirm { background-color: #DC2626; color: white; }
    .modal-btn-confirm:hover { background-color: #B91C1C; }

    .modal-form-input {
        width: 100%; padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem; font-size: 0.875rem;
        background-color: var(--card-bg-light);
        color: var(--text-dark);
    }
    body.dark-mode .modal-form-input {
        background-color: var(--card-bg-dark);
        border-color: var(--border-color-light);
        color: var(--text-light);
    }
     .modal-form-input:focus {
        outline: none; border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2);
    }
    body.dark-mode .modal-form-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2);
    }
</style>
@endpush

@section('content')
    <div class="user-filters-and-actions">
        <form method="GET" action="{{ route('admin.users.index') }}" class="user-filters">
            <div class="role-filter">
                <label for="role_filter" class="sr-only">Filter by role</label>
                <select name="role_filter" id="role_filter" class="form-select-filter" onchange="this.form.submit()">
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ request('role_filter', 'all') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-search-container">
                <label for="search_user_input" class="sr-only">Search users</label>
                <input type="text" name="search_user" id="search_user_input" class="form-search-input" placeholder="Search name or email..." value="{{ request('search_user') }}">
                <button type="submit" class="btn-search-filter" aria-label="Search"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <div class="mt-2 md:mt-0">
             @if($users->isNotEmpty() && $users->contains(fn($user) => !$user->isAdmin()))
            <button type="button" class="btn-bulk-delete" id="deleteAllUsersBtnTrigger">
                <i class="fas fa-skull-crossbones mr-1"></i> Delete ALL Non-Admin Users
            </button>
             @endif
        </div>
    </div>

    @if ($users->isEmpty())
        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
            <i class="fas fa-user-slash fa-3x mb-4"></i>
            <p class="text-xl">No users found matching your criteria.</p>
        </div>
    @else
        <form id="adminBulkUserDeleteForm" action="{{ route('admin.users.bulk-destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="users-table-container">
                <table class="users-table w-full">
                    <thead>
                        <tr>
                            <th class="w-12"><input type="checkbox" id="adminSelectAllUsersCheckbox" title="Select all users on this page"></th>
                            <th>User Info</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="admin-user-checkbox"></td>
                            <td>
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=EBF4FF&color=7F9CF5&size=32' }}" alt="{{ $user->name }}" class="avatar-sm">
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="capitalize px-2 py-0.5 text-xs rounded-full font-semibold {{ $user->role === 'client' ? 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100' : 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' }}">{{ $user->role }}</span></td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="user-actions">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-action-view" title="View Details"><i class="fas fa-eye"></i> <span class="hidden sm:inline ml-1">Consulter</span></a>
                                <a href="{{ route('admin.users.chat', $user) }}" class="btn btn-action-chat" title="Chat with User"><i class="fas fa-comments"></i> <span class="hidden sm:inline ml-1">Contacter</span></a>
                                <button type="button" class="btn btn-action-delete" onclick="confirmSingleUserDeleteByAdmin('{{ $user->id }}', '{{ $user->name }}')" title="Delete User"><i class="fas fa-trash-alt"></i> <span class="hidden sm:inline ml-1">Supprimer</span></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="mt-6 pagination-links">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
             <div class="mt-6">
                <button type="button" class="btn-bulk-delete" onclick="openAdminDeleteSelectedUsersModal()" id="adminDeleteSelectedButton" style="display:none;">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Selected Users
                </button>
            </div>
        </form>
    @endif

    <div id="adminDeleteUserModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeAdminDeleteUserModal()">×</span>
            <h4 class="modal-title">Confirm User Deletion</h4>
            <p class="modal-text">Are you sure you want to delete user <strong id="adminUserNameToDelete" class="font-semibold"></strong>? This will remove their data and cannot be undone.</p>
            <form id="adminSingleUserDeleteForm" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeAdminDeleteUserModal()">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Yes, Delete User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="adminDeleteAllUsersModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeAdminDeleteAllUsersModal()">×</span>
            <h4 class="modal-title text-red-600 dark:text-red-400">Confirm Delete ALL Non-Admin Users</h4>
            <p class="modal-text">This is a highly destructive action. Are you absolutely sure you want to delete ALL client and provider accounts? This action is irreversible. <br> <strong class="text-red-500 dark:text-red-400">Please enter your admin password to confirm.</strong></p>
            <form id="adminDeleteAllFormInternal" action="{{ route('admin.users.bulk-destroy') }}" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_all_flag" value="true">
                <div class="form-group">
                    <label for="admin_delete_all_confirm_password" class="form-label">Your Admin Password:</label>
                    <input type="password" name="delete_all_confirm_password" id="admin_delete_all_confirm_password" class="form-input modal-form-input" required>
                    @error('delete_all_confirm_password', 'deleteAllUsers')
                         <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeAdminDeleteAllUsersModal()">Cancel Action</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Yes, Delete ALL Users</button>
                </div>
            </form>
        </div>
    </div>

     <div id="adminDeleteSelectedUsersModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeAdminDeleteSelectedUsersModal()">×</span>
            <h4 class="modal-title">Confirm Delete Selected Users</h4>
            <p class="modal-text">Are you sure you want to delete the selected users? This action is irreversible.</p>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeAdminDeleteSelectedUsersModal()">Cancel</button>
                <button type="button" class="modal-btn modal-btn-confirm" onclick="submitBulkDeleteForm()">Yes, Delete Selected</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const adminDeleteUserModal = document.getElementById('adminDeleteUserModal');
    const adminSingleUserDeleteForm = document.getElementById('adminSingleUserDeleteForm');
    const adminUserNameToDeleteSpan = document.getElementById('adminUserNameToDelete');

    function confirmSingleUserDeleteByAdmin(userId, userName) {
        if(adminSingleUserDeleteForm) adminSingleUserDeleteForm.action = "{{ url('admin/users') }}/" + userId;
        if(adminUserNameToDeleteSpan) adminUserNameToDeleteSpan.textContent = userName;
        if(adminDeleteUserModal) adminDeleteUserModal.style.display = "flex";
    }
    function closeAdminDeleteUserModal() { if(adminDeleteUserModal) adminDeleteUserModal.style.display = "none"; }

    const adminDeleteAllUsersModal = document.getElementById('adminDeleteAllUsersModal');
    const deleteAllUsersBtnTrigger = document.getElementById('deleteAllUsersBtnTrigger');
    if(deleteAllUsersBtnTrigger) {
        deleteAllUsersBtnTrigger.addEventListener('click', () => {
            if(adminDeleteAllUsersModal) adminDeleteAllUsersModal.style.display = "flex";
        });
    }
    function closeAdminDeleteAllUsersModal() {
        if(adminDeleteAllUsersModal) {
            adminDeleteAllUsersModal.style.display = "none";
            const passInput = document.getElementById('admin_delete_all_confirm_password');
            if(passInput) passInput.value = '';
            const errorElements = adminDeleteAllUsersModal.querySelectorAll('p.text-red-500.text-xs.mt-1');
            errorElements.forEach(el => el.textContent = '');
        }
    }

    const adminDeleteSelectedUsersModal = document.getElementById('adminDeleteSelectedUsersModal');
    const mainBulkDeleteForm = document.getElementById('adminBulkUserDeleteForm');

    function openAdminDeleteSelectedUsersModal() {
        const checkedUsers = document.querySelectorAll('.admin-user-checkbox:checked').length;
        if(checkedUsers > 0) {
            if(adminDeleteSelectedUsersModal) adminDeleteSelectedUsersModal.style.display = "flex";
        } else {
            alert('Please select at least one user to delete.');
        }
    }
    function closeAdminDeleteSelectedUsersModal() { if(adminDeleteSelectedUsersModal) adminDeleteSelectedUsersModal.style.display = "none"; }

    function submitBulkDeleteForm() {
        let deleteAllFlagInput = mainBulkDeleteForm.querySelector('input[name="delete_all_flag"]');
        if (deleteAllFlagInput) deleteAllFlagInput.remove();
        const checkedUserIds = Array.from(document.querySelectorAll('.admin-user-checkbox:checked')).map(cb => cb.value);
        if(checkedUserIds.length > 0) {
            mainBulkDeleteForm.submit();
        } else {
            alert("No users selected for deletion.");
            closeAdminDeleteSelectedUsersModal();
        }
    }

    const adminSelectAllCheckbox = document.getElementById('adminSelectAllUsersCheckbox');
    const adminUserCheckboxes = document.querySelectorAll('.admin-user-checkbox');
    const adminDeleteSelectedButton = document.getElementById('adminDeleteSelectedButton');

    if(adminSelectAllCheckbox) {
        adminSelectAllCheckbox.addEventListener('change', function(e) {
            if(adminUserCheckboxes) adminUserCheckboxes.forEach(checkbox => checkbox.checked = e.target.checked);
            toggleAdminDeleteSelectedButton();
        });
    }
    if(adminUserCheckboxes) {
        adminUserCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleAdminDeleteSelectedButton);
        });
    }

    function toggleAdminDeleteSelectedButton() {
        if (!adminUserCheckboxes || !adminDeleteSelectedButton) return;
        const anyAdminChecked = Array.from(adminUserCheckboxes).some(cb => cb.checked);
        adminDeleteSelectedButton.style.display = anyAdminChecked ? 'inline-flex' : 'none';
    }
    toggleAdminDeleteSelectedButton();

    window.onclick = function(event) {
        if (event.target == adminDeleteUserModal) closeAdminDeleteUserModal();
        if (event.target == adminDeleteAllUsersModal) closeAdminDeleteAllUsersModal();
        if (event.target == adminDeleteSelectedUsersModal) closeAdminDeleteSelectedUsersModal();
    }

    @if(session('errors') && session('errors')->hasBag('deleteAllUsers'))
        openAdminDeleteAllUsersModal();
    @endif
</script>
@endpush
