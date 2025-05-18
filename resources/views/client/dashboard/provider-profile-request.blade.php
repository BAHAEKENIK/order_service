@extends('layouts.client-dashboard')

@section('title', $provider->name . ' - Profile & Services')
@section('page-title', $provider->name)

@push('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr); /* Default to 1 column */
        gap: 2rem;
    }
    @media (min-width: 1024px) { /* lg breakpoint */
        .profile-grid {
            grid-template-columns: 300px 1fr; /* Sidebar for info, main for services/reviews */
        }
    }

    .provider-main-info-card, .provider-services-card, .provider-reviews-card {
        background-color: var(--card-bg-light);
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06);
    }
    body.dark-mode .provider-main-info-card,
    body.dark-mode .provider-services-card,
    body.dark-mode .provider-reviews-card {
        background-color: var(--card-bg-dark);
    }

    .provider-profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem auto;
        display: block;
        border: 3px solid var(--border-color-light);
    }
    body.dark-mode .provider-profile-avatar {
        border-color: var(--border-color-dark);
    }

    .provider-profile-name {
        font-size: 1.75rem; /* text-2xl */
        font-weight: 600; /* semibold */
        text-align: center;
        margin-bottom: 0.25rem;
    }
    .provider-profile-location, .provider-profile-contact {
        font-size: 0.9rem;
        color: var(--text-muted-light, #6B7280);
        text-align: center;
        margin-bottom: 0.25rem;
    }
    body.dark-mode .provider-profile-location, body.dark-mode .provider-profile-contact {
        color: var(--text-muted-dark, #9CA3AF);
    }
    .provider-profile-rating {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 0.75rem;
        margin-bottom: 1rem;
    }
    .profile-rating-value {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-right: 0.5rem;
    }
    body.dark-mode .profile-rating-value { color: var(--secondary-color); }
    .profile-rating-stars { font-size: 1rem; color: #F59E0B; /* Amber-500 */ }
    .profile-review-count { font-size: 0.8rem; color: var(--text-muted-light, #6B7280); margin-left: 0.5rem; }
    body.dark-mode .profile-review-count { color: var(--text-muted-dark, #9CA3AF); }

    .profile-description {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-dark);
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color-light);
    }
    body.dark-mode .profile-description {
        color: var(--text-light);
        border-top-color: var(--border-color-dark);
    }

    .section-heading {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .section-heading {
        border-bottom-color: var(--border-color-dark);
    }

    .service-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color-light);
    }
    .service-item:last-child { border-bottom: none; }
    body.dark-mode .service-item { border-bottom-color: var(--border-color-dark); }
    .service-info .service-title { font-weight: 500; }
    .service-info .service-category { font-size: 0.8rem; color: var(--text-muted-light, #6B7280); }
    body.dark-mode .service-info .service-category { color: var(--text-muted-dark, #9CA3AF); }
    .service-price { font-weight: 500; }
    .btn-request-service {
        background-color: var(--primary-color);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 0.25rem;
        text-decoration: none;
        font-size: 0.8rem;
    }
    .btn-request-service:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-request-service { background-color: var(--secondary-color); color: var(--text-dark); }

    .review-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color-light);
    }
    .review-item:last-child { border-bottom: none; }
    body.dark-mode .review-item { border-bottom-color: var(--border-color-dark); }
    .review-header { display: flex; align-items: center; margin-bottom: 0.5rem; }
    .review-avatar { width: 32px; height: 32px; border-radius: 50%; margin-right: 0.75rem; }
    .review-client-name { font-weight: 500; }
    .review-stars { margin-left: auto; color: #F59E0B; }
    .review-comment { font-size: 0.9rem; color: var(--text-muted-light, #6B7280); }
    body.dark-mode .review-comment { color: var(--text-muted-dark, #9CA3AF); }

    .back-link {
        display: inline-block;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }
    .back-link:hover { text-decoration: underline; }
    body.dark-mode .back-link { color: var(--secondary-color); }

    .btn-contact-provider {
        display: block;
        width: 100%;
        text-align: center;
        margin-top: 1.5rem;
        background-color: #3B82F6; /* Tailwind blue-500 */
        color: white;
        padding: 0.75rem;
        border-radius: 0.375rem;
        text-decoration: none;
        font-weight: 500;
    }
    .btn-contact-provider:hover { background-color: #2563EB; /* Tailwind blue-600 */ }
    body.dark-mode .btn-contact-provider { background-color: #60A5FA; }

</style>
@endpush

@section('content')
    <div>
        <a href="{{ route('client.request.make') }}" class="back-link"><i class="fas fa-arrow-left mr-2"></i>Back to Provider Search</a>
    </div>

    <div class="profile-grid">
        {{-- Provider Main Info Column --}}
        <div class="provider-main-info-card">
            <img src="{{ $provider->profile_photo_path ? Storage::url($provider->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($provider->name).'&color=4A55A2&background=E0E7FF&size=120&font-size=0.33' }}" alt="{{ $provider->name }}" class="provider-profile-avatar">
            <h2 class="provider-profile-name">{{ $provider->name }}</h2>
            @if($provider->providerDetail && $provider->providerDetail->company_name)
                <p class="provider-profile-location">{{ $provider->providerDetail->company_name }}</p>
            @endif
            @if($provider->city || $provider->address)
                 <p class="provider-profile-location">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $provider->city ?? '' }}{{ $provider->city && $provider->address ? ', ' : '' }}{{ Str::limit($provider->address, 30) }}
                 </p>
            @endif
            @if($provider->phone_number)
                <p class="provider-profile-contact"><i class="fas fa-phone-alt mr-1"></i> {{ $provider->phone_number }}</p>
            @endif

            <div class="provider-profile-rating">
                @if($reviewCount > 0)
                    <span class="profile-rating-value">{{ number_format($averageRating, 1) }}</span>
                    <span class="profile-rating-stars">
                        @php $roundedRating = round($averageRating); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-star {{ $i <= $roundedRating ? 'fas' : 'far' }}"></i>
                        @endfor
                    </span>
                    <span class="profile-review-count">({{ $reviewCount }} review{{ $reviewCount > 1 ? 's' : '' }})</span>
                @else
                    <span class="profile-review-count">No reviews yet</span>
                @endif
            </div>

            @if($provider->providerDetail && $provider->providerDetail->professional_description)
            <div class="profile-description">
                <h4 class="font-semibold mb-1 text-sm">About {{ $provider->name }}</h4>
                <p>{{ $provider->providerDetail->professional_description }}</p>
            </div>
            @endif

             {{-- <a href="{{ route('client.messages.chat', ['serviceRequest' => 'temp_provider_'.$provider->id]) }}" class="btn-contact-provider">
                <i class="fas fa-comments mr-2"></i>Contact {{ $provider->name }}
            </a> --}}
            {{-- Simplified Contact Button, for now, leads to make general request against this provider --}}
             <a href="{{ route('client.request.service.form', ['provider' => $provider]) }}" class="btn-contact-provider mt-4">
                <i class="fas fa-paper-plane mr-2"></i>Send General Request
            </a>
        </div>

        {{-- Services and Reviews Column --}}
        <div>
            <div class="provider-services-card mb-8">
                <h3 class="section-heading">Services Offered</h3>
                @if($provider->services->count() > 0)
                    @foreach($provider->services as $service)
                        <div class="service-item">
                            <div class="service-info">
                                <p class="service-title">{{ $service->title }}</p>
                                <p class="service-category">{{ $service->category->name }}</p>
                            </div>
                            <div class="text-right">
                                @if($service->base_price)
                                <p class="service-price">${{ number_format($service->base_price, 2) }}</p>
                                @endif
                                <a href="{{ route('client.request.service.form', ['provider' => $provider, 'service' => $service]) }}" class="btn-request-service mt-1">Request this</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">This provider has not listed specific services yet. You can still send a general request.</p>
                @endif
            </div>

            @if($reviewCount > 0)
            <div class="provider-reviews-card">
                <h3 class="section-heading">Latest Reviews</h3>
                 @foreach($provider->reviewsReceived as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <img src="{{ $review->client->profile_photo_path ? Storage::url($review->client->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($review->client->name).'&color=7F9CF5&background=EBF4FF&size=32' }}" alt="{{ $review->client->name }}" class="review-avatar">
                            <span class="review-client-name">{{ $review->client->name }}</span>
                            <div class="review-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-star {{ $i <= $review->rating ? 'fas' : 'far' }}"></i>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                        <p class="review-comment mt-1">{{ $review->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $review->created_at->format('M d, Y') }}</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
@endsection
