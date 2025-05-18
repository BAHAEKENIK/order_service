@extends('layouts.client-dashboard')

@section('title', 'Make a Service Request')
@section('page-title', 'Make request')

@push('styles')
<style>
    .search-filter-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        align-items: center;
    }
    .search-input {
        flex-grow: 1;
        padding: 0.65rem 1rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem;
        font-size: 0.9rem;
        background-color: var(--content-bg-light);
    }
    body.dark-mode .search-input {
        background-color: var(--content-bg-dark);
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    .category-select, .btn-search {
        padding: 0.65rem 1rem;
        border: 1px solid var(--border-color-light);
        border-radius: 0.375rem;
        font-size: 0.9rem;
        background-color: var(--content-bg-light);
        color: var(--text-dark);
        cursor: pointer;
    }
    body.dark-mode .category-select, body.dark-mode .btn-search {
        background-color: var(--content-bg-dark);
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    .btn-search {
        background-color: var(--primary-color);
        color: white !important; /* Ensure text is light */
        border-color: var(--primary-color);
        border-radius: 0.375rem; /* Standard button rounding from PDF */
        padding: 0.60rem 1.25rem; /* Adjusted padding to better match PDF search button */
    }
    .btn-search:hover {
        background-color: var(--primary-hover-color);
    }
     body.dark-mode .btn-search {
        background-color: var(--secondary-color);
        color: var(--text-dark) !important;
        border-color: var(--secondary-color);
    }

    .provider-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Adjust minmax for card size if needed */
        gap: 1.5rem;
    }

    .provider-card {
        background-color: var(--card-bg-light);
        border-radius: 0.5rem; /* 8px */
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    }
    .provider-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1); /* Enhanced shadow on hover */
    }
    body.dark-mode .provider-card {
        background-color: var(--card-bg-dark);
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3), 0 1px 2px 0 rgba(0,0,0,0.25);
    }
    body.dark-mode .provider-card:hover {
         box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3), 0 4px 6px -4px rgba(0,0,0,0.25);
    }

    .provider-card-header {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }
    .provider-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 0.375rem; /* 6px */
        object-fit: cover;
    }
    .provider-info { flex-grow: 1; }
    .provider-name-link { text-decoration: none; } /* Remove underline from name link */
    .provider-name {
        font-size: 1.125rem; /* 18px */
        font-weight: 600;
        margin-bottom: 0.125rem;
        color: var(--text-dark);
        transition: color 0.2s;
    }
    .provider-name-link:hover .provider-name { color: var(--primary-color); }
    body.dark-mode .provider-name { color: var(--text-light); }
    body.dark-mode .provider-name-link:hover .provider-name { color: var(--secondary-color); }

    .provider-title { font-size: 0.8rem; color: var(--text-muted-light, #6B7280); margin-bottom: 0.5rem; }
    body.dark-mode .provider-title { color: var(--text-muted-dark, #9CA3AF); }
    .provider-rating-box {
        background-color: #f2f1f8; /* Light lavender background from PDF */
        border-radius: 0.375rem;
        padding: 0.35rem 0.65rem; /* Adjusted padding */
        text-align: center;
        width: fit-content;
        margin-top: 0.25rem;
    }
    body.dark-mode .provider-rating-box {
        background-color: #2E316C; /* Darker lavender for dark mode */
    }
    .provider-rating-value { font-size: 1rem; font-weight: bold; color: var(--primary-color); } /* Matched font size */
    body.dark-mode .provider-rating-value { color: var(--secondary-color); }
    .rating-stars { font-size: 0.7rem; /* Adjusted star size */ letter-spacing: 1px; }

    .provider-about-title { font-weight: 600; margin-bottom: 0.25rem; font-size: 0.9rem; color: var(--text-dark); }
    body.dark-mode .provider-about-title { color: var(--text-light); }

    .provider-about-text {
        font-size: 0.875rem;
        color: var(--text-muted-light, #6B7280);
        margin-bottom: 1rem;
        line-height: 1.6;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        flex-grow: 1;
    }
    body.dark-mode .provider-about-text { color: var(--text-muted-dark, #9CA3AF); }

    .provider-actions {
        margin-top: auto;
        display: flex;
        gap: 0.5rem; /* Slightly reduced gap for pill buttons */
        padding-top: 0.5rem;
    }
    .provider-actions .btn { /* Base for both buttons in actions */
        flex-grow: 1;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 9999px !important; /* Pill shape override */
        text-decoration: none;
        text-align: center;
        font-weight: 500;
        border: 1px solid transparent; /* Base for border override if needed */
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
    .btn-contact {
        background-color: var(--primary-color);
        color: white !important;
        border-color: var(--primary-color);
    }
    .btn-contact:hover { background-color: var(--primary-hover-color); border-color: var(--primary-hover-color); }
    body.dark-mode .btn-contact {
        background-color: var(--secondary-color);
        color:var(--text-dark) !important;
        border-color:var(--secondary-color);
    }
     body.dark-mode .btn-contact:hover { background-color: #5E7CB6; }


    .btn-send-request {
        background-color: #14B8A6; /* Teal-500 from Tailwind for the send request green/teal button */
        color: white !important;
        border-color: #14B8A6;
    }
    .btn-send-request:hover { background-color: #0F766E; /* Teal-600 */ }
    body.dark-mode .btn-send-request {
        background-color: #14B8A6;
        color:white !important; /* Keep white text on teal for dark mode for visibility */
        border-color:#14B8A6;
    }
     body.dark-mode .btn-send-request:hover { background-color: #0D9488; /* Teal-700 for dark hover */ }


    .pagination-links { margin-top: 2rem; }
    .pagination-links nav > div:first-child { display: none; }
    .pagination-links a, .pagination-links span { padding: 0.5rem 0.75rem; margin: 0 0.125rem; border: 1px solid var(--border-color-light); color: var(--primary-color); text-decoration: none; border-radius: 0.25rem; font-size: 0.875rem; }
    body.dark-mode .pagination-links a, body.dark-mode .pagination-links span { border-color: var(--border-color-dark); color: var(--secondary-color); background-color: var(--sidebar-active-bg-dark) }
    .pagination-links span[aria-current="page"] span, .pagination-links span[aria-disabled="true"] span { background-color: var(--primary-color); color: white; border-color: var(--primary-color); }
    body.dark-mode .pagination-links span[aria-current="page"] span { background-color: var(--secondary-color); color: var(--text-dark); border-color: var(--secondary-color); }
    .pagination-links a:hover { background-color: var(--sidebar-active-bg-light); }
    body.dark-mode .pagination-links a:hover { background-color: var(--border-color-dark); }
</style>
@endpush

@section('content')
    <form method="GET" action="{{ route('client.request.make') }}" class="search-filter-bar">
        <div class="flex-grow relative">
            <span class="absolute inset-y-0 left-0 pl-3 mt-3 ml-1 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            </span>
            <input type="text" name="search" class="search-input pl-10" placeholder="Search providers, services..." value="{{ request('search') }}">
        </div>
        <select name="category" class="category-select">
            <option value="all">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-search">Search</button>
    </form>

    @if ($providers->isEmpty())
        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
            <i class="fas fa-search fa-3x mb-4"></i>
            <p class="text-xl">No providers found matching your criteria.</p>
            <p>Try adjusting your search or category filter.</p>
        </div>
    @else
        <div class="provider-grid">
            @foreach ($providers as $provider)
                <div class="provider-card"> {{-- This div now gets the hover effect from CSS --}}
                    <a href="{{ route('client.provider.details', $provider) }}" class="provider-name-link">
                        <div class="provider-card-header">
                            <div class="provider-avatar">
                                 <img src="{{ $provider->profile_photo_path ? Storage::url($provider->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($provider->name).'&background=EBF4FF&color=7F9CF5&size=60&rounded=true' }}" alt="{{ $provider->name }}">
                            </div>
                            <div class="provider-info">
                                <h3 class="provider-name">{{ $provider->name }}</h3>
                                <p class="provider-title">
                                    {{ $provider->services->first() ? $provider->services->first()->category->name : ($provider->providerDetail && $provider->providerDetail->professional_description ? Str::words($provider->providerDetail->professional_description, 5) : 'Service Provider') }}
                                </p>
                                @if($provider->providerDetail)
                                <div class="provider-rating-box">
                                    <p class="provider-rating-value">{{ number_format($provider->providerDetail->average_rating ?? 0, 1) }}</p>
                                    <div class="rating-stars">
                                        @php $rating = round($provider->providerDetail->average_rating ?? 0); @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>

                    <h4 class="provider-about-title">About</h4>
                    <p class="provider-about-text">
                        {{ Str::limit($provider->providerDetail->professional_description ?? 'No description available.', 100) }}
                    </p>

                    <div class="provider-actions">
                        {{-- The route 'client.messages.with-provider' will handle finding/creating chat context --}}
                        <a href="{{ route('client.messages.with-provider', $provider) }}" class="btn btn-contact">Contact</a>
                        <a href="{{ route('client.request.service.form', $provider) }}" class="btn btn-send-request">Send request</a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($providers->hasPages())
            <div class="mt-8 pagination-links">
                {{ $providers->links() }}
            </div>
        @endif
    @endif
@endsection
