@extends('layouts.provider-dashboard')

@section('title', 'My Client Reviews')
@section('page-title', 'My Reviews')

@push('styles')
<style>
    .review-card {
        background-color: var(--card-bg-light);
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    body.dark-mode .review-card {
        background-color: var(--card-bg-dark);
    }

    .review-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.75rem;
    }
    .review-client-name {
        font-weight: 600;
        color: var(--text-dark);
    }
    body.dark-mode .review-client-name {
        color: var(--text-light);
    }
    .review-date {
        font-size: 0.8rem;
        color: var(--text-muted-light, #6B7280);
        margin-left: auto; /* Pushes date to the right */
    }
    body.dark-mode .review-date {
        color: var(--text-muted-dark, #9CA3AF);
    }

    .review-stars {
        margin-bottom: 0.5rem;
        color: #F59E0B; /* Amber-500 for stars */
        font-size: 1rem; /* Adjust star size */
    }
    .review-stars .fa-star.empty { /* For empty stars if you implement half stars later */
        color: var(--text-muted-light, #D1D5DB);
    }
    body.dark-mode .review-stars .fa-star.empty {
        color: var(--text-muted-dark, #4B5563);
    }

    .review-comment {
        font-size: 0.9rem;
        color: var(--text-dark);
        line-height: 1.6;
        margin-bottom: 0.75rem;
        white-space: pre-wrap; /* Preserve line breaks in comment */
    }
    body.dark-mode .review-comment {
        color: var(--text-light);
    }
    .review-for-service {
        font-size: 0.8rem;
        color: var(--text-muted-light, #6B7280);
        border-top: 1px dashed var(--border-color-light);
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }
    body.dark-mode .review-for-service {
        color: var(--text-muted-dark, #9CA3AF);
        border-top-color: var(--border-color-dark);
    }
    .pagination-links { margin-top: 2rem; } /* Uses styles from layout/app.css potentially */

</style>
@endpush

@section('content')
    @if ($reviews->isEmpty())
        <div class="text-center py-10">
            <i class="fas fa-comments fa-3x mb-4 text-gray-400 dark:text-gray-500"></i>
            <p class="text-xl text-gray-500 dark:text-gray-400">You have not received any reviews yet.</p>
        </div>
    @else
        @foreach ($reviews as $review)
            <div class="review-card">
                <div class="review-header">
                    <img src="{{ $review->client->profile_photo_path ? Storage::url($review->client->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($review->client->name).'&color=7F9CF5&background=EBF4FF&size=40' }}"
                         alt="{{ $review->client->name }}" class="review-avatar">
                    <div>
                        <p class="review-client-name">{{ $review->client->name }}</p>
                        <div class="review-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-star {{ $i <= $review->rating ? 'fas' : 'far' }}"></i>
                            @endfor
                            <span class="ml-1 text-sm font-medium text-gray-600 dark:text-gray-400">({{ $review->rating }}.0)</span>
                        </div>
                    </div>
                    <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
                </div>

                @if ($review->comment)
                    <p class="review-comment">{{ $review->comment }}</p>
                @else
                    <p class="review-comment italic text-gray-400 dark:text-gray-500">No comment provided.</p>
                @endif

                @if ($review->serviceRequest)
                    <div class="review-for-service">
                        For:
                        <a href="{{ route('provider.requests.detail', $review->serviceRequest) }}" class="text-primary-color dark:text-secondary-color hover:underline font-medium">
                            {{ $review->serviceRequest->service->title ?? ($review->serviceRequest->category->name ?? 'Request #'.$review->serviceRequest->id) }}
                        </a>
                    </div>
                @endif
            </div>
        @endforeach

        @if($reviews->hasPages())
            <div class="mt-6 pagination-links">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
@endsection
