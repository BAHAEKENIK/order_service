@extends('layouts.admin-dashboard')

@section('title', 'Admin Dashboard Overview')
@section('page-title', 'Dashboard Overview')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); /* Slightly adjusted minmax */
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background-color: var(--card-bg-light);
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04); /* Softer shadow */
        display: flex;
        align-items: center;
        transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -2px rgba(0,0,0,0.04);
    }
    body.dark-mode .stat-card {
        background-color: var(--card-bg-dark);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2), 0 2px 4px -1px rgba(0,0,0,0.15);
    }
     body.dark-mode .stat-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2), 0 4px 6px -2px rgba(0,0,0,0.15);
    }

    .stat-icon {
        font-size: 1.75rem; /* text-2xl, adjusted */
        margin-right: 1rem;
        padding: 0.875rem; /* p-3.5 */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px; /* h-14 w-14 */
        height: 56px;
    }
    .stat-icon-users { color: var(--primary-color); background-color: rgba(74, 85, 162, 0.1); }
    body.dark-mode .stat-icon-users { color: var(--secondary-color); background-color: rgba(120, 149, 203, 0.15); }

    .stat-icon-clients { color: #10B981; background-color: rgba(16, 185, 129, 0.1); } /* Emerald-500 */
    body.dark-mode .stat-icon-clients { color: #34D399; background-color: rgba(52, 211, 153, 0.15); }

    .stat-icon-providers { color: #F59E0B; background-color: rgba(245, 158, 11, 0.1); } /* Amber-500 */
    body.dark-mode .stat-icon-providers { color: #FBBF24; background-color: rgba(251, 191, 36, 0.15); }

    .stat-icon-requests { color: #3B82F6; background-color: rgba(59, 130, 246, 0.1); } /* Blue-500 */
    body.dark-mode .stat-icon-requests { color: #60A5FA; background-color: rgba(96, 165, 250, 0.15); }

    .stat-icon-services { color: #8B5CF6; background-color: rgba(139, 92, 246, 0.1); } /* Violet-500 */
    body.dark-mode .stat-icon-services { color: #A78BFA; background-color: rgba(167, 139, 250, 0.15); }

    .stat-icon-categories { color: #EC4899; background-color: rgba(236, 72, 153, 0.1); } /* Pink-500 */
    body.dark-mode .stat-icon-categories { color: #F472B6; background-color: rgba(244, 114, 182, 0.15); }

    .stat-icon-contactmsg { color: #6366F1; background-color: rgba(99, 102, 241, 0.1); } /* Indigo-500 */
    body.dark-mode .stat-icon-contactmsg { color: #818CF8; background-color: rgba(129, 140, 248, 0.15); }


    .stat-info .stat-number { font-size: 1.75rem; font-weight: 700; color: var(--text-dark); }
    body.dark-mode .stat-info .stat-number { color: var(--text-light); }
    .stat-info .stat-label { font-size: 0.875rem; color: var(--text-muted-light, #6B7280); }
    body.dark-mode .stat-info .stat-label { color: var(--text-muted-dark, #9CA3AF); }

    /* Quick Links Section */
    .quick-links-card, .activity-feed-card {
        background-color: var(--card-bg-light);
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    }
    body.dark-mode .quick-links-card, body.dark-mode .activity-feed-card {
        background-color: var(--card-bg-dark);
    }
    .quick-links-card h3, .activity-feed-card h3 {
        font-size: 1.125rem; /* text-lg */
        font-weight: 600; /* semibold */
        margin-bottom: 0.75rem; /* mb-3 */
        color: var(--text-dark);
    }
    body.dark-mode .quick-links-card h3, body.dark-mode .activity-feed-card h3 {
        color: var(--text-light);
    }
    .quick-links-card a {
        display: block;
        color: var(--primary-color);
        text-decoration: none;
        padding: 0.25rem 0;
        font-size: 0.9rem;
    }
    .quick-links-card a:hover { text-decoration: underline; }
    body.dark-mode .quick-links-card a { color: var(--secondary-color); }

</style>
@endpush

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-users"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalUsers'] ?? 0 }}</p>
                <p class="stat-label">Registered Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-clients"><i class="fas fa-user-friends"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalClients'] ?? 0 }}</p>
                <p class="stat-label">Total Clients</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-providers"><i class="fas fa-hard-hat"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalProviders'] ?? 0 }}</p>
                <p class="stat-label">Total Providers</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-requests"><i class="fas fa-concierge-bell"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalServiceRequests'] ?? 0 }}</p>
                <p class="stat-label">Service Requests</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-services"><i class="fas fa-tools"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalServices'] ?? 0 }}</p>
                <p class="stat-label">Listed Services</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-categories"><i class="fas fa-tags"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['totalCategories'] ?? 0 }}</p>
                <p class="stat-label">Service Categories</p>
            </div>
        </div>
         <div class="stat-card">
            <div class="stat-icon stat-icon-contactmsg"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stat-info">
                <p class="stat-number">{{ $stats['pendingContactMessages'] ?? 0 }}</p>
                <p class="stat-label">New Contact Messages</p>
            </div>
        </div>
    </div>

    <div class="mt-8 grid md:grid-cols-2 gap-6">
        <div class="quick-links-card">
            <h3 class="form-section-heading">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}">Manage Users</a>
                <a href="{{ route('admin.categories.index') }}">Manage Categories</a>
                <a href="{{ route('admin.contact-messages.index') }}">View Contact Messages</a>
                <a href="{{ route('admin.inbox.index') }}">Support Inbox</a>
            </div>
        </div>
         <div class="activity-feed-card">
            <h3 class="form-section-heading">Recent Activity</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Platform activity summary will be shown here...</p>
            
        </div>
    </div>
@endsection
