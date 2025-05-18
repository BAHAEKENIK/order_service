@extends('layouts.client-dashboard')

@section('title', 'My Service Requests')
@section('header-title', 'My Requests')

@push('styles')
<style>
    .table-wrapper {
        background-color: var(--sidebar-bg-light); /* Matches sidebar/header bg */
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow-x: auto; /* For responsive table */
    }
    body.dark-mode .table-wrapper {
        background-color: var(--sidebar-bg-dark);
    }
    .requests-table {
        width: 100%;
        border-collapse: collapse;
    }
    .requests-table th, .requests-table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-light);
        font-size: 0.875rem;
    }
    body.dark-mode .requests-table th, body.dark-mode .requests-table td {
        border-bottom-color: var(--border-dark);
    }
    .requests-table th {
        font-weight: 600;
        color: var(--text-muted-light);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background-color: var(--hover-bg-light); /* Slightly different for header */
    }
    body.dark-mode .requests-table th {
        color: var(--text-muted-dark);
        background-color: var(--hover-bg-dark);
    }
    .status-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 9999px; /* pill shape */
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    .status-sent { background-color: #DBEAFE; color: #1E40AF; } /* blue-100, blue-700 */
    body.dark-mode .status-sent { background-color: #1E3A8A; color: #BFDBFE; } /* blue-800, blue-200 */
    .status-accepted { background-color: #D1FAE5; color: #065F46; } /* green-100, green-700 */
    body.dark-mode .status-accepted { background-color: #065F46; color: #A7F3D0; } /* green-800, green-200 */
    .status-rejected { background-color: #FEE2E2; color: #991B1B; } /* red-100, red-700 */
    body.dark-mode .status-rejected { background-color: #991B1B; color: #FECACA; } /* red-800, red-200 */
    .status-pending { background-color: #FEF3C7; color: #92400E; } /* amber-100, amber-700 */
    body.dark-mode .status-pending { background-color: #92400E; color: #FDE68A; }
    .status-in_progress { background-color: #E0E7FF; color: #3730A3; } /* indigo-100, indigo-700 */
    body.dark-mode .status-in_progress { background-color: #3730A3; color: #C7D2FE; }
    .status-completed { background-color: #E5E7EB; color: #374151; } /* gray-200, gray-700 */
    body.dark-mode .status-completed { background-color: #4B5563; color: #D1D5DB; }

    .action-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }
    .action-link:hover { text-decoration: underline; }
    body.dark-mode .action-link { color: var(--secondary-color); }

    .pagination-links nav { /* Style Laravel's default pagination */
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }
    .pagination-links .pagination { /* ul element */
        display: flex;
        list-style: none;
        padding: 0;
        border-radius: 0.375rem;
        overflow: hidden; /* to make border-radius work on first/last items */
    }
     .pagination-links .page-item .page-link,
     .pagination-links .page-item span.page-link {
        padding: 0.5rem 0.85rem;
        margin: 0;
        border: 1px solid var(--border-light);
        color: var(--primary-color);
        background-color: var(--sidebar-bg-light);
        text-decoration: none;
        font-size: 0.875rem;
    }
    body.dark-mode .pagination-links .page-item .page-link,
    body.dark-mode .pagination-links .page-item span.page-link {
        border-color: var(--border-dark);
        color: var(--secondary-color);
        background-color: var(--sidebar-bg-dark);
    }
    .pagination-links .page-item:not(:first-child) .page-link,
    .pagination-links .page-item:not(:first-child) span.page-link {
        border-left: none;
    }
    .pagination-links .page-item.active .page-link,
    .pagination-links .page-item.active span.page-link {
        background-color: var(--primary-color);
        color: var(--text-light);
        border-color: var(--primary-color);
        z-index: 1;
    }
    body.dark-mode .pagination-links .page-item.active .page-link,
    body.dark-mode .pagination-links .page-item.active span.page-link {
        background-color: var(--secondary-color);
        color: var(--text-dark);
        border-color: var(--secondary-color);
    }
    .pagination-links .page-item.disabled .page-link,
    .pagination-links .page-item.disabled span.page-link {
        color: var(--text-muted-light);
        background-color: var(--hover-bg-light);
        pointer-events: none;
    }
    body.dark-mode .pagination-links .page-item.disabled .page-link,
    body.dark-mode .pagination-links .page-item.disabled span.page-link {
         color: var(--text-muted-dark);
         background-color: var(--hover-bg-dark);
    }
     .pagination-links .page-item:hover:not(.active):not(.disabled) .page-link {
        background-color: var(--hover-bg-light);
    }
    body.dark-mode .pagination-links .page-item:hover:not(.active):not(.disabled) .page-link {
        background-color: var(--hover-bg-dark);
    }

</style>
@endpush

@section('content')
<div class="table-wrapper">
    @if($serviceRequests->isEmpty())
        <div class="text-center p-10">
            <i class="fas fa-folder-open fa-3x text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-lg text-gray-600 dark:text-gray-400">You haven't made any requests yet.</p>
            <a href="{{ route('client.request.make') }}" class="mt-4 inline-block px-6 py-2 text-sm font-medium leading-6 text-center text-white uppercase transition bg-blue-600 rounded shadow ripple hover:shadow-lg hover:bg-blue-700 focus:outline-none">
                Make Your First Request
            </a>
        </div>
    @else
    <table class="requests-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Service</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Professional</th>
                <th>Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($serviceRequests as $index => $request)
            <tr>
                <td>{{ $serviceRequests->firstItem() + $index }}</td>
                <td>
                    {{ $request->service->title ?? ($request->category->name ?? 'N/A') }}
                    @if($request->service && $request->category && $request->service->category_id !== $request->category_id)
                        <small class="block text-xs text-gray-500">(Category: {{ $request->category->name }})</small>
                    @elseif(!$request->service && $request->category)
                         <small class="block text-xs text-gray-500">(General {{ $request->category->name }} Request)</small>
                    @endif
                </td>
                <td>{{ $request->created_at->format('M d, Y') }}</td>
                <td>
                    @php
                        $statusClass = '';
                        switch ($request->status) {
                            case 'sent':
                            case 'pending':
                                $statusClass = 'status-pending'; break;
                            case 'accepted':
                                $statusClass = 'status-accepted'; break;
                            case 'rejected':
                            case 'cancelled_by_client':
                            case 'cancelled_by_provider':
                                $statusClass = 'status-rejected'; break;
                            case 'in_progress':
                                $statusClass = 'status-in_progress'; break;
                            case 'completed':
                                $statusClass = 'status-completed'; break;
                            default:
                                $statusClass = 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300';
                        }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ str_replace('_', ' ', $request->status) }}
                    </span>
                </td>
                <td>
                    {{ $request->provider->name ?? 'N/A' }}
                    @if($request->provider && $request->provider->providerDetail && $request->provider->providerDetail->company_name)
                       <small class="block text-xs text-gray-500"> {{ $request->provider->providerDetail->company_name }}</small>
                    @elseif($request->provider)
                         <small class="block text-xs text-gray-500">{{ Str::studly($request->provider->services->first()->category->name ?? 'Provider') }}</small> {{-- Display provider's main service category or just "Provider" --}}
                    @endif
                </td>
                <td>${{ number_format($request->proposed_budget ?? ($request->service->base_price ?? 0), 2) }}</td>
                <td>
                    <a href="{{ route('client.requests.detail', $request) }}" class="action-link">Detail</a>
                    {{-- You can add more actions here, like "Cancel" if status allows --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if($serviceRequests->hasPages())
    <div class="pagination-links mt-6">
        {{ $serviceRequests->links() }}
    </div>
@endif

@endsection
