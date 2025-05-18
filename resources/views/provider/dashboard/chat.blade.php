@extends('layouts.provider-dashboard')

{{-- Determine the name for the title correctly whether it's client or admin --}}
@php
    $chatPartnerName = $otherParty ? $otherParty->name : 'User';
    if ($otherParty && $otherParty->isAdmin()){
        $chatPartnerName .= " (Admin Support)";
    }
@endphp

@section('title', 'Chat with ' . $chatPartnerName)
@section('page-title')
    <div class="flex items-center">
        <a href="{{ route('provider.messages.index') }}" class="mr-3 text-gray-500 hover:text-primary-color dark:text-gray-400 dark:hover:text-secondary-color">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        @if($otherParty)
        <img src="{{ $otherParty->profile_photo_path ? Storage::url($otherParty->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherParty->name).'&color=7F9CF5&background=EBF4FF&size=40&rounded=true' }}" alt="{{ $otherParty->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
        @endif
        <div>
            Chat with {{ $chatPartnerName }}
            <p class="text-xs font-normal text-gray-500 dark:text-gray-400">
                @if($serviceRequest->description == 'Support chat with Admin: ' . Auth::user()->name)
                    Admin Support Channel
                @else
                    Regarding Request #{{ $serviceRequest->id }}: {{ Str::limit($serviceRequest->service->title ?? $serviceRequest->description, 25) }}
                @endif
            </p>
        </div>
    </div>
@endsection

@push('styles')
{{-- Using styles from client's chat.blade.php; can be centralized if identical --}}
<style>
    .chat-page-container { display: flex; flex-direction: column; height: calc(100vh - 130px); /* Adjusted for typical dashboard header and title bar height */ background-color: var(--card-bg-light); border-radius: 0.5rem; overflow: hidden; }
    body.dark-mode .chat-page-container { background-color: var(--card-bg-dark); }
    .chat-messages { flex-grow: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; }
    .message-bubble { max-width: 70%; padding: 0.75rem 1rem; border-radius: 1rem; margin-bottom: 0.75rem; line-height: 1.4; font-size: 0.9rem; word-break: break-word; }
    .message-sent { background-color: var(--primary-color); color: white; align-self: flex-end; border-bottom-right-radius: 0.25rem; }
    body.dark-mode .message-sent { background-color: var(--secondary-color); color: var(--text-dark); }
    .message-received { background-color: #E5E7EB; color: var(--text-dark); align-self: flex-start; border-bottom-left-radius: 0.25rem; }
    body.dark-mode .message-received { background-color: #374151; color: var(--text-light); }
    .message-meta { font-size: 0.7rem; color: var(--text-muted-light, #9CA3AF); margin-top: 0.25rem; }
    body.dark-mode .message-meta { color: var(--text-muted-dark, #6B7280); }
    .message-sent .message-meta { text-align: right; } .message-received .message-meta { text-align: left; }
    .chat-input-area { padding: 1rem 1.5rem; border-top: 1px solid var(--border-color-light); display: flex; align-items: center; background-color: var(--card-bg-light); }
    body.dark-mode .chat-input-area { border-top-color: var(--border-color-dark); background-color: var(--card-bg-dark); }
    .chat-input-area textarea { flex-grow: 1; border: 1px solid var(--border-color-light); border-radius: 1.5rem; padding: 0.75rem 1.25rem; resize: none; min-height: 44px; max-height: 120px; font-size: 0.9rem; background-color: var(--content-bg-light); color: var(--text-dark); }
    body.dark-mode .chat-input-area textarea { background-color: var(--content-bg-dark); border-color: var(--border-color-dark); color: var(--text-light); }
    .chat-input-area textarea::placeholder { color: var(--text-muted-light, #9CA3AF); }
    body.dark-mode .chat-input-area textarea::placeholder { color: var(--text-muted-dark, #6B7280); }
    .chat-input-area button[type="submit"] { background-color: var(--primary-color); color: white; border: none; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; margin-left: 0.75rem; font-size: 1.2rem; cursor: pointer; transition: background-color 0.3s; }
    .chat-input-area button[type="submit"]:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .chat-input-area button[type="submit"] { background-color: var(--secondary-color); color: var(--text-dark); }
    .no-messages-notice { text-align: center; color: var(--text-muted-light, #6B7280); margin: auto; }
    body.dark-mode .no-messages-notice { color: var(--text-muted-dark, #9CA3AF); }
</style>
@endpush

@section('content')
    <div class="chat-page-container">
        <div class="chat-messages" id="chatMessagesContainer"> {{-- Added an ID for easier JS targeting --}}
            @forelse ($serviceRequest->messages as $message)
                <div class="message-bubble {{ $message->sender_id == Auth::id() ? 'message-sent' : 'message-received' }}">
                    <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                    <div class="message-meta">
                        {{ $message->created_at->format('M d, H:i A') }}
                        @if($message->sender_id == Auth::id() && $message->read_at)
                            <i class="fas fa-check-double text-blue-400 ml-1" title="Read"></i>
                        @elseif($message->sender_id == Auth::id())
                            <i class="fas fa-check text-gray-400 dark:text-gray-500 ml-1" title="Sent"></i>
                        @endif
                    </div>
                </div>
            @empty
                <p class="no-messages-notice">
                    @if($otherParty->isAdmin())
                        This is the start of your conversation with Admin Support.
                    @else
                        No messages in this conversation yet. Start chatting!
                    @endif
                </p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('provider.messages.store', $serviceRequest) }}" class="chat-input-area">
            @csrf
            <textarea name="content" rows="1" placeholder="Type a message..." required oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"' class="focus:ring-primary-color dark:focus:ring-secondary-color focus:border-primary-color dark:focus:border-secondary-color"></textarea>
            <button type="submit" title="Send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatMessagesContainer = document.getElementById('chatMessagesContainer');
        if (chatMessagesContainer) {
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }
        const textarea = document.querySelector('.chat-input-area textarea');
        if(textarea){
            function autoResizeTextarea() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            }
            textarea.style.height = textarea.scrollHeight + 'px'; // Initial size
            textarea.addEventListener('input', autoResizeTextarea, false);
            textarea.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    if (this.value.trim() !== '') { // Only submit if not empty
                        this.form.submit();
                    }
                }
            });
        }
    });
</script>
@endpush
