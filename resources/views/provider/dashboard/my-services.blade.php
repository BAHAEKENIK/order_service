@extends('layouts.provider-dashboard')

@section('title', 'My Services')
@section('page-title', 'My Services')

@push('styles')
<style>
    .header-actions { margin-bottom: 1.5rem; text-align: right; }
    .btn-add-service {
        background-color: var(--primary-color); color: white; padding: 0.6rem 1.2rem;
        text-decoration: none; border-radius: 0.375rem; font-weight: 500;
        display: inline-flex; align-items: center;
    }
    .btn-add-service:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-add-service { background-color: var(--secondary-color); color: var(--text-dark); }

    .services-table-container {
        background-color: var(--card-bg-light); border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06); overflow-x:auto;
    }
    body.dark-mode .services-table-container { background-color: var(--card-bg-dark); }
    .services-table { width: 100%; border-collapse: collapse; }
    .services-table th, .services-table td {
        padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color-light);
        font-size: 0.875rem;
    }
    body.dark-mode .services-table th, body.dark-mode .services-table td { border-bottom-color: var(--border-color-dark); }
    .services-table th { font-weight: 600; color: var(--text-muted-light, #6B7280); text-transform: uppercase; letter-spacing: 0.05em;}
    body.dark-mode .services-table th { color: var(--text-muted-dark, #9CA3AF); }

    .service-image-thumbnail { width: 60px; height: 40px; object-fit: cover; border-radius: 0.25rem; margin-right: 1rem; }
    .service-actions a, .service-actions button {
        margin-right: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.8rem;
        border-radius: 0.25rem; text-decoration: none;
    }
    .btn-edit { color: var(--primary-color); border: 1px solid var(--primary-color); }
    .btn-edit:hover { background-color: var(--primary-color); color:white; }
    body.dark-mode .btn-edit { color: var(--secondary-color); border-color: var(--secondary-color); }
    body.dark-mode .btn-edit:hover { background-color: var(--secondary-color); color:var(--text-dark); }

    .btn-delete { color: #EF4444; border: 1px solid #EF4444; }
    .btn-delete:hover { background-color: #EF4444; color:white; }
    body.dark-mode .btn-delete { color: #F87171; border-color: #F87171; }
    body.dark-mode .btn-delete:hover { background-color: #F87171; color:var(--text-dark); }

    .status-tag { padding: 0.2em 0.6em; border-radius: 0.25rem; font-size: 0.75rem; text-transform: capitalize; }
    .status-available { background-color: #D1FAE5; color: #065F46; } /* green */
    body.dark-mode .status-available { background-color: #064E3B; color: #A7F3D0;}
    .status-unavailable { background-color: #FEF3C7; color: #92400E; } /* amber */
    body.dark-mode .status-unavailable { background-color: #78350F; color: #FDE68A; }

    .pagination-links { margin-top: 1.5rem; }
</style>
@endpush

@section('content')
    <div class="header-actions">
        <a href="{{ route('provider.services.create') }}" class="btn-add-service">
            <i class="fas fa-plus mr-2"></i> Add New Service
        </a>
    </div>

    @if ($services->isEmpty())
        <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-lg shadow">
            <i class="fas fa-toolbox fa-3x mb-4 text-gray-400 dark:text-gray-500"></i>
            <p class="text-xl text-gray-500 dark:text-gray-400">You haven't added any services yet.</p>
            <p class="mt-2">
                <a href="{{ route('provider.services.create') }}" class="text-primary-color dark:text-secondary-color hover:underline font-medium">
                    Add your first service now!
                </a>
            </p>
        </div>
    @else
        <div class="services-table-container">
            <table class="services-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>
                                <img src="{{ $service->image_path ? Storage::url($service->image_path) : 'https://via.placeholder.com/60x40?text=No+Image' }}" alt="{{ $service->title }}" class="service-image-thumbnail">
                            </td>
                            <td>{{ $service->title }}</td>
                            <td>{{ $service->category->name ?? 'N/A' }}</td>
                            <td>{{ $service->base_price ? '$' . number_format($service->base_price, 2) : 'Not set' }}</td>
                            <td>
                                <span class="status-tag status-{{ $service->status }}">
                                    {{ $service->status }}
                                </span>
                            </td>
                            <td class="service-actions">
                                <a href="{{ route('provider.services.edit', $service) }}" class="btn-edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form action="{{ route('provider.services.destroy', $service) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this service? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($services->hasPages())
            <div class="mt-6 pagination-links">
                {{ $services->links() }}
            </div>
        @endif
    @endif
@endsection
