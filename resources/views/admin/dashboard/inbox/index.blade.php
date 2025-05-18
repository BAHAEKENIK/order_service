@extends('layouts.admin-dashboard')

@section('title', 'Support Inbox')
@section('page-title', 'Support Inbox')

@push('styles')
{{-- Reusing inbox styles. Consider centralizing to layouts/partials or app.css --}}
<style>
    .inbox-container { display: flex; height: calc(100vh - 120px); border: 1px solid var(--border-color-light); border-radius: 0.5rem; overflow: hidden; }
    body.dark-mode .inbox-container { border-color: var(--border-color-dark); }
    .conversation-list { width: 320px; border-right: 1px solid var(--border-color-light); overflow-y: auto; padding: 0; background-color: var(--sidebar-bg-light); }
    body.dark-mode .conversation-list { border-right-color: var(--border-color-dark); background-color: var(--sidebar-bg-dark); }
    .conversation-list-header { padding: 1rem; border-bottom: 1px solid var(--border-color-light); font-weight: 600; position: sticky; top: 0; background: inherit; z-index: 1;}
    body.dark-mode .conversation-list-header { border-bottom-color: var(--border-color-dark); }
    .conversation-item { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border-color-light); cursor: pointer; transition: background-color 0.2s; text-decoration:none; }
    body.dark-mode .conversation-item { border-bottom-color: var(--border-color-dark); }
    .conversation-item:hover { background-color: var(--sidebar-active-bg-light); }
    body.dark-mode .conversation-item:hover { background-color: var(--sidebar-active-bg-dark); }
    .conversation-item.active { background-color: var(--primary-color); color: white !important; }
    body.dark-mode .conversation-item.active { background-color: var(--primary-hover-color); }
    .conversation-item.active .conversation-name, .conversation-item.active .conversation-last-message, .conversation-item.active .conversation-time { color: white !important; }
    .conv-avatar img { width: 40px; height: 40px; border-radius: 50%; margin-right: 0.75rem; object-fit: cover; }
    .conv-details { flex-grow: 1; overflow: hidden; }
    .conversation-name { font-weight: 600; color: var(--text-dark); font-size: 0.9rem; margin-bottom: 0.125rem; }
    body.dark-mode .conversation-name { color: var(--text-light); }
    .conversation-last-message { font-size: 0.8rem; color: var(--text-muted-light); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    body.dark-mode .conversation-last-message { color: var(--text-muted-dark); }
    .conversation-time { font-size: 0.75rem; color: var(--text-muted-light); white-space: nowrap; margin-left: 0.5rem; }
    body.dark-mode .conversation-time { color: var(--text-muted-dark); }
    .chat-area-placeholder { flex-grow: 1; display: flex; justify-content: center; align-items: center; font-size: 1.1rem; color: var(--text-muted-light); padding: 2rem; background-color: var(--content-bg-light); }
    body.dark-mode .chat-area-placeholder { color: var(--text-muted-dark); background-color: var(--content-bg-dark); }
    .pagination-links { margin-top: 1rem; padding: 0 1rem; }
</style>
@endpush

@section('content')
    <div class="inbox-container">
        <div class="conversation-list">
            <div class="conversation-list-header text-lg">
                Conversations
            </div>
            @if($conversations->isEmpty())
                <p class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No conversations yet.</p>
            @else
                <ul>
                    @foreach ($conversations as $sr)
                        @php
                            $otherPartyUser = null;
                            if ($sr->client_id === $adminId) { // Admin initiated, other party is provider_id (the user)
                                $otherPartyUser = $sr->provider;
                            } elseif ($sr->provider_id === $adminId) { // User initiated with Admin, other party is client_id
                                $otherPartyUser = $sr->client;
                            }
                            $latestMessage = $sr->messages->first();
                        @endphp
                        @if($otherPartyUser && $latestMessage)
                            <li>
                                <a href="{{ route('admin.inbox.chat', $sr) }}"
                                   class="conversation-item {{ (isset($serviceRequest) && $serviceRequest->id == $sr->id) ? 'active' : '' }}">
                                    <div class="conv-avatar">
                                        <img src="{{ $otherPartyUser->profile_photo_path ? Storage::url($otherPartyUser->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherPartyUser->name).'&color=7F9CF5&background=EBF4FF&size=40' }}" alt="{{ $otherPartyUser->name }}">
                                    </div>
                                    <div class="conv-details">
                                        <p class="conversation-name">{{ $otherPartyUser->name }} <span class="text-xs capitalize">({{ $otherPartyUser->role }})</span></p>
                                        <p class="conversation-last-message">
                                            @if($latestMessage->sender_id == $adminId)You: @endif{{ Str::limit($latestMessage->content, 25) }}
                                        </p>
                                    </div>
                                    <span class="conversation-time">
                                        {{ $latestMessage->created_at->isToday() ? $latestMessage->created_at->format('g:i A') : $latestMessage->created_at->format('M d') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                @if($conversations->hasPages())
                    <div class="p-4 pagination-links">
                        {{ $conversations->links('pagination::simple-tailwind') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="chat-area-placeholder" id="chatAreaContentPlaceholder">
            <div class="text-center">
                 <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Select a conversation from the list to view messages.</p>
            </div>
        </div>
    </div>
@endsection
