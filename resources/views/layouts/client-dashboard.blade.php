<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Client Dashboard') - Easy Services</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #4A55A2; /* Indigo-like color from PDF */
            --primary-hover-color: #3A4382;
            --secondary-color: #7895CB; /* Lighter blue for accents */
            --sidebar-bg-light: #F9FAFB; /* Tailwind gray-50 */
            --sidebar-bg-dark: #1F2937; /* Tailwind gray-800 */
            --sidebar-text-light: #374151; /* Tailwind gray-700 */
            --sidebar-text-dark: #D1D5DB; /* Tailwind gray-300 */
            --sidebar-active-bg-light: #E5E7EB; /* Tailwind gray-200 */
            --sidebar-active-bg-dark: #374151; /* Tailwind gray-700 */
            --sidebar-active-text-light: var(--primary-color);
            --sidebar-active-text-dark: #FFFFFF;
            --content-bg-light: #FFFFFF;
            --content-bg-dark: #111827; /* Tailwind gray-900 */
            --text-light: #F9FAFB;
            --text-dark: #1F2937;
            --border-color-light: #E5E7EB;
            --border-color-dark: #374151;
        }
        html.dark { color-scheme: dark; }
        body { font-family: 'Poppins', 'Instrument Sans', sans-serif; background-color: var(--content-bg-light); color: var(--text-dark); margin: 0; }
        body.dark-mode { background-color: var(--content-bg-dark); color: var(--text-light); }

        .dashboard-layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg-light);
            padding: 1.5rem 1rem;
            border-right: 1px solid var(--border-color-light);
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s, border-color 0.3s;
            position: fixed; /* Fixed sidebar */
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 10; /* Ensure it's behind modals but above content */
        }
        body.dark-mode .sidebar { background-color: var(--sidebar-bg-dark); border-right-color: var(--border-color-dark); }

        .sidebar-logo img { height: 2.5rem; /* 40px */ margin-bottom: 2rem; margin-left:0.5rem } /* Adjusted logo size slightly based on PDF look */
        .sidebar-nav ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem; /* Consistent padding */
            margin-bottom: 0.5rem; /* Spacing between items */
            border-radius: 0.375rem; /* rounded-md */
            text-decoration: none;
            color: var(--sidebar-text-light);
            font-weight: 500; /* medium */
            font-size: 0.9rem; /* Slightly smaller font if needed */
            transition: background-color 0.2s, color 0.2s;
        }
        body.dark-mode .sidebar-nav li a { color: var(--sidebar-text-dark); }
        .sidebar-nav li a:hover { background-color: var(--sidebar-active-bg-light); color: var(--sidebar-active-text-light); }
        body.dark-mode .sidebar-nav li a:hover { background-color: var(--sidebar-active-bg-dark); color: var(--sidebar-active-text-dark); }
        /* Active state more prominent as per PDF */
        .sidebar-nav li a.active { background-color: var(--primary-color) !important; color: white !important; }
        .sidebar-nav li a.active i { color: white !important; } /* Ensure icon color changes too */
        body.dark-mode .sidebar-nav li a.active { background-color: var(--primary-color) !important; color: white !important; }
        .sidebar-nav li a i { margin-right: 0.75rem; width: 20px; text-align: center; font-size: 1.1rem; }

        .sidebar-footer { margin-top: auto; padding-top: 1.5rem; border-top: 1px solid var(--border-color-light); }
        body.dark-mode .sidebar-footer { border-top-color: var(--border-color-dark); }
        .user-profile-widget { display: flex; align-items: center; padding: 0.5rem 0; }
        .user-profile-widget img { width: 36px; /* Slightly smaller avatar */ height: 36px; border-radius: 50%; margin-right: 0.75rem; object-fit: cover; border: 1px solid var(--border-color-light); }
        body.dark-mode .user-profile-widget img { border-color: var(--border-color-dark); }
        .user-profile-widget div p:first-child { font-weight: 500; /* medium */ color: var(--text-dark); font-size:0.875rem; margin-bottom: 0.1rem; }
        body.dark-mode .user-profile-widget div p:first-child { color: var(--text-light); }
        .user-profile-widget div a { font-size: 0.75rem; /* 12px */ color: var(--text-muted-light, #6B7280); text-decoration: none; }
        body.dark-mode .user-profile-widget div a { color: var(--text-muted-dark, #9CA3AF); }
        .user-profile-widget div a:hover { text-decoration: underline; }
        .user-profile-widget .settings-icon { margin-left: auto; color: var(--text-muted-light, #6B7280); font-size: 1rem; padding: 0.25rem; cursor:pointer;}
        body.dark-mode .user-profile-widget .settings-icon { color: var(--text-muted-dark, #9CA3AF); }

        .main-content-wrapper {
            margin-left: 260px; /* Same as sidebar width */
            flex-grow: 1;
            display: flex;
            flex-direction: column; /* To make header and main stack */
            min-height: 100vh;
        }
        .main-content {
            flex-grow: 1; padding: 1.5rem 2rem; overflow-y: auto;
        }
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem; /* Space below header */
            padding-bottom: 1rem; /* Space below title/actions */
            border-bottom: 1px solid var(--border-color-light);
            background-color: var(--content-bg-light); /* Give header a background */
            padding: 1rem 2rem; /* Consistent padding for the header itself */
        }
        body.dark-mode .content-header { border-bottom-color: var(--border-color-dark); background-color: var(--content-bg-dark); }
        .content-title { font-size: 1.5rem; font-weight: 600; /* Semibold as per PDF */ color:var(--text-dark); }
        body.dark-mode .content-title { color: var(--text-light); }

        .top-bar-actions { display: flex; align-items: center; gap: 1rem; }
        .top-bar-actions button, .top-bar-actions a { color: var(--text-muted-light, #6B7280); font-size:1.25rem; background: none; border:none; cursor: pointer; display:flex; align-items:center; padding:0.25rem; }
        body.dark-mode .top-bar-actions button, body.dark-mode .top-bar-actions a { color: var(--text-muted-dark, #9CA3AF); }
        .top-bar-actions .logout-btn { font-size:0.8rem; /* Slightly smaller */ text-transform: uppercase; font-weight:500; padding: 0.3rem 0.6rem; border-radius: 0.25rem; }
        .top-bar-actions .logout-btn:hover { background-color: var(--sidebar-active-bg-light); color: var(--sidebar-text-light) }
        body.dark-mode .top-bar-actions .logout-btn:hover { background-color: var(--sidebar-active-bg-dark); color:var(--sidebar-text-dark) }


        #theme-toggle-dashboard { font-size:1.1rem; /* Adjust size if needed */}

        /* Mobile specific for later - for now, just ensures sidebar can be hidden */
        @media (max-width: 1024px) { /* Adjusted breakpoint, e.g., 'lg' in Tailwind */
            .sidebar {
                /* Example of how you might hide it and show with JS */
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                position: fixed;
                z-index: 40; /* High z-index for overlay */
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content-wrapper {
                margin-left: 0; /* Full width when sidebar is hidden/overlayed */
            }
            /* Add a hamburger icon button here that would be visible on mobile */
            /* For now, sidebar will be hidden on smaller screens, you'd need JS to toggle it */
        }
    </style>
    @stack('styles') {{-- For page-specific styles --}}
</head>
<body class="antialiased"> {{-- Add class="dark-mode" here if you want to default to dark for testing dashboard --}}
    <div class="dashboard-layout">
        <aside class="sidebar" id="clientSidebar">
            <div class="sidebar-logo">
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Easy Services Logo">
                </a>
            </div>
            <nav class="sidebar-nav flex-grow">
                <ul>
                    <li>
                        <a href="{{ route('client.requests.my') }}" class="{{ request()->routeIs('client.requests.my*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i> My Requests
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.request.make') }}" class="{{ request()->routeIs('client.request.make*') ? 'active' : '' }}">
                            <i class="fas fa-edit"></i> Make a request
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.messages.index') }}" class="{{ request()->routeIs('client.messages.*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Messages
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="user-profile-widget">
                    <a href="{{ route('client.profile.edit') }}">
                        <img src="{{ Auth::user()->profile_photo_path ? Storage::url(Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=4A55A2&background=E0E7FF' }}" alt="{{ Auth::user()->name }}">
                    </a>
                    <div class="mr-auto">
                        <p>{{ Auth::user()->name }}</p>
                        <a href="{{ route('client.profile.edit') }}" class="view-profile-link">View profile</a>
                    </div>
                    <a href="{{ route('client.profile.edit') }}" class="settings-icon" title="Settings">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
            </div>
        </aside>

        <div class="main-content-wrapper">
             {{-- Top bar within the main content area --}}
            <header class="content-header">
                {{-- Mobile Hamburger Menu Button (placeholder) --}}
                {{-- <button id="mobileMenuButton" class="lg:hidden p-2 mr-2 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-bars text-xl"></i>
                </button> --}}
                <h1 class="content-title">@yield('page-title', 'Dashboard')</h1>
                <div class="top-bar-actions">
                     <button id="theme-toggle-dashboard" title="Toggle Theme">
                        <i class="fas fa-sun"></i>
                        <i class="fas fa-moon" style="display:none;"></i>
                    </button>
                    <a href="{{ route('client.contact.admin') }}" title="Contact Admin / Notifications">
    <i class="fas fa-headset"></i>
    {{-- You can add a badge here for unread admin messages later --}}
</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="logout-btn" title="Logout">LOGOUT</button>
                    </form>
                </div>
            </header>

            <main class="main-content">
                @if(session('success'))
                    <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-size:0.9rem;">
                        {{ session('success') }}
                    </div>
                @endif
                 @if(session('error'))
                    <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-size:0.9rem;">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

<script>
    const themeToggleDashboard = document.getElementById('theme-toggle-dashboard');
    const body = document.body;

    const applyDashboardTheme = (theme) => {
        const sunIcon = themeToggleDashboard.querySelector('.fa-sun');
        const moonIcon = themeToggleDashboard.querySelector('.fa-moon');
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark'); // For Tailwind's dark: prefix
            if (sunIcon) sunIcon.style.display = 'none';
            if (moonIcon) moonIcon.style.display = 'inline';
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark');
            if (sunIcon) sunIcon.style.display = 'inline';
            if (moonIcon) moonIcon.style.display = 'none';
        }
    };

    // Load saved theme or system preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) { applyDashboardTheme(savedTheme); }
    else if (prefersDark) { applyDashboardTheme('dark'); }
    else { applyDashboardTheme('light'); }

    // Theme toggle button event
    if(themeToggleDashboard) {
        themeToggleDashboard.addEventListener('click', () => {
            const isDarkMode = body.classList.contains('dark-mode');
            if (isDarkMode) { applyDashboardTheme('light'); localStorage.setItem('theme', 'light'); }
            else { applyDashboardTheme('dark'); localStorage.setItem('theme', 'dark'); }
        });
    }

    // Placeholder for mobile sidebar toggle
    // const mobileMenuButton = document.getElementById('mobileMenuButton');
    // const sidebar = document.getElementById('clientSidebar');
    // if (mobileMenuButton && sidebar) {
    //     mobileMenuButton.addEventListener('click', () => {
    //         sidebar.classList.toggle('open'); // You'd need to define .open style for sidebar
    //     });
    // }
</script>
@stack('scripts')
</body>
</html>
