@extends('layouts.provider-dashboard')

@section('title', 'Request Detail #'. $serviceRequest->id)
@section('page-title', 'Request Details')

@push('styles')
<style>
    /* Using styles from client.dashboard.service-request-detail as a base and adapting if needed */
    .detail-card {
        background-color: var(--card-bg-light);
        padding: 1.5rem 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }
    body.dark-mode .detail-card {
        background-color: var(--card-bg-dark);
    }
    .detail-header {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .detail-header {
        border-bottom-color: var(--border-color-dark);
    }
    .detail-item { margin-bottom: 0.75rem; display: flex; flex-wrap: wrap; }
    .detail-label { font-weight: 500; color: var(--text-muted-light, #6B7280); width: 150px; flex-shrink: 0; }
    body.dark-mode .detail-label { color: var(--text-muted-dark, #9CA3AF); }
    .detail-value { color: var(--text-dark); flex-grow: 1; }
    body.dark-mode .detail-value { color: var(--text-light); }

    .status-badge { padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; display: inline-block; text-transform: capitalize; }
    .status-pending { background-color: #FEF3C7; color: #92400E; } body.dark-mode .status-pending { background-color: #78350F; color: #FDE68A; }
    .status-accepted { background-color: #D1FAE5; color: #065F46; } body.dark-mode .status-accepted { background-color: #064E3B; color: #A7F3D0; }
    .status-rejected { background-color: #FEE2E2; color: #991B1B; } body.dark-mode .status-rejected { background-color: #7F1D1D; color: #FECACA; }
    .status-in_progress { background-color: #DBEAFE; color: #1E40AF; } body.dark-mode .status-in_progress { background-color: #1E3A8A; color: #BFDBFE; }
    .status-completed { background-color: #C7D2FE; color: #3730A3; } body.dark-mode .status-completed { background-color: #3730A3; color: #C7D2FE; }
    .status-cancelled_by_client, .status-cancelled_by_provider { background-color: #E5E7EB; color: #4B5563; } body.dark-mode .status-cancelled_by_client, body.dark-mode .status-cancelled_by_provider { background-color: #374151; color: #D1D5DB; }
    .status-inquiry { background-color: #E0E7FF; color: #3730A3;} body.dark-mode .status-inquiry {background-color: #312E81; color: #C7D2FE;}

    .actions-and-status-update {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color-light);
        display: flex;
        flex-direction: column; /* Stack buttons and form */
        gap: 1.5rem;
    }
    body.dark-mode .actions-and-status-update { border-top-color: var(--border-color-dark); }
    .action-buttons-group { display: flex; gap: 1rem; flex-wrap:wrap; }

    .btn-action { padding: 0.6rem 1.2rem; border-radius: 0.375rem; text-decoration: none; font-weight: 500; font-size: 0.9rem; text-align: center; transition: background-color 0.2s; border: none; cursor: pointer; }
    .btn-accept-action { background-color: var(--primary-color); color: white !important; }
    .btn-accept-action:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-accept-action { background-color: var(--secondary-color); color: var(--text-dark) !important; }

    .btn-ignore-action { background-color: #F3F4F6; /* Light Gray from PDF Ignore Button */ color: var(--text-muted-light); border:1px solid #E5E7EB;}
    .btn-ignore-action:hover { background-color: #E5E7EB; }
    body.dark-mode .btn-ignore-action { background-color: #374151; color: var(--text-muted-dark); border-color:#4B5563 }

    .status-update-form { display: flex; align-items: center; gap: 0.75rem; }
    .form-select-status { padding: 0.6rem 1rem; border: 1px solid var(--border-color-light); border-radius: 0.375rem; font-size: 0.9rem; background-color: var(--content-bg-light); color: var(--text-dark); }
    body.dark-mode .form-select-status { background-color: var(--content-bg-dark); border-color: var(--border-color-dark); color: var(--text-light); }
    .btn-update-status { /* Assuming btn-secondary-action style */ background-color: var(--border-color-light); color: var(--text-dark); }
    .btn-update-status:hover { background-color: #D1D5DB; }
    body.dark-mode .btn-update-status { background-color: var(--border-color-dark); color: var(--text-light); }
</style>
@endpush

@section('content')
    <a href="{{ route('provider.requests.index') }}" class="inline-block mb-4 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to All Requests
    </a>

    <div class="detail-card">
        <h3 class="detail-header">Request Overview</h3>
        <div class="grid md:grid-cols-2 gap-x-8 gap-y-3">
            <div class="detail-item"><span class="detail-label">Request ID:</span><span class="detail-value">#{{ $serviceRequest->id }}</span></div>
            <div class="detail-item"><span class="detail-label">Status:</span><span class="detail-value"><span class="status-badge status-{{ Str::slug($serviceRequest->status, '_') }}">{{ Str::title(str_replace('_', ' ', $serviceRequest->status)) }}</span></span></div>
            <div class="detail-item"><span class="detail-label">Requested on:</span><span class="detail-value">{{ $serviceRequest->created_at->format('M d, Y \a\t h:i A') }}</span></div>
            <div class="detail-item"><span class="detail-label">Desired Date:</span><span class="detail-value">{{ $serviceRequest->desired_date_time ? \Carbon\Carbon::parse($serviceRequest->desired_date_time)->format('M d, Y, h:i A') : 'Not specified' }}</span></div>
            <div class="detail-item"><span class="detail-label">Category:</span><span class="detail-value">{{ $serviceRequest->category->name ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Service:</span><span class="detail-value">{{ $serviceRequest->service->title ?? 'General/Custom Request' }}</span></div>
            <div class="detail-item"><span class="detail-label">Client Budget:</span><span class="detail-value">{{ $serviceRequest->proposed_budget ? '$' . number_format($serviceRequest->proposed_budget, 2) : 'Not specified' }}</span></div>
            {{-- Assuming your service model might have base_price if it's a pre-defined service from provider's list --}}
            @if($serviceRequest->service && $serviceRequest->service->base_price)
            <div class="detail-item"><span class="detail-label">Your Price:</span><span class="detail-value">${{ number_format($serviceRequest->service->base_price, 2) }}</span></div>
            @endif
        </div>
        <div class="detail-item mt-3"><span class="detail-label">Full Description:</span><span class="detail-value whitespace-pre-wrap">{{ $serviceRequest->description }}</span></div>
        <div class="detail-item"><span class="detail-label">Service Location:</span><span class="detail-value">{{ $serviceRequest->address }}, {{ $serviceRequest->city }}</span></div>
    </div>

    @if($serviceRequest->client)
    <div class="detail-card">
        <h3 class="detail-header">Client Information</h3>
        <div class="grid md:grid-cols-2 gap-x-8 gap-y-3">
            <div class="detail-item"><span class="detail-label">Name:</span><span class="detail-value">{{ $serviceRequest->client->name }}</span></div>
            <div class="detail-item"><span class="detail-label">Email:</span><span class="detail-value">{{ $serviceRequest->client->email }}</span></div>
            <div class="detail-item"><span class="detail-label">Phone:</span><span class="detail-value">{{ $serviceRequest->client->phone_number ?? 'Not provided' }}</span></div>
        </div>
    </div>
    @endif

    <div class="actions-and-status-update">
        <div class="action-buttons-group">
            <a href="{{ route('provider.messages.chat', $serviceRequest) }}" class="btn-action btn-primary-action">
                <i class="fas fa-comments mr-2"></i>Chat with Client
            </a>

            {{-- Accept/Ignore Buttons for Pending/Inquiry requests --}}
            @if(in_array($serviceRequest->status, ['pending', 'inquiry']))
                <form action="{{ route('provider.requests.update-status', $serviceRequest) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="btn-action btn-accept-action">
                        <i class="fas fa-check-circle mr-2"></i>Accept
                    </button>
                </form>
                <form action="{{ route('provider.requests.update-status', $serviceRequest) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn-action btn-ignore-action">
                        <i class="fas fa-times-circle mr-2"></i>Ignore
                    </button>
                </form>
            @endif
        </div>

        {{-- Form for Provider to Update Status (e.g., to In Progress, Completed) --}}
        @if(!empty($updatableStatuses) && !in_array($serviceRequest->status, ['pending', 'inquiry', 'completed', 'rejected', 'cancelled_by_client']))
            <form action="{{ route('provider.requests.update-status', $serviceRequest) }}" method="POST" class="status-update-form items-center">
                @csrf
                @method('PATCH')
                <label for="status_update" class="form-label mb-0 mr-2">Change Status to:</label>
                <select name="status" id="status_update" class="form-select-status">
                    @foreach($updatableStatuses as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-action btn-update-status">Update Status</button>
            </form>
        @elseif($serviceRequest->status === 'completed')
             <p class="text-sm text-green-600 dark:text-green-400 font-medium"><i class="fas fa-check-circle mr-1"></i> This request is marked as completed.</p>
        @elseif(in_array($serviceRequest->status, ['rejected', 'cancelled_by_client']))
             <p class="text-sm text-red-600 dark:text-red-400 font-medium"><i class="fas fa-ban mr-1"></i> This request is {{ str_replace('_', ' ', $serviceRequest->status) }}.</p>
        @endif
    </div>

@endsection
