@extends('layouts.provider-dashboard')

@section('title', 'Incoming Requests')
@section('page-title', 'Requests')

@push('styles')
<style>
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }
    .filter-controls {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .btn-filter, .status-select {
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        background-color: var(--content-bg-light);
        color: var(--text-dark);
        cursor: pointer;
    }
    body.dark-mode .btn-filter, body.dark-mode .status-select {
        background-color: var(--content-bg-dark);
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    .btn-filter i { margin-right: 0.5rem; }

    .request-card {
        background-color: var(--card-bg-light);
        border-radius: 0.5rem; /* 8px from PDF */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Softer shadow from PDF */
        margin-bottom: 1.5rem;
        /* padding: 1.5rem; */ /* Padding will be handled by sections */
        overflow: hidden; /* To ensure rounded corners */
        display: block; /* Make the whole card clickable */
        text-decoration: none;
        color: inherit;
        transition: box-shadow 0.2s ease-out;
    }
    .request-card:hover {
         box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    body.dark-mode .request-card {
        background-color: var(--card-bg-dark);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    body.dark-mode .request-card:hover {
         box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }


    .request-card-content {
        padding: 1.5rem;
    }
    .request-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .client-info { display: flex; align-items: center; }
    .client-avatar-indicator { /* Green dot in PDF */
        width: 8px; height: 8px; background-color: #34D399; /* Tailwind green-400 */
        border-radius: 50%; margin-right: 0.5rem;
    }
    .client-name { font-weight: 600; font-size:0.95rem; }
    .request-date { font-size: 0.8rem; color: var(--text-muted-light, #6B7280); }
    body.dark-mode .request-date { color: var(--text-muted-dark, #9CA3AF); }

    .request-title {
        font-weight: 500; /* Medium */
        font-size: 1rem; /* 16px */
        margin-bottom: 0.5rem;
    }
    .request-description {
        font-size: 0.875rem; /* 14px */
        color: var(--text-muted-light, #6B7280);
        line-height: 1.6;
        margin-bottom: 1rem;
        /* Clamp to 2 lines for preview */
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    body.dark-mode .request-description { color: var(--text-muted-dark, #9CA3AF); }

    .request-tags { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
    .tag {
        background-color: #F3F4F6; /* Tailwind gray-100 */
        color: var(--text-muted-light, #4B5563); /* Tailwind gray-600 */
        padding: 0.25rem 0.75rem;
        border-radius: 9999px; /* Pill */
        font-size: 0.75rem; /* 12px */
        font-weight: 500;
    }
    body.dark-mode .tag {
        background-color: #374151; /* Tailwind gray-700 */
        color: var(--text-muted-dark, #D1D5DB); /* Tailwind gray-300 */
    }
    .tag.urgent { background-color: #FEE2E2; color: #B91C1C; } /* Reddish */
    body.dark-mode .tag.urgent { background-color: #7F1D1D; color: #FECACA; }


    .request-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end; /* Align buttons to the right */
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color-light);
        background-color: var(--sidebar-bg-light); /* Light background for action bar */
    }
    body.dark-mode .request-actions {
        border-top-color: var(--border-color-dark);
        background-color: var(--sidebar-bg-dark);
    }

    .btn-request-action {
        padding: 0.5rem 1.25rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .btn-accept { background-color: var(--primary-color); color: white; border-color: var(--primary-color); }
    .btn-accept:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-accept { background-color: var(--secondary-color); color: var(--text-dark); border-color:var(--secondary-color)}

    .btn-ignore { background-color: #E5E7EB; color: var(--text-dark); border-color: #D1D5DB;}
    .btn-ignore:hover { background-color: #D1D5DB; }
    body.dark-mode .btn-ignore { background-color: #374151; color: var(--text-light); border-color:#4B5563}

    .pagination-links { margin-top: 2rem; } /* Styles are in client-dashboard.blade.php layout or app.css */

</style>
@endpush

@section('content')
    <div class="filter-bar">
        <form method="GET" action="{{ route('provider.requests.index') }}" class="filter-controls">
            <button type="button" class="btn-filter" onclick="this.form.submit()"> {{-- Simplified filter trigger for now --}}
                <i class="fas fa-filter"></i> Filter
            </button>
            <select name="filter_status" class="status-select" onchange="this.form.submit()">
                @foreach($statuses as $statusValue)
                    <option value="{{ $statusValue }}" {{ request('filter_status', 'pending') == $statusValue ? 'selected' : '' }}>
                        {{ Str::title(str_replace('_', ' ', $statusValue)) }}
                    </option>
                @endforeach
            </select>
            {{-- Add more filters here (date range, client name, etc.) --}}
        </form>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $totalOpportunities ?? 0 }} New Opportunit{{ $totalOpportunities == 1 ? 'y' : 'ies' }}</span>
    </div>

    @if($serviceRequests->isEmpty())
        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
            <i class="fas fa-envelope-open fa-3x mb-4"></i>
            <p class="text-xl">No service requests found{{ request('filter_status') && request('filter_status') !== 'all' ? ' matching this status' : '' }}.</p>
        </div>
    @else
        @foreach ($serviceRequests as $request)
            <a href="{{ route('provider.requests.detail', $request) }}" class="request-card">
                <div class="request-card-content">
                    <div class="request-card-header">
                        <div class="client-info">
                            <span class="client-avatar-indicator"></span> {{-- Green dot --}}
                            <span class="client-name">{{ $request->client->name ?? 'Unknown Client' }}</span>
                        </div>
                        <span class="request-date">{{ $request->created_at->format('d M') }}</span> {{-- e.g., 16 Avr --}}
                    </div>
                    <h3 class="request-title">
                        {{ $request->service ? $request->service->title : Str::words($request->description, 5) }} - {{ $request->city ?? 'Location not set' }} - {{ $request->desired_date_time ? \Carbon\Carbon::parse($request->desired_date_time)->format('d M') : '' }}
                    </h3>
                    <p class="request-description">{{ Str::limit($request->description, 150) }}</p>
                    <div class="request-tags">
                        {{-- Placeholder tags based on your example PDF. You'll need to generate these from data if applicable. --}}
                        {{-- For example, from service attributes, client's urgency preference, etc. --}}
                         @if(Str::contains(strtolower($request->description), 'urgent')) <span class="tag urgent">Urgent</span> @endif
                        <span class="tag">{{ $request->category->name ?? 'General' }}</span>
                         @if($request->proposed_budget) <span class="tag">${{ number_format($request->proposed_budget) }} Budget</span>@endif
                        {{-- <span class="tag">4+1 room</span>
                        <span class="tag">1 bath</span>
                        <span class="tag">Other</span> --}}
                    </div>
                </div>
                 @if(in_array($request->status, ['pending', 'inquiry']))
                <div class="request-actions">
                    <form action="{{ route('provider.requests.update-status', $request) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn-request-action btn-accept">Accepter</button>
                    </form>
                    <form action="{{ route('provider.requests.update-status', $request) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn-request-action btn-ignore">Ignore</button>
                    </form>
                </div>
                @endif
            </a>
        @endforeach

        @if($serviceRequests->hasPages())
            <div class="mt-8 pagination-links">
                {{ $serviceRequests->appends(request()->query())->links() }} {{-- Ensure filters are kept during pagination --}}
            </div>
        @endif
    @endif
@endsection
