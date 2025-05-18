@extends('layouts.provider-dashboard')

@section('title', 'My Inbox')
@section('page-title', 'Inbox')

@push('styles')
<style>
    .inbox-container { display: flex; height: calc(100vh - 120px); border: 1px solid var(--border-color-light); border-radius: 0.5rem; overflow: hidden; }
    body.dark-mode .inbox-container { border-color: var(--border-color-dark); }
    .conversation-list { width: 320px; border-right: 1px solid var(--border-color-light); overflow-y: auto; padding: 0; }
    body.dark-mode .conversation-list { border-right-color: var(--border-color-dark); }
    .conversation-item { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border-color-light); cursor: pointer; transition: background-color 0.2s; text-decoration:none; }
    body.dark-mode .conversation-item { border-bottom-color: var(--border-color-dark); }
    .conversation-item:hover { background-color: var(--sidebar-active-bg-light); }
    body.dark-mode .conversation-item:hover { background-color: var(--sidebar-active-bg-dark); }
    .conversation-item.active { background-color: var(--primary-color); color: white !important; } /* Ensure text turns white */
    body.dark-mode .conversation-item.active { background-color: var(--primary-hover-color); }
    .conversation-item.active .conversation-name, .conversation-item.active .conversation-last-message, .conversation-item.active .conversation-time { color: white !important; }
    .conv-avatar img { width: 40px; height: 40px; border-radius: 50%; margin-right: 0.75rem; object-fit: cover; }
    .conv-details { flex-grow: 1; overflow: hidden; }
    .conversation-name { font-weight: 600; color: var(--text-dark); font-size: 0.9rem; margin-bottom: 0.125rem; }
    body.dark-mode .conversation-name { color: var(--text-light); }
    .conversation-last-message { font-size: 0.8rem; color: var(--text-muted-light, #6B7280); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    body.dark-mode .conversation-last-message { color: var(--text-muted-dark, #9CA3AF); }
    .conversation-time { font-size: 0.75rem; color: var(--text-muted-light, #6B7280); white-space: nowrap; margin-left: 0.5rem; }
    body.dark-mode .conversation-time { color: var(--text-muted-dark, #9CA3AF); }
    .chat-area-placeholder { flex-grow: 1; display: flex; justify-content: center; align-items: center; font-size: 1.1rem; color: var(--text-muted-light, #6B7280); padding: 2rem; }
    body.dark-mode .chat-area-placeholder { color: var(--text-muted-dark, #9CA3AF); }
    .pagination-links { margin-top: 1rem; padding: 0 1rem; }
</style>
@endpush

@section('content')
    <div class="inbox-container">
        <div class="conversation-list">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-sidebar-bg-light dark:bg-sidebar-bg-dark z-10">
                <h2 class="text-lg font-semibold">All Conversations</h2>
            </div>
            @if($serviceRequestsWithConversations->isEmpty())
                <p class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">You have no active messages.</p>
            @else
                <ul>
                    @foreach ($serviceRequestsWithConversations as $sr)
                        @php
                            $currentUserIsClientOfThisSR = ($sr->client_id == Auth::id());
                            $currentUserIsProviderOfThisSR = ($sr->provider_id == Auth::id());

                            $otherPartyUser = null;
                            if ($currentUserIsProviderOfThisSR && !$sr->client->isAdmin()) { // Provider to Client
                                $otherPartyUser = $sr->client;
                            } elseif ($currentUserIsClientOfThisSR && $sr->provider->isAdmin()) { // Provider (as client) to Admin
                                $otherPartyUser = $sr->provider; // Here, provider role is Admin
                            }
                            // This assumes providers only initiate "admin chat" ServiceRequests where they are client_id
                            // And clients only initiate "admin chat" ServiceRequests where they are client_id

                            $latestMessage = $sr->messages->first();
                        @endphp
                        @if($otherPartyUser && $latestMessage)
                            <li>
                                <a href="{{ route('provider.messages.chat', $sr) }}"
                                   class="conversation-item {{ (isset($serviceRequest) && $serviceRequest->id == $sr->id) ? 'active' : '' }}">
                                    <div class="conv-avatar">
                                        <img src="{{ $otherPartyUser->profile_photo_path ? Storage::url($otherPartyUser->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherPartyUser->name).'&color=7F9CF5&background=EBF4FF&size=40' }}" alt="{{ $otherPartyUser->name }}">
                                    </div>
                                    <div class="conv-details">
                                        <p class="conversation-name">{{ $otherPartyUser->name }} @if($otherPartyUser->isAdmin()) (Admin) @endif</p>
                                        <p class="conversation-last-message">
                                            @if($latestMessage->sender_id == Auth::id())You: @endif{{ Str::limit($latestMessage->content, 30) }}
                                        </p>
                                    </div>
                                    <span class="conversation-time">
                                        {{ $latestMessage->created_at->isToday() ? $latestMessage->created_at->format('g:i A') : $latestMessage->created_at->format('M d') }}
                                    </span>
                                </a>
                            </li>
                        @elseif($sr->provider_id == Auth::id() && $sr->client)
                            {{-- Fallback if logic for admin chat is different for providers' own SRs where they are provider --}}
                            @php $otherPartyUser = $sr->client; $latestMessage = $sr->messages->first(); @endphp
                             <li>
                                <a href="{{ route('provider.messages.chat', $sr) }}"
                                   class="conversation-item {{ (isset($serviceRequest) && $serviceRequest->id == $sr->id) ? 'active' : '' }}">
                                    <div class="conv-avatar"> <img src="{{ $otherPartyUser->profile_photo_path ? Storage::url($otherPartyUser->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherPartyUser->name).'&color=7F9CF5&background=EBF4FF&size=40' }}" alt="{{ $otherPartyUser->name }}"> </div>
                                    <div class="conv-details"> <p class="conversation-name">{{ $otherPartyUser->name }}</p> <p class="conversation-last-message"> @if($latestMessage && $latestMessage->sender_id == Auth::id())You: @endif{{ Str::limit($latestMessage->content ?? 'No messages yet', 30) }} </p> </div>
                                    @if($latestMessage) <span class="conversation-time"> {{ $latestMessage->created_at->isToday() ? $latestMessage->created_at->format('g:i A') : $latestMessage->created_at->format('M d') }} </span> @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                @if($serviceRequestsWithConversations->hasPages())
                    <div class="p-4 pagination-links">
                        {{ $serviceRequestsWithConversations->links('pagination::simple-tailwind') }}
                    </div>
                @endif
            @endif
        </div>
        <div class="chat-area-placeholder" id="chatAreaContent">
            <div class="text-center">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Select a conversation to view messages or start a new one.</p>
            </div>
        </div>
    </div>
@endsection
