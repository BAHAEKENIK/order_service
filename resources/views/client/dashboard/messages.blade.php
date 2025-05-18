@extends('layouts.client-dashboard')

@section('title', 'My Messages')
@section('page-title', 'Inbox')

@push('styles')
<style>
    .inbox-container {
        display: flex;
        height: calc(100vh - 120px); /* Adjust based on your header/footer height */
        border: 1px solid var(--border-color-light);
        border-radius: 0.5rem;
        overflow: hidden; /* Crucial for two-panel layout */
    }
    body.dark-mode .inbox-container {
        border-color: var(--border-color-dark);
    }

    .conversation-list {
        width: 320px; /* Fixed width for conversation list */
        border-right: 1px solid var(--border-color-light);
        overflow-y: auto;
        padding: 0; /* Remove padding from ul directly */
    }
    body.dark-mode .conversation-list {
        border-right-color: var(--border-color-dark);
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 1rem; /* py-4 px-4 */
        border-bottom: 1px solid var(--border-color-light);
        cursor: pointer;
        transition: background-color 0.2s;
    }
    body.dark-mode .conversation-item {
        border-bottom-color: var(--border-color-dark);
    }
    .conversation-item:hover {
        background-color: var(--sidebar-active-bg-light);
    }
    body.dark-mode .conversation-item:hover {
        background-color: var(--sidebar-active-bg-dark);
    }
    .conversation-item.active { /* For when this conversation is open */
        background-color: var(--primary-color);
        color: white;
    }
     body.dark-mode .conversation-item.active {
        background-color: var(--primary-hover-color); /* Slightly different for dark mode active */
    }
    .conversation-item.active .conversation-name,
    .conversation-item.active .conversation-last-message,
    .conversation-item.active .conversation-time {
        color: white;
    }


    .conv-avatar img {
        width: 40px; height: 40px; border-radius: 50%; margin-right: 0.75rem; object-fit: cover;
    }
    .conv-details {
        flex-grow: 1;
        overflow: hidden; /* For text ellipsis */
    }
    .conversation-name {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 0.125rem;
    }
    body.dark-mode .conversation-name { color: var(--text-light); }
    .conversation-last-message {
        font-size: 0.8rem;
        color: var(--text-muted-light, #6B7280);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
     body.dark-mode .conversation-last-message { color: var(--text-muted-dark, #9CA3AF); }

    .conversation-time {
        font-size: 0.75rem;
        color: var(--text-muted-light, #6B7280);
        white-space: nowrap;
        margin-left: 0.5rem; /* Space for time */
    }
    body.dark-mode .conversation-time { color: var(--text-muted-dark, #9CA3AF); }

    .chat-area {
        flex-grow: 1;
        display: flex; /* Placeholder: message if no chat is selected */
        justify-content: center;
        align-items: center;
        font-size: 1.1rem;
        color: var(--text-muted-light, #6B7280);
    }
    body.dark-mode .chat-area { color: var(--text-muted-dark, #9CA3AF); }
</style>
@endpush

@section('content')
    <div class="inbox-container">
        <div class="conversation-list">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Inbox</h2>
                {{-- <button class="p-1 text-primary-color dark:text-secondary-color">
                    <i class="fas fa-edit"></i>
                </button> --}}
            </div>
            @if($serviceRequestsWithConversations->isEmpty())
                <p class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No active conversations.</p>
            @else
                <ul>
                    @foreach ($serviceRequestsWithConversations as $sr)
                        @php
                            // Determine the other party in the conversation
                            $otherPartyUser = ($sr->provider_id === $userId) ? $sr->client : $sr->provider;
                            $latestMessage = $sr->messages->first(); // We loaded only the latest
                        @endphp
                        @if($otherPartyUser && $latestMessage)
                            <li>
                                <a href="{{ route('client.messages.chat', $sr) }}"
                                   class="conversation-item {{ (isset($serviceRequest) && $serviceRequest->id == $sr->id) ? 'active' : '' }}">
                                    <div class="conv-avatar">
                                        <img src="{{ $otherPartyUser->profile_photo_path ? Storage::url($otherPartyUser->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherPartyUser->name).'&color=7F9CF5&background=EBF4FF&size=40' }}" alt="{{ $otherPartyUser->name }}">
                                    </div>
                                    <div class="conv-details">
                                        <p class="conversation-name">{{ $otherPartyUser->name }}</p>
                                        <p class="conversation-last-message">
                                            @if($latestMessage->sender_id == $userId)You: @endif{{ Str::limit($latestMessage->content, 30) }}
                                        </p>
                                    </div>
                                    <span class="conversation-time">
                                        {{ $latestMessage->created_at->diffForHumans(null, true, true) }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                 @if($serviceRequestsWithConversations->hasPages())
                    <div class="p-4">
                        {{ $serviceRequestsWithConversations->links('pagination::simple-tailwind') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="chat-area" id="chatAreaPlaceholder">
             @if(isset($serviceRequest) && $serviceRequest->id)
                {{-- The chat content will be loaded by chat.blade.php, this is just a wrapper --}}
                <p>Chat area for Service Request #{{ $serviceRequest->id }} would load here.</p>
            @else
                <p><i class="fas fa-comments fa-2x mb-2"></i><br>Select a conversation to start chatting.</p>
            @endif
        </div>
    </div>
@endsection
