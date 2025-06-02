@extends('layouts.admin-dashboard')

@section('title', 'View Contact Message')
@section('page-title', 'Contact Message: ' . Str::limit($contactMessage->subject, 30))

@push('styles')
<style>
    .content-card {
        background-color: var(--card-bg-light); border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); padding:1.5rem; margin-bottom: 1.5rem;
    }
    body.dark-mode .content-card { background-color: var(--card-bg-dark); }
    .content-card-header { font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color-light); }
    body.dark-mode .content-card-header { border-bottom-color: var(--border-color-dark); }

    .detail-item { margin-bottom: 0.75rem; display: flex; flex-wrap: wrap; }
    .detail-label { font-weight: 500; color: var(--text-muted-light); width: 100px; flex-shrink: 0; font-size: 0.875rem;}
    body.dark-mode .detail-label { color: var(--text-muted-dark); }
    .detail-value { color: var(--text-dark); flex-grow: 1; font-size: 0.875rem;}
    body.dark-mode .detail-value { color: var(--text-light); }

    .message-content-box {
        background-color: #F9FAFB; border: 1px solid var(--border-color-light); padding: 1rem;
        border-radius: 0.375rem; white-space: pre-wrap; line-height: 1.6; font-size: 0.9rem;
    }
    body.dark-mode .message-content-box { background-color: var(--border-color-dark); border-color: #4B5563; color: var(--text-light); }

    .admin-reply-box { margin-top: 1rem; border-top: 1px dashed var(--border-color-light); padding-top: 1rem;}
    body.dark-mode .admin-reply-box { border-top-color: var(--border-color-dark); }

    .form-label { display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.875rem; }
    .form-input, .form-textarea {
        width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--border-color-light);
        border-radius: 0.375rem; background-color: var(--content-bg-light); color: var(--text-dark); font-size:0.9rem;
    }
    body.dark-mode .form-input, body.dark-mode .form-textarea { background-color: var(--card-bg-dark); border-color: var(--border-color-dark); color:var(--text-light); }
    .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
    body.dark-mode .form-input:focus, body.dark-mode .form-textarea:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
    .form-textarea { min-height: 150px; }
    .form-group { margin-bottom: 1.5rem; }
    .btn-reply-submit { background-color: var(--primary-color); color: white !important; padding: 0.65rem 1.5rem; border-radius: 0.375rem; font-weight: 500; border:none; cursor:pointer;}
    .btn-reply-submit:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-reply-submit { background-color: var(--secondary-color); color: var(--text-dark) !important; }
    .status-badge { padding: 0.25rem 0.6rem; border-radius:9999px; font-size: 0.75rem; font-weight: 500; display: inline-block; text-transform: capitalize;}
    .status-new { background-color: #FEF3C7; color: #92400E; } body.dark-mode .status-new { background-color: #78350F; color: #FDE68A;}
    .status-read_by_admin { background-color: #DBEAFE; color: #1E40AF; } body.dark-mode .status-read_by_admin { background-color: #1E3A8A; color: #BFDBFE;}
    .status-replied { background-color: #D1FAE5; color: #065F46; } body.dark-mode .status-replied { background-color: #064E3B; color: #A7F3D0;}
</style>
@endpush

@section('content')
    <a href="{{ route('admin.contact-messages.index') }}" class="inline-block mb-6 text-primary-color dark:text-secondary-color hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to All Contact Messages
    </a>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 content-card">
            <h3 class="content-card-header">Message from {{ $contactMessage->name }}</h3>
            <div class="detail-item"><dt class="detail-label">Sender Email:</dt><dd class="detail-value"><a href="mailto:{{ $contactMessage->email }}" class="text-primary-color dark:text-secondary-color hover:underline">{{ $contactMessage->email }}</a></dd></div>
            <div class="detail-item"><dt class="detail-label">Subject:</dt><dd class="detail-value">{{ $contactMessage->subject }}</dd></div>
            <div class="detail-item"><dt class="detail-label">Received:</dt><dd class="detail-value">{{ $contactMessage->created_at->format('F d, Y \a\t H:i A') }}</dd></div>
            @if($contactMessage->user_id)
                 <div class="detail-item"><dt class="detail-label">User Account:</dt><dd class="detail-value"><a href="{{ route('admin.users.show', $contactMessage->user_id) }}" class="text-primary-color dark:text-secondary-color hover:underline">View User Profile (#{{ $contactMessage->user_id }})</a></dd></div>
            @endif
            <div class="mt-4">
                <p class="form-label font-medium">Message Content:</p>
                <div class="message-content-box">
                    {{ $contactMessage->message }}
                </div>
            </div>
            @if($contactMessage->admin_reply)
            <div class="admin-reply-box mt-6">
                <h4 class="form-label font-medium text-green-600 dark:text-green-400">Your Reply (Sent: {{ $contactMessage->replied_at ? $contactMessage->replied_at->format('M d, Y H:i') : '' }}):</h4>
                <div class="message-content-box bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700">
                    {{ $contactMessage->admin_reply }}
                </div>
            </div>
            @endif
        </div>

        <div class="content-card">
            <h3 class="content-card-header">Reply to Message</h3>
             @if($contactMessage->status !== 'replied')
                <form action="{{ route('admin.contact-messages.reply', $contactMessage) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reply_subject" class="form-label">Reply Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="reply_subject" id="reply_subject" class="form-input" value="{{ old('reply_subject', 'Re: ' . $contactMessage->subject) }}" required>
                        @error('reply_subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="reply_content" class="form-label">Your Reply <span class="text-red-500">*</span></label>
                        <textarea name="reply_content" id="reply_content" rows="10" class="form-textarea" required placeholder="Type your email reply here...">{{ old('reply_content') }}</textarea>
                        @error('reply_content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn-reply-submit w-full"><i class="fas fa-paper-plane mr-2"></i>Send Email Reply</button>
                    </div>
                </form>
            @else
                <div class="text-center p-4 bg-green-50 dark:bg-green-700/30 border border-green-200 dark:border-green-600 rounded-md">
                    <i class="fas fa-check-circle text-3xl text-green-500 dark:text-green-400 mb-2"></i>
                    <p class="font-semibold text-green-700 dark:text-green-300">You have already replied to this message.</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Reply sent on: {{ $contactMessage->replied_at ? $contactMessage->replied_at->format('M d, Y H:i A') : 'N/A' }}
                    </p>
                </div>
            @endif

            <div class="form-group mt-6 border-t pt-4 border-dashed border-gray-300 dark:border-gray-600">
                 <label for="admin_notes" class="form-label">Internal Notes (Optional)</label>
                <textarea name="admin_notes" id="admin_notes" rows="3" class="form-textarea" placeholder="Add internal notes about this contact or your reply...">{{ old('admin_notes', $contactMessage->admin_notes) }}</textarea>
                {{-- <button type="submit" formaction="{{ route('admin.contact-messages.add-note', $contactMessage) }}" class="mt-2 btn-secondary-action text-xs">Save Note</button> (Needs separate route and controller method) --}}
                 <small class="text-xs text-gray-500 dark:text-gray-400">These notes are for admin use only and won't be sent to the user.</small>
            </div>
        </div>
    </div>
@endsection
