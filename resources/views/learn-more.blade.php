<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More - Easy Services</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom CSS - Reusing variables from welcome.blade.php for consistency */
        :root {
            --primary-color: #4A55A2;
            --primary-hover-color: #3A4382;
            --secondary-color: #7895CB;
            --text-light: #F8F9FA;
            --text-dark: #1F2A37; /* Tailwind gray-800 for text for better readability */
            --bg-light: #FFFFFF;
            --bg-dark: #111827; /* Tailwind gray-900 for body background */
            --card-bg-light: #FFFFFF;
            --card-bg-dark: #1F2937; /* Tailwind gray-800 for card-like sections */
            --border-light: #E2E8F0;
            --border-dark: #4A5568;
        }

        html.dark { color-scheme: dark; }

        body {
            font-family: 'Poppins', 'Instrument Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
            padding-top: 70px; /* Account for fixed header */
        }
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-light);
        }

        /* Header (copied from welcome for consistency - consider a layout file) */
        header {
            background-color: var(--bg-light);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: fixed; /* Fixed header */
            top: 0;
            left: 0;
            right: 0;
            z-index: 50; /* Ensure it's above other content */
        }
        body.dark-mode header {
            background-color: var(--card-bg-dark); /* Or var(--bg-dark) if you prefer */
        }
        .nav-link {
            color: var(--text-dark); padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-weight: 500;
        }
        body.dark-mode .nav-link { color: var(--text-light); }
        .nav-link:hover { color: var(--primary-color); }
        body.dark-mode .nav-link:hover { color: var(--secondary-color); }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light) !important; padding: 0.625rem 1.25rem; border-radius: 0.375rem; text-decoration: none; display: inline-block; font-weight: 500; font-size: 0.875rem; text-align: center; transition: background-color 0.3s ease; cursor: pointer; }
        .btn-primary:hover { background-color: var(--primary-hover-color); }
        .btn-secondary { background-color: #F3F4F6; color: var(--primary-color); border: 1px solid #F3F4F6; padding: 0.625rem 1.25rem; border-radius: 0.375rem; text-decoration: none; display: inline-block; font-weight: 500; font-size: 0.875rem; text-align: center; }
        body.dark-mode .btn-secondary { background-color: var(--border-dark); color: var(--secondary-color); border-color: var(--border-dark); }
        .btn-outline { border: 1px solid var(--primary-color); color: var(--primary-color); padding: 0.6rem 1.25rem; border-radius: 0.375rem; transition: background-color 0.3s ease, color 0.3s ease; text-decoration: none; display: inline-block;}
        .btn-outline:hover { background-color: var(--primary-color); color: var(--text-light); }
        body.dark-mode .btn-outline { border-color: var(--secondary-color); color: var(--secondary-color); }
        body.dark-mode .btn-outline:hover { background-color: var(--secondary-color); color: var(--text-dark); }


        /* Main Content Styling */
        .content-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .section-title {
            font-size: 2.25rem; /* ~36px */
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: center;
        }
        body.dark-mode .section-title {
            color: var(--secondary-color);
        }
        .intro-paragraph {
            font-size: 1.125rem; /* ~18px */
            line-height: 1.7;
            text-align: center;
            margin-bottom: 2.5rem;
            color: var(--text-muted-light, #4A5568); /* Default Tailwind gray-600 */
        }
        body.dark-mode .intro-paragraph {
            color: var(--text-muted-dark, #A0AEC0); /* Default Tailwind gray-400 */
        }
        .feature-section {
            margin-bottom: 2.5rem;
            padding: 1.5rem;
            background-color: var(--card-bg-light);
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); /* Tailwind shadow-md */
        }
        body.dark-mode .feature-section {
            background-color: var(--card-bg-dark);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3), 0 2px 4px -1px rgba(0,0,0,0.2);
        }

        .feature-title {
            font-size: 1.5rem; /* ~24px */
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .feature-text {
            font-size: 1rem; /* ~16px */
            line-height: 1.6;
            color: var(--text-muted-light, #4A5568);
        }
        body.dark-mode .feature-text {
            color: var(--text-muted-dark, #A0AEC0);
        }
        .icon-placeholder { /* For styling icons next to feature titles */
            color: var(--primary-color);
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }
        body.dark-mode .icon-placeholder {
            color: var(--secondary-color);
        }
        .contact-cta-section {
            text-align: center;
            margin-top: 3rem;
            padding: 2rem;
            background-color: var(--primary-color);
            border-radius: 0.5rem;
        }
        body.dark-mode .contact-cta-section {
             background-color: var(--primary-hover-color); /* Darker shade of primary for dark mode CTA */
        }
        .contact-cta-section p {
            color: var(--text-light);
            font-size: 1.125rem;
            margin-bottom: 1rem;
        }
        .contact-cta-section .btn-outline { /* Make outline button light on dark bg */
            border-color: var(--text-light);
            color: var(--text-light);
        }
         .contact-cta-section .btn-outline:hover {
            background-color: var(--text-light);
            color: var(--primary-color);
        }

        #theme-toggle-container { position: fixed; top: 1rem; right: 1.5rem; z-index: 1000; } /* Theme toggle is better placed in fixed header for real app */
        #theme-toggle { background: var(--bg-light); border: 1px solid var(--border-light); border-radius: 0.375rem; padding: 0.5rem; }
        body.dark-mode #theme-toggle { background: var(--card-bg-dark); border: 1px solid var(--border-dark); }

    </style>
</head>
<body class="antialiased">

    <header class="py-3 shadow-sm dark:shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ url('/') }}">
                 <img src="{{ asset('images/logo.png') }}" alt="Easy Logo" class="h-5">
            </a>
            <nav class="hidden md:flex items-center space-x-5">
                <a href="{{ url('/') }}" class="nav-link">Home</a>
                <a href="{{ url('/') }}#about-us" class="nav-link">About Us</a> {{-- Link to section in welcome page --}}
                <a href="{{ route('contact') }}" class="nav-link">Contact us</a>
                <a href="{{ route('learn-more') }}" class="nav-link active">Learn More</a>
                 {{-- Theme toggle in header --}}
                <button id="theme-toggle" class="p-2 rounded-md -mr-2">
                    <i class="fas fa-sun text-lg text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-lg text-gray-200 hidden dark:inline"></i>
                </button>
            </nav>
             <div class="md:hidden flex items-center"> {{-- Mobile --}}
                <button id="theme-toggle-mobile" class="p-2 rounded-md">
                    <i class="fas fa-sun text-lg text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-lg text-gray-200 hidden dark:inline"></i>
                </button>
                {{-- Add a hamburger menu button here if needed for mobile nav items --}}
            </div>
        </div>
    </header>

    <main class="content-container">
        <h1 class="section-title">Discover Easy Services</h1>
        <p class="intro-paragraph">
            Easy Services is a modern platform inspired by leading service marketplaces, designed to effortlessly connect clients with skilled professionals. Whether you're looking for a specific service or you're a provider aiming to expand your reach, we provide the tools for seamless interaction and successful collaborations.
        </p>

        <div class="feature-section">
            <h2 class="feature-title"><i class="fas fa-users icon-placeholder"></i>For Our Clients</h2>
            <p class="feature-text">
                Easily browse a wide array of service categories, filter providers by location or ratings, and send detailed service requests. Our platform allows you to specify your needs, budget, and preferred schedule. Once a provider accepts your request, you can communicate directly via our secure messaging system and track the progress of your service. After completion, leave a review to help others in the community.
            </p>
        </div>

        <div class="feature-section">
            <h2 class="feature-title"><i class="fas fa-tools icon-placeholder"></i>For Service Providers</h2>
            <p class="feature-text">
                Showcase your skills by creating detailed service listings. Receive service requests directly to your personalized dashboard, where you can manage, accept, or decline them. Communicate effectively with clients to finalize details and deliver exceptional service. Build your reputation with client reviews and ratings. Our platform supports you without handling payments, focusing solely on connecting you with opportunities.
            </p>
        </div>

        <div class="feature-section">
            <h2 class="feature-title"><i class="fas fa-shield-alt icon-placeholder"></i>Key Features</h2>
            <ul class="list-disc list-inside pl-4 space-y-2 feature-text">
                <li>Multi-role authentication for Clients, Providers, and Admins.</li>
                <li>Personalized dashboards tailored to user roles.</li>
                <li>Comprehensive service category browsing and filtering.</li>
                <li>Detailed service request system with address, description, date, and budget.</li>
                <li>Private messaging between clients and providers for accepted requests.</li>
                <li>Provider service creation and management.</li>
                <li>Client review and rating system for completed services.</li>
                <li>Advanced search and filtering by location, category, and provider rating.</li>
                <li>Admin panel for user management, service moderation, and platform oversight.</li>
            </ul>
        </div>

        <div class="contact-cta-section">
            <p>Have a question or need more information?</p>
            <a href="{{ route('contact') }}" class="btn-outline px-6 py-2">
                Contact Us
            </a>
        </div>

    </main>

    <footer class="text-center py-8 bg-gray-100 dark:bg-gray-800 text-sm text-gray-600 dark:text-gray-400 mt-12">
        © {{ date('Y') }} Easy Services. All rights reserved.
    </footer>

<script>
    // Dark Mode Toggle
    const themeToggleDesktop = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile'); // Get mobile toggle
    const body = document.body;

    const applyTheme = (theme) => {
        const sunIcons = document.querySelectorAll('.fa-sun');
        const moonIcons = document.querySelectorAll('.fa-moon');
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark');
            sunIcons.forEach(icon => icon.classList.add('dark:hidden'));
            moonIcons.forEach(icon => { icon.classList.remove('hidden'); icon.classList.add('dark:inline'); });
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark');
            sunIcons.forEach(icon => icon.classList.remove('dark:hidden'));
            moonIcons.forEach(icon => { icon.classList.add('hidden'); icon.classList.remove('dark:inline'); });
        }
    };

    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (prefersDark) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    function toggleThemeOnClick() {
        const isDarkMode = body.classList.contains('dark-mode');
        if (isDarkMode) {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
        } else {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
        }
    }

    if(themeToggleDesktop) themeToggleDesktop.addEventListener('click', toggleThemeOnClick);
    if(themeToggleMobile) themeToggleMobile.addEventListener('click', toggleThemeOnClick); // Attach to mobile toggle
</script>

</body>
</html>
