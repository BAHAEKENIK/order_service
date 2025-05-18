@extends('layouts.client-dashboard')

@section('title', 'Chat with ' . $otherParty->name)
@section('page-title')
    <div class="flex items-center">
        <img src="{{ $otherParty->profile_photo_path ? Storage::url($otherParty->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($otherParty->name).'&color=7F9CF5&background=EBF4FF&size=40&rounded=true' }}" alt="{{ $otherParty->name }}" class="w-10 h-10 rounded-full mr-3">
        <div>
            Chat with {{ $otherParty->name }}
            {{-- <p class="text-xs font-normal text-green-500">Active now</p> --}}
        </div>
    </div>
@endsection


@push('styles')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 150px); /* Adjust based on your header/title height and padding */
        background-color: var(--card-bg-light);
        border-radius: 0.5rem; /* From PDF design */
    }
    body.dark-mode .chat-container {
        background-color: var(--card-bg-dark);
    }

    .chat-header { /* Already handled by page-title and top-bar-actions in layout */
        /* padding: 1rem;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        align-items: center;
        justify-content: space-between; */
    }
    /* body.dark-mode .chat-header { border-bottom-color: var(--border-color-dark); } */

    .chat-messages {
        flex-grow: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 1rem; /* More rounded bubbles */
        margin-bottom: 0.75rem;
        line-height: 1.4;
        font-size: 0.9rem;
    }

    .message-sent {
        background-color: var(--primary-color);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0.25rem; /* Asymmetric rounding like common chat apps */
    }
    body.dark-mode .message-sent {
         background-color: var(--secondary-color);
         color: var(--text-dark);
    }

    .message-received {
        background-color: #E5E7EB; /* Tailwind gray-200 */
        color: var(--text-dark);
        align-self: flex-start;
        border-bottom-left-radius: 0.25rem;
    }
    body.dark-mode .message-received {
        background-color: #374151; /* Tailwind gray-700 */
        color: var(--text-light);
    }

    .message-meta {
        font-size: 0.7rem;
        color: var(--text-muted-light, #9CA3AF);
        margin-top: 0.25rem;
    }
    body.dark-mode .message-meta { color: var(--text-muted-dark, #6B7280); }
    .message-sent .message-meta { text-align: right; }
    .message-received .message-meta { text-align: left; }


    .chat-input-area {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color-light);
        display: flex;
        align-items: center;
        background-color: var(--card-bg-light); /* Ensure input area has background */
    }
    body.dark-mode .chat-input-area {
        border-top-color: var(--border-color-dark);
        background-color: var(--card-bg-dark);
    }
    .chat-input-area textarea {
        flex-grow: 1;
        border: 1px solid var(--border-color-light);
        border-radius: 1.5rem; /* Pill shape for text area */
        padding: 0.75rem 1.25rem;
        resize: none;
        min-height: 44px; /* Roughly matches PDF height */
        max-height: 120px; /* Allow some expansion */
        font-size: 0.9rem;
        background-color: var(--content-bg-light); /* Matching main content area */
    }
    body.dark-mode .chat-input-area textarea {
        background-color: var(--content-bg-dark);
        border-color: var(--border-color-dark);
        color: var(--text-light);
    }
    .chat-input-area textarea::placeholder { color: var(--text-muted-light, #9CA3AF); }
    body.dark-mode .chat-input-area textarea::placeholder { color: var(--text-muted-dark, #6B7280); }

    .chat-input-area button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%; /* Round send button */
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 0.75rem;
        font-size: 1.2rem; /* Icon size */
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .chat-input-area button:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .chat-input-area button { background-color: var(--secondary-color); color: var(--text-dark); }

    /* Optional: Attachment icons as per PDF bottom bar */
    .chat-input-area .attachment-icons button {
        background: none;
        border: none;
        color: var(--text-muted-light, #6B7280);
        font-size: 1.2rem;
        padding: 0.5rem;
        margin-right: 0.5rem;
    }
     body.dark-mode .chat-input-area .attachment-icons button { color: var(--text-muted-dark, #9CA3AF); }

</style>
@endpush

@section('content')
    <div class="chat-container">
        {{--
            The chat header is now part of the main layout's @section('page-title')
            It includes the other party's name and avatar.
        --}}

        <div class="chat-messages" id="chatMessages">
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
                <p class="text-center text-gray-500 dark:text-gray-400 my-auto">No messages in this conversation yet. Start chatting!</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('client.messages.store', $serviceRequest) }}" class="chat-input-area">
            @csrf
            {{-- Placeholder for attachment icons from PDF --}}
            <div class="attachment-icons">
                {{-- <button type="button" title="Attach File"><i class="fas fa-paperclip"></i></button>
                <button type="button" title="Upload Image"><i class="fas fa-image"></i></button>
                <button type="button" title="Location"><i class="fas fa-map-marker-alt"></i></button> --}}
            </div>
            <textarea name="content" rows="1" placeholder="Type a message..." required oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
            <button type="submit" title="Send">
                <i class="fas fa-paper-plane"></i>
            </button>
             {{-- <button type="button" title="Emoji"><i class="far fa-smile"></i></button> --}}
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            // Scroll to the bottom of the chat messages
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Auto-resize textarea
        const textarea = document.querySelector('.chat-input-area textarea');
        if(textarea){
            const initialHeight = textarea.scrollHeight + 'px';
             textarea.style.height = initialHeight; // Set initial height
            textarea.addEventListener('input', function() {
                this.style.height = 'auto'; // Reset height
                this.style.height = (this.scrollHeight) + 'px'; // Set to scroll height
            });
        }
    });
</script>
@endpush
