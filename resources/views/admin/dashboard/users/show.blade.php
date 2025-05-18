@extends('layouts.admin-dashboard')

@section('title', 'User Details: ' . $user->name)
@section('page-title', 'View User Profile')

@push('styles')
<style>
    .profile-container { max-width: 800px; margin: auto; }
    .profile-card {
        background-color: var(--card-bg-light);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }
    body.dark-mode .profile-card { background-color: var(--card-bg-dark); }

    .profile-header {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .profile-header { border-bottom-color: var(--border-color-dark); }

    .profile-avatar-large {
        width: 80px; height: 80px;
        border-radius: 50%; object-fit: cover; margin-right: 1.5rem;
        border: 2px solid var(--border-color-light);
    }
    body.dark-mode .profile-avatar-large { border-color: var(--border-color-dark); }

    .profile-name { font-size: 1.5rem; font-weight: 600; }
    .profile-role {
        font-size: 0.875rem; text-transform: capitalize; padding: 0.2rem 0.6rem; border-radius: 0.25rem;
        display: inline-block; margin-top:0.25rem;
    }
    .role-client { background-color: #DBEAFE; color: #1E40AF; } body.dark-mode .role-client { background-color: #1E3A8A; color: #BFDBFE; }
    .role-provider { background-color: #D1FAE5; color: #065F46; } body.dark-mode .role-provider { background-color: #064E3B; color: #A7F3D0; }


    .profile-section { padding: 1.5rem; }
    .profile-section-title {
        font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;
        padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color-light);
    }
    body.dark-mode .profile-section-title { border-bottom-color: var(--border-color-dark); }

    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0.75rem 1.5rem; }
    .detail-pair { margin-bottom: 0.5rem; }
    .detail-pair dt { font-weight: 500; color: var(--text-muted-light); font-size: 0.8rem; margin-bottom:0.1rem; }
    body.dark-mode .detail-pair dt { color: var(--text-muted-dark); }
    .detail-pair dd { font-size: 0.9rem; }

    .service-list-item, .review-list-item {
        padding: 0.75rem 0; border-bottom: 1px solid var(--border-color-light);
    }
    .service-list-item:last-child, .review-list-item:last-child { border-bottom: none; }
    body.dark-mode .service-list-item, body.dark-mode .review-list-item { border-bottom-color: var(--border-color-dark); }

    .service-title { font-weight: 500; }
    .service-category-tag { font-size: 0.75rem; background-color: #E0E7FF; color: #3730A3; padding: 0.15rem 0.5rem; border-radius: 0.25rem; margin-left: 0.5rem; }
    body.dark-mode .service-category-tag { background-color: #312E81; color: #C7D2FE; }
    .review-stars { color: #F59E0B; font-size: 0.9rem; }
    .review-comment { margin-top: 0.25rem; font-style: italic; font-size: 0.85rem; color: var(--text-muted-light)}
    body.dark-mode .review-comment { color: var(--text-muted-dark) }

    .btn-back {
        display: inline-flex; align-items: center; text-decoration: none;
        color: var(--primary-color); font-weight: 500; margin-bottom: 1.5rem;
    }
    body.dark-mode .btn-back { color: var(--secondary-color); }
    .btn-back:hover { text-decoration: underline; }
    .btn-back i { margin-right: 0.5rem; }

    .profile-actions { margin-top: 1.5rem; display:flex; gap:1rem; }
     .btn-action-chat { background-color: var(--primary-color); color:white !important; border:1px solid var(--primary-color)}
    body.dark-mode .btn-action-chat { background-color:var(--secondary-color); color: var(--text-dark) !important; border:1px solid var(--secondary-color)}
</style>
@endpush

@section('content')
    <a href="{{ route('admin.users.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>Back to User List
    </a>

    <div class="profile-card">
        <div class="profile-header">
            <img src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=80&color=FFFFFF&background=4A55A2' }}" alt="{{ $user->name }}" class="profile-avatar-large">
            <div>
                <h2 class="profile-name">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                <span class="profile-role role-{{$user->role}}">{{ $user->role }}</span>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Joined: {{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="profile-section">
            <h3 class="profile-section-title">Contact & Location</h3>
            <dl class="detail-grid">
                <div class="detail-pair"> <dt>Phone:</dt> <dd>{{ $user->phone_number ?? 'Not Provided' }}</dd> </div>
                <div class="detail-pair"> <dt>Address:</dt> <dd>{{ $user->address ?? 'Not Provided' }}</dd> </div>
                <div class="detail-pair"> <dt>City:</dt> <dd>{{ $user->city ?? 'Not Provided' }}</dd> </div>
            </dl>
        </div>

        @if($user->isProvider())
            <div class="profile-section">
                <h3 class="profile-section-title">Provider Information</h3>
                <dl class="detail-grid">
                    <div class="detail-pair"><dt>Availability:</dt> <dd>{{ $user->providerDetail && $user->providerDetail->is_available ? 'Available' : 'Not Available' }}</dd></div>
                    <div class="detail-pair"><dt>Average Rating:</dt> <dd>{{ number_format($user->providerDetail->average_rating ?? 0, 1) }}/5.0 ({{ $user->reviewsReceived->count() }} Reviews)</dd></div>
                    <div class="detail-pair col-span-full"><dt>Professional Description:</dt> <dd class="whitespace-pre-wrap">{{ $user->providerDetail->professional_description ?? 'N/A' }}</dd></div>
                </dl>
                <h4 class="font-semibold mt-4 mb-2 text-sm">Services Offered ({{ $user->services->count() }})</h4>
                 @if($user->services->count() > 0)
                    <ul class="list-disc pl-5 text-sm">
                        @foreach($user->services->take(5) as $service)
                            <li>{{ $service->title }} ({{ $service->category->name }}) - Status: {{ $service->status }}</li>
                        @endforeach
                        @if($user->services->count() > 5) <li>And more...</li> @endif
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No services listed yet.</p>
                @endif
                <h4 class="font-semibold mt-4 mb-2 text-sm">Recent Reviews Received</h4>
                 @if($user->reviewsReceived->count() > 0)
                    @foreach($user->reviewsReceived->take(3) as $review)
                        <div class="review-list-item">
                            <div class="review-stars">
                                @for ($i = 1; $i <= 5; $i++) <i class="fa-star {{ $i <= $review->rating ? 'fas' : 'far' }}"></i> @endfor
                                <span class="text-xs text-gray-500 ml-2">by {{ $review->client->name }} on {{ $review->created_at->format('M d') }}</span>
                            </div>
                            @if($review->comment) <p class="review-comment">"{{ Str::limit($review->comment, 100) }}"</p> @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">No reviews received yet.</p>
                @endif
            </div>
        @elseif($user->isClient())
            <div class="profile-section">
                <h3 class="profile-section-title">Client Activity</h3>
                <dl class="detail-grid">
                    <div class="detail-pair"><dt>Service Requests:</dt><dd>{{ $user->client_service_requests_count ?? '0' }} made</dd></div>
                    <div class="detail-pair"><dt>Reviews Given:</dt><dd>{{ $user->reviews_given_count ?? '0' }} given</dd></div>
                </dl>
                 <h4 class="font-semibold mt-4 mb-2 text-sm">Recent Reviews Given</h4>
                @if($user->reviewsGiven->count() > 0)
                    @foreach($user->reviewsGiven->take(3) as $review)
                         <div class="review-list-item">
                            <div class="review-stars">
                                @for ($i = 1; $i <= 5; $i++) <i class="fa-star {{ $i <= $review->rating ? 'fas' : 'far' }}"></i> @endfor
                                <span class="text-xs text-gray-500 ml-2">for {{ $review->provider->name }} on {{ $review->created_at->format('M d') }}</span>
                            </div>
                            @if($review->comment) <p class="review-comment">"{{ Str::limit($review->comment, 100) }}"</p> @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">No reviews given yet.</p>
                @endif
            </div>
        @endif

        <div class="profile-actions px-6 pb-6">
            <a href="{{ route('admin.users.chat', $user) }}" class="btn btn-action-chat">
                <i class="fas fa-comments mr-2"></i>Chat with User
            </a>
            {{-- Add Edit button link when edit user functionality is ready --}}
            {{-- <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded inline-flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit User Details
            </a> --}}
        </div>
    </div>
@endsection
