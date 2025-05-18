@extends('layouts.client-dashboard')

@section('title', 'Service Request #'. $serviceRequest->id)
@section('page-title', 'Request Details')

@push('styles')
<style>
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
        font-size: 1.25rem; /* text-xl */
        font-weight: 600; /* semibold */
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .detail-header {
        border-bottom-color: var(--border-color-dark);
    }
    .detail-item {
        margin-bottom: 0.75rem;
        display: flex;
        flex-wrap: wrap;
    }
    .detail-label {
        font-weight: 500;
        color: var(--text-muted-light, #6B7280);
        width: 150px;
        flex-shrink: 0;
    }
    body.dark-mode .detail-label {
        color: var(--text-muted-dark, #9CA3AF);
    }
    .detail-value {
        color: var(--text-dark);
        flex-grow: 1;
    }
    body.dark-mode .detail-value {
        color: var(--text-light);
    }

    .status-badge { padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; display: inline-block; text-transform: capitalize; }
    .status-pending { background-color: #FEF3C7; color: #92400E; } body.dark-mode .status-pending { background-color: #78350F; color: #FDE68A; }
    .status-accepted { background-color: #D1FAE5; color: #065F46; } body.dark-mode .status-accepted { background-color: #064E3B; color: #A7F3D0; }
    .status-rejected { background-color: #FEE2E2; color: #991B1B; } body.dark-mode .status-rejected { background-color: #7F1D1D; color: #FECACA; }
    .status-in_progress { background-color: #DBEAFE; color: #1E40AF; } body.dark-mode .status-in_progress { background-color: #1E3A8A; color: #BFDBFE; }
    .status-completed { background-color: #C7D2FE; color: #3730A3; } body.dark-mode .status-completed { background-color: #3730A3; color: #C7D2FE; } /* Adjusted completed for dark mode */
    .status-cancelled_by_client, .status-cancelled_by_provider { background-color: #E5E7EB; color: #4B5563; } body.dark-mode .status-cancelled_by_client, body.dark-mode .status-cancelled_by_provider { background-color: #374151; color: #D1D5DB; }
    .status-inquiry { background-color: #E0E7FF; color: #3730A3;} body.dark-mode .status-inquiry {background-color: #312E81; color: #C7D2FE;}


    .action-buttons, .status-update-form-container { /* Changed from status-update-form for clarity */
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color-light);
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center; /* Align items for status update form */
    }
    body.dark-mode .action-buttons, body.dark-mode .status-update-form-container {
        border-top-color: var(--border-color-dark);
    }
    .btn-action, .btn-update-status {
        padding: 0.6rem 1.2rem; border-radius: 0.375rem; text-decoration: none; font-weight: 500; font-size: 0.9rem; text-align: center; transition: background-color 0.2s; border: none; cursor: pointer;
    }
    .btn-primary-action { background-color: var(--primary-color); color: white !important; }
    .btn-primary-action:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-primary-action { background-color: var(--secondary-color); color: var(--text-dark) !important; }
    .btn-secondary-action { background-color: var(--border-color-light); color: var(--text-dark); }
    .btn-secondary-action:hover { background-color: #D1D5DB; }
    body.dark-mode .btn-secondary-action { background-color: var(--border-color-dark); color: var(--text-light); }
    .btn-danger-action { background-color: #EF4444; color: white; }
    .btn-danger-action:hover { background-color: #DC2626; }
    body.dark-mode .btn-danger-action { background-color: #F87171; color: var(--text-dark); }

    .form-select-status {
        padding: 0.6rem 1rem; border: 1px solid var(--border-color-light); border-radius: 0.375rem; font-size: 0.9rem; background-color: var(--content-bg-light); color: var(--text-dark); margin-right: 0.5rem;
    }
    body.dark-mode .form-select-status { background-color: var(--content-bg-dark); border-color: var(--border-color-dark); color: var(--text-light); }
    .review-display-card { margin-top: 1.5rem; }
    .review-stars { font-size: 1rem; color: #F59E0B; }

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: var(--card-bg-light); margin: 10% auto; padding: 2rem; border-radius: 0.5rem; width: 90%; max-width: 450px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
    body.dark-mode .modal-content { background-color: var(--card-bg-dark); }
    .modal-close-btn { color: #aaa; position: absolute; top: 10px; right: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
    body.dark-mode .modal-close-btn { color: #777; }
    .modal-close-btn:hover, .modal-close-btn:focus { color: var(--text-dark); text-decoration: none; }
    body.dark-mode .modal-close-btn:hover, body.dark-mode .modal-close-btn:focus { color: var(--text-light); }
    .modal-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
    .modal-text { font-size: 0.9rem; margin-bottom: 1.5rem; color: var(--text-muted-light, #6B7280); }
    body.dark-mode .modal-text { color: var(--text-muted-dark, #9CA3AF); }
    .modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top:1.5rem; }
    .modal-btn { padding: 0.5rem 1rem; border-radius:0.375rem; border:none; cursor:pointer; font-weight: 500; }
    .modal-btn-cancel { background-color: var(--border-color-light); color: var(--text-dark); }
    body.dark-mode .modal-btn-cancel { background-color: var(--border-color-dark); color: var(--text-light); }
    .modal-btn-confirm { background-color: #EF4444; color: white; }
    body.dark-mode .modal-btn-confirm { background-color: #F87171; color: var(--text-dark); }

</style>
@endpush

@section('content')
    <a href="{{ route('client.requests.my') }}" class="inline-block mb-4 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to My Requests
    </a>

    <div class="detail-card">
        <h3 class="detail-header">Request Overview</h3>
        <div class="grid md:grid-cols-2 gap-x-8 gap-y-3">
            <div class="detail-item"><span class="detail-label">Request ID:</span><span class="detail-value">#{{ $serviceRequest->id }}</span></div>
            <div class="detail-item"><span class="detail-label">Status:</span><span class="detail-value"><span class="status-badge status-{{ Str::slug($serviceRequest->status, '_') }}">{{ Str::replace('_', ' ', $serviceRequest->status) }}</span></span></div>
            <div class="detail-item"><span class="detail-label">Requested on:</span><span class="detail-value">{{ $serviceRequest->created_at->format('M d, Y \a\t h:i A') }}</span></div>
            <div class="detail-item"><span class="detail-label">Desired Date:</span><span class="detail-value">{{ $serviceRequest->desired_date_time ? \Carbon\Carbon::parse($serviceRequest->desired_date_time)->format('M d, Y, h:i A') : 'Not specified' }}</span></div>
            <div class="detail-item"><span class="detail-label">Category:</span><span class="detail-value">{{ $serviceRequest->category->name ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Service:</span><span class="detail-value">{{ $serviceRequest->service->title ?? 'General Inquiry / Custom' }}</span></div>
            <div class="detail-item"><span class="detail-label">Proposed Budget:</span><span class="detail-value">{{ $serviceRequest->proposed_budget ? '$' . number_format($serviceRequest->proposed_budget, 2) : 'Not specified' }}</span></div>
            <div class="detail-item"><span class="detail-label">Cost (Est.):</span><span class="detail-value">{{ $serviceRequest->service && $serviceRequest->service->base_price ? '$' . number_format($serviceRequest->service->base_price, 2) : ($serviceRequest->proposed_budget ? '(Client proposed)' : 'To be determined') }}</span></div>
        </div>
        <div class="detail-item mt-3"><span class="detail-label">Description:</span><span class="detail-value whitespace-pre-wrap">{{ $serviceRequest->description }}</span></div>
        <div class="detail-item"><span class="detail-label">Service Location:</span><span class="detail-value">{{ $serviceRequest->address }}, {{ $serviceRequest->city }}</span></div>
    </div>

    @if($serviceRequest->provider)
    <div class="detail-card">
        <h3 class="detail-header">Provider Information</h3>
        <div class="detail-item"><span class="detail-label">Name:</span><span class="detail-value">{{ $serviceRequest->provider->name }}</span></div>
        @if($serviceRequest->provider->providerDetail)
        <div class="detail-item"><span class="detail-label">About:</span><span class="detail-value whitespace-pre-wrap">{{ $serviceRequest->provider->providerDetail->professional_description ?? 'Not available.' }}</span></div>
        <div class="detail-item"><span class="detail-label">Rating:</span><span class="detail-value">{{ number_format($serviceRequest->provider->providerDetail->average_rating ?? 0, 1) }}/5.0 (@if($serviceRequest->provider->reviewsReceived()->count() > 0){{ $serviceRequest->provider->reviewsReceived()->count() }} review{{ $serviceRequest->provider->reviewsReceived()->count() > 1 ? 's' : '' }}@else No reviews yet @endif)</span></div>
        @endif
    </div>
    @endif

    @if($serviceRequest->review)
    <div class="detail-card review-display-card">
        <h3 class="detail-header">Your Review</h3>
        <div class="detail-item"><span class="detail-label">Rating:</span><span class="detail-value review-stars">@for ($i = 1; $i <= 5; $i++)<i class="fa-star {{ $i <= $serviceRequest->review->rating ? 'fas' : 'far' }}"></i>@endfor</span></div>
        @if($serviceRequest->review->comment)<div class="detail-item"><span class="detail-label">Comment:</span><span class="detail-value whitespace-pre-wrap">{{ $serviceRequest->review->comment }}</span></div>@endif
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Reviewed on: {{ $serviceRequest->review->created_at->format('M d, Y') }}</p>
    </div>
    @endif

    <div class="action-buttons">
        <a href="{{ route('client.messages.chat', $serviceRequest) }}" class="btn-action btn-primary-action">
            <i class="fas fa-comments mr-2"></i>View Conversation
        </a>

        @if($serviceRequest->status === 'completed' && !$serviceRequest->review()->exists())
            <a href="{{ route('client.review.create', $serviceRequest) }}" class="btn-action btn-secondary-action">
                <i class="fas fa-star mr-2"></i>Leave a Review
            </a>
        @endif

        @if(in_array($serviceRequest->status, $cancellableStatuses ?? ['pending', 'accepted', 'inquiry']))
            <button type="button" class="btn-action btn-danger-action" onclick="openCancelModal()">
                <i class="fas fa-times-circle mr-2"></i>Cancel Request
            </button>
        @endif
    </div>

    {{-- Client Update Status Form (if client can mark as completed) --}}
    @if(in_array($serviceRequest->status, $completableStatuses ?? ['accepted', 'in_progress']))
        <div class="status-update-form-container"> {{-- Renamed class --}}
            <form action="{{ route('client.request.update-status', $serviceRequest) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <label for="status_update" class="form-label mb-0">Service Status:</label>
                <select name="status" id="status_update" class="form-select-status">
                    <option value="completed" {{ old('status', $serviceRequest->status) == 'completed' ? 'selected' : '' }}>Mark as Completed</option>
                    {{-- Add other statuses if client should be able to set them --}}
                </select>
                <button type="submit" class="btn-action btn-secondary-action">Update</button>
            </form>
        </div>
    @endif

    <!-- Cancel Request Modal -->
    <div id="cancelRequestModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn" onclick="closeCancelModal()">×</span>
            <h4 class="modal-title">Confirm Cancellation</h4>
            <p class="modal-text">Are you sure you want to cancel this service request? This action cannot be undone.</p>
            <form id="cancelRequestForm" action="{{ route('client.request.cancel', $serviceRequest) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeCancelModal()">Keep Request</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Yes, Cancel Request</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const cancelModal = document.getElementById('cancelRequestModal');
    function openCancelModal() {
        if(cancelModal) cancelModal.style.display = "block";
    }
    function closeCancelModal() {
        if(cancelModal) cancelModal.style.display = "none";
    }
    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target == cancelModal) {
            closeCancelModal();
        }
    }
</script>
@endpush
