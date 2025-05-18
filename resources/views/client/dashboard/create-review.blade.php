@extends('layouts.client-dashboard')

@section('title', 'Review Service Request #'. $serviceRequest->id)
@section('page-title', 'Leave a Review')

@push('styles')
<style>
    .review-form-container {
        background-color: var(--card-bg-light);
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        max-width: 700px;
        margin: 0 auto;
    }
    body.dark-mode .review-form-container {
        background-color: var(--card-bg-dark);
    }
    .form-label { display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 0.5rem; }
    .form-textarea {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        background-color: #F3F4F6;
        color: var(--text-dark);
        min-height: 120px;
    }
    body.dark-mode .form-textarea { background-color: #374151; border-color: var(--border-color-dark); color: var(--text-light); }
    .form-textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
    body.dark-mode .form-textarea:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
    .form-group { margin-bottom: 1.5rem; }

    .star-rating { display: flex; justify-content: center; margin-bottom: 1.5rem; }
    .star-rating input[type="radio"] { display: none; }
    .star-rating label {
        font-size: 2rem;
        color: var(--text-muted-light, #CBD5E0);
        cursor: pointer;
        padding: 0 0.2rem;
        transition: color 0.2s;
    }
    body.dark-mode .star-rating label { color: var(--text-muted-dark, #4A5568); }
    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #F59E0B; /* Amber-500 */
    }
    .star-rating input[type="radio"]:not(:checked) ~ label:hover,
    .star-rating input[type="radio"]:not(:checked) ~ label:hover ~ label {
      color: #F59E0B;
    }
    .btn-submit-review { background-color: var(--primary-color); color: white !important; padding: 0.6rem 1.5rem; border-radius: 0.375rem; border:none; font-weight:500; cursor:pointer; }
    .btn-submit-review:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-submit-review { background-color: var(--secondary-color); color: var(--text-dark) !important; }

</style>
@endpush

@section('content')
    <a href="{{ route('client.requests.detail', $serviceRequest) }}" class="inline-block mb-4 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to Request Details
    </a>

    <div class="review-form-container">
        <h3 class="text-xl font-semibold mb-1">Review for: <span class="text-primary-color dark:text-secondary-color">{{ $serviceRequest->service->title ?? $serviceRequest->category->name.' Request' }}</span></h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Provided by: {{ $serviceRequest->provider->name }}</p>

        <form action="{{ route('client.review.store', $serviceRequest) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="rating" class="form-label text-center">Your Rating <span class="text-red-500">*</span></label>
                <div class="star-rating flex-row-reverse justify-center"> {{-- flex-row-reverse for CSS hover trick --}}
                    <input type="radio" id="star5" name="rating" value="5" {{ old('rating') == 5 ? 'checked' : '' }} required /><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4" {{ old('rating') == 4 ? 'checked' : '' }} /><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3" {{ old('rating') == 3 ? 'checked' : '' }} /><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2" {{ old('rating') == 2 ? 'checked' : '' }} /><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1" {{ old('rating') == 1 ? 'checked' : '' }} /><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                </div>
                @error('rating') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="comment" class="form-label">Your Comment (Optional)</label>
                <textarea name="comment" id="comment" class="form-textarea" placeholder="Share your experience...">{{ old('comment') }}</textarea>
                @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 text-right">
                <button type="submit" class="btn-submit-review">Submit Review</button>
            </div>
        </form>
    </div>
@endsection
