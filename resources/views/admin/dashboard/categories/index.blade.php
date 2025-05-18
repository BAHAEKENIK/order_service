@extends('layouts.admin-dashboard')

@section('title', 'Manage Service Categories')
@section('page-title', 'Service Categories Management')

@push('styles')
<style>
    .category-management-container {
        display: grid;
        grid-template-columns: 1fr; /* Default to 1 column */
        gap: 2rem; /* Space between table and form */
    }

    @media (min-width: 1024px) { /* lg breakpoint */
        .category-management-container {
            grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.5fr); /* Table takes more space */
        }
    }

    .content-card { /* Replaces table-container and add-category-form-container */
        background-color: var(--card-bg-light);
        border-radius: 0.5rem; /* 8px */
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); /* shadow-md */
        padding: 1.5rem; /* p-6 */
    }
    body.dark-mode .content-card {
        background-color: var(--card-bg-dark);
    }

    .content-card-header {
        font-size: 1.125rem; /* text-lg */
        font-weight: 600; /* semibold */
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .content-card-header {
        border-bottom-color: var(--border-color-dark);
    }

    .categories-table {
        width: 100%;
        border-collapse: collapse;
    }
    .categories-table th,
    .categories-table td {
        padding: 0.75rem 1rem; /* py-3 px-4 */
        text-align: left;
        border-bottom: 1px solid var(--border-color-light);
        font-size: 0.875rem; /* text-sm */
        vertical-align: middle;
    }
    body.dark-mode .categories-table th, body.dark-mode .categories-table td {
        border-bottom-color: var(--border-color-dark);
    }
    .categories-table th {
        font-weight: 600; /* semibold */
        color: var(--text-muted-light);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    body.dark-mode .categories-table th { color: var(--text-muted-dark); }

    .btn-delete-category {
        background-color: rgba(239, 68, 68, 0.1);
        color: #DC2626;
        padding: 0.3rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
    }
    .btn-delete-category:hover { background-color: #FEE2E2; color: #B91C1C; }
    body.dark-mode .btn-delete-category { background-color: rgba(248, 113, 113, 0.2); color: #FCA5A5; }
    body.dark-mode .btn-delete-category:hover { background-color: rgba(220, 38, 38, 0.4); color: #FECACA; }


    .form-label { display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.875rem; }
    .form-input, .form-textarea {
        width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--border-color-light);
        border-radius: 0.375rem; background-color: #F9FAFB; font-size: 0.875rem; color: var(--text-dark);
    }
    body.dark-mode .form-input, body.dark-mode .form-textarea {
        background-color: var(--border-color-dark); border-color: #4B5563; color: var(--text-light);
    }
    .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
    body.dark-mode .form-input:focus, body.dark-mode .form-textarea:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
    .form-textarea { min-height: 80px; }
    .btn-add-category { background-color: var(--primary-color); color: white !important; padding: 0.65rem 1.5rem; border: none; border-radius: 0.375rem; font-weight: 500; cursor: pointer; }
    .btn-add-category:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-add-category { background-color: var(--secondary-color); color: var(--text-dark) !important; }
    .pagination-links { margin-top: 1.5rem; }

    /* === MODAL STYLES - THEME AWARE (MATCHES PAGE THEME) === */
    .modal {
        display: none; position: fixed; z-index: 1050; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto;
        background-color: rgba(0,0,0,0.6); /* Semi-transparent backdrop */
        align-items:center; justify-content:center;
        transition: opacity 0.3s ease;
    }
    .modal.active { display: flex; opacity: 1; }
    .modal-content {
        background-color: var(--card-bg-light); /* Default: Light background for modal */
        color: var(--text-dark);               /* Default: Dark text for modal */
        margin: auto; padding: 2rem; border-radius: 0.5rem;
        width: 90%; max-width: 450px; position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2), 0 5px 10px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color-dark);
    }
    body.dark-mode .modal-content {
        background-color: var(--card-bg-dark); /* Dark background for modal in dark mode */
        color: var(--text-light);              /* Light text for modal in dark mode */
        border: 1px solid var(--border-color-light);
    }

    .modal-close-btn {
        color: var(--text-muted-light); position: absolute; top: 0.75rem; right: 1rem;
        font-size: 1.75rem; font-weight: bold; cursor: pointer;
    }
    .modal-close-btn:hover, .modal-close-btn:focus { color: var(--text-dark); text-decoration: none; }
    body.dark-mode .modal-close-btn { color: var(--text-muted-dark); }
    body.dark-mode .modal-close-btn:hover, body.dark-mode .modal-close-btn:focus { color: var(--text-light); }

    .modal-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
    /* Modal text will inherit from .modal-content based on the theme */
    .modal-text { font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.6; }
    .modal-text strong { font-weight: 600; }
    #deleteCategoryWarningText { font-size:0.85rem; padding: 0.5rem; border-radius: 0.25rem; margin-bottom: 1rem; }
    body:not(.dark-mode) #deleteCategoryWarningText { background-color: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; } /* amber-50, amber-700 */
    body.dark-mode #deleteCategoryWarningText { background-color: #3B322C; color: #FCD34D; border: 1px solid #78350F; } /* amber-800, amber-300 */


    .modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top:1.5rem; }
    .modal-btn { padding: 0.5rem 1rem; border-radius:0.375rem; border:none; cursor:pointer; font-weight: 500; }

    /* Cancel button: follows theme's secondary button style roughly */
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
    /* Confirm (Delete) button: danger style */
    .modal-btn-confirm { background-color: #DC2626; color: white; }
    .modal-btn-confirm:hover { background-color: #B91C1C; }
    body.dark-mode .modal-btn-confirm { /* Keep it clearly a danger action */
        background-color: #EF4444; color: white;
    }
     body.dark-mode .modal-btn-confirm:hover { background-color: #DC2626; }
    /* === END OF MODAL STYLES === */

</style>
@endpush

@section('content')
<div class="category-management-container">
    <div class="content-card">
        <h3 class="content-card-header">Existing Categories</h3>
        @if($categories->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">No categories found. Add one using the form.</p>
        @else
            <div class="overflow-x-auto">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Services</th>
                            <th>Requests</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td class="font-medium">{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ Str::limit($category->description, 40) }}</td>
                            <td>{{ $category->services_count }}</td>
                            <td>{{ $category->service_requests_count }}</td>
                            <td>
                                <button type="button" class="btn-delete-category" onclick="confirmCategoryDelete('{{ $category->id }}', '{{ $category->name }}', {{ $category->services_count + $category->service_requests_count }})">
                                    <i class="fas fa-trash-alt mr-1"></i> supprimer
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             @if($categories->hasPages())
                <div class="mt-6 pagination-links">
                    {{ $categories->links() }}
                </div>
            @endif
        @endif
    </div>

    <div class="content-card add-category-form-container">
        <h3 class="content-card-header">Add New Category</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" class="form-textarea">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <button type="submit" class="btn-add-category"><i class="fas fa-plus mr-2"></i>Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Modal -->
<div id="deleteCategoryModal" class="modal">
    <div class="modal-content"> {{-- This modal will now follow the page theme directly --}}
        <span class="modal-close-btn" onclick="closeDeleteCategoryModal()">×</span>
        <h4 class="modal-title text-red-600 dark:text-red-400">Confirm Category Deletion</h4>
        <p id="deleteCategoryModalTextMain" class="modal-text">Are you sure you want to delete the category <strong id="categoryNameToDelete" class="font-semibold"></strong>? This action cannot be undone.</p>
        <p id="deleteCategoryWarningText" class="modal-text" style="display:none;"></p>
        <form id="deleteCategoryForm" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteCategoryModal()">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-confirm">Yes, Delete Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const deleteCategoryModal = document.getElementById('deleteCategoryModal');
    const deleteCategoryForm = document.getElementById('deleteCategoryForm');
    const categoryNameToDeleteSpan = document.getElementById('categoryNameToDelete');
    const deleteCategoryWarningText = document.getElementById('deleteCategoryWarningText'); // Element for specific warning
    const deleteCategoryModalTextMain = document.getElementById('deleteCategoryModalTextMain'); // Main modal text


    function confirmCategoryDelete(categoryId, categoryName, itemCount) {
        if(deleteCategoryForm) deleteCategoryForm.action = "{{ url('admin/categories') }}/" + categoryId;
        if(categoryNameToDeleteSpan) categoryNameToDeleteSpan.textContent = categoryName;

        // Always show the base message
        if(deleteCategoryModalTextMain) deleteCategoryModalTextMain.innerHTML = `Are you sure you want to delete category <strong class="font-semibold">${categoryName}</strong>? This action cannot be undone.`;

        if (itemCount > 0) {
            if(deleteCategoryWarningText) {
                deleteCategoryWarningText.innerHTML = `Warning: This category is currently associated with <strong>${itemCount}</strong> service(s) and/or request(s). Deleting it might cause issues unless these items are reassigned or also handled.`;
                deleteCategoryWarningText.style.display = 'block';
                // Style warning text (inline for simplicity or add classes)
                deleteCategoryWarningText.style.backgroundColor = document.body.classList.contains('dark-mode') ? 'var(--sidebar-active-bg-dark)' : '#FFFBEB'; // Amber-50ish
                deleteCategoryWarningText.style.color = document.body.classList.contains('dark-mode') ? '#FCD34D': '#B45309'; // Amber-400ish / Amber-700
                deleteCategoryWarningText.style.padding = '0.5rem';
                deleteCategoryWarningText.style.borderRadius = '0.25rem';
                deleteCategoryWarningText.style.border = `1px solid ${document.body.classList.contains('dark-mode') ? '#78350F' : '#FDE68A'}`;

            }
        } else {
            if(deleteCategoryWarningText) deleteCategoryWarningText.style.display = 'none';
        }

        if(deleteCategoryModal) {
             deleteCategoryModal.style.opacity = 0; // Start faded out
            deleteCategoryModal.style.display = "flex";
            setTimeout(() => { deleteCategoryModal.style.opacity = 1; }, 10); // Trigger fade-in
        }
    }

    function closeDeleteCategoryModal() {
        if(deleteCategoryModal) {
            deleteCategoryModal.style.opacity = 0;
            setTimeout(() => { deleteCategoryModal.style.display = "none"; }, 300); // Wait for fade
        }
    }

    window.onclick = function(event) {
        if (event.target == deleteCategoryModal) {
            closeDeleteCategoryModal();
        }
    }
</script>
@endpush
