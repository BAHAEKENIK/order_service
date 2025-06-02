@extends('layouts.admin-dashboard')

@section('title', 'Contact Us Messages')
@section('page-title', 'Received Contact Messages')

@push('styles')
<style>
    .messages-table-container {
        background-color: var(--card-bg-light);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        overflow-x: auto;
    }
    body.dark-mode .messages-table-container { background-color: var(--card-bg-dark); }

    .messages-table { width: 100%; border-collapse: collapse; }
    .messages-table th, .messages-table td {
        padding: 0.75rem 1rem; text-align: left;
        border-bottom: 1px solid var(--border-color-light);
        font-size: 0.875rem; vertical-align: middle;
    }
    body.dark-mode .messages-table th, body.dark-mode .messages-table td {
        border-bottom-color: var(--border-color-dark);
    }
    .messages-table th {
        font-weight: 600; color: var(--text-muted-light);
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    body.dark-mode .messages-table th { color: var(--text-muted-dark); }

    .status-badge { padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; display: inline-block; text-transform: capitalize; }
    .status-new { background-color: #FEF3C7; color: #92400E; } body.dark-mode .status-new { background-color: #78350F; color: #FDE68A;}
    .status-read_by_admin { background-color: #DBEAFE; color: #1E40AF; } body.dark-mode .status-read_by_admin { background-color: #1E3A8A; color: #BFDBFE;}
    .status-replied { background-color: #D1FAE5; color: #065F46; } body.dark-mode .status-replied { background-color: #064E3B; color: #A7F3D0;}

    .btn-view-reply {
        padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius:0.375rem;
        text-decoration: none; display: inline-flex; align-items:center; gap:0.3rem;
        font-weight: 500;
    }
    .btn-view-reply-primary {
        background-color: var(--primary-color); color: white !important; border: 1px solid var(--primary-color);
    }
    .btn-view-reply-primary:hover { background-color: var(--primary-hover-color); }
    body.dark-mode .btn-view-reply-primary {
        background-color: var(--secondary-color); color: var(--text-dark) !important; border-color: var(--secondary-color);
    }

    .pagination-links { margin-top: 1.5rem; }
</style>
@endpush

@section('content')
    @if ($messages->isEmpty())
        <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-lg shadow">
            <i class="fas fa-envelope-open-text fa-3x mb-4 text-gray-400 dark:text-gray-500"></i>
            <p class="text-xl text-gray-500 dark:text-gray-400">No contact messages have been received.</p>
        </div>
    @else
        <div class="messages-table-container">
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr class="{{ $message->status == 'new' ? 'font-bold dark:font-semibold' : '' }}">
                            <td>
                                {{ $message->name }}
                                @if($message->user_id)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">(User #{{$message->user_id}})</span>
                                @endif
                            </td>
                            <td>{{ $message->email }}</td>
                            <td>{{ Str::limit($message->subject, 40) }}</td>
                            <td>{{ $message->created_at->diffForHumans() }}</td>
                            <td>
                                <span class="status-badge status-{{ Str::slug($message->status, '_') }}">
                                    {{ Str::title(str_replace(['_', 'by admin'], ' ', $message->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn-view-reply btn-view-reply-primary">
                                    <i class="fas fa-eye"></i> View & Reply
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($messages->hasPages())
            <div class="mt-6 pagination-links">
                {{ $messages->links() }}
            </div>
        @endif
    @endif
@endsection
