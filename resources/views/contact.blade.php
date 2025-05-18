<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - Easy Services</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <style>

    </style>
    @endif

    <style>
        /* Custom CSS */
        :root {
            --primary-color: #4A55A2; /* A blue-purple like in the PDF */
            --primary-hover-color: #3A4382;
            --secondary-color: #7895CB; /* A lighter blue for dark mode accents if needed */
            --text-light: #F8F9FA;
            --text-dark: #2D3748; /* Tailwind gray-700 for text */
            --bg-light: #FFFFFF;
            --bg-dark: #1A202C; /* Tailwind gray-900 */
            --input-bg-light: #FFFFFF;
            --input-bg-dark: #2D3748; /* Tailwind gray-800 for input fields */
            --input-border-light: #CBD5E0; /* Tailwind gray-300 */
            --input-border-dark: #4A5568;   /* Tailwind gray-600 */
            --placeholder-light: #A0AEC0; /* Tailwind gray-500 */
            --placeholder-dark: #718096; /* Tailwind gray-400 */
        }

        html.dark {
             color-scheme: dark;
        }

        body {
            font-family: 'Poppins', 'Instrument Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            transition: background-color 0.3s ease, color 0.3s ease;
            padding-top: 70px; /* Adjust if header height changes */
        }
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-light);
        }

        /* Header styles - kept from previous for consistency if you use it */
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
            background-color: var(--input-bg-dark); /* Slightly different for contrast */
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .nav-link {
            color: var(--text-dark);
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }
        body.dark-mode .nav-link {
            color: var(--text-light);
        }
        .nav-link:hover, .nav-link.active { /* active class for current page */
            color: var(--primary-color);
        }
        body.dark-mode .nav-link:hover, body.dark-mode .nav-link.active {
            color: var(--secondary-color);
        }

        /* Button styles - primary for Register and Send, secondary for Log in */
        .btn {
            padding: 0.625rem 1.25rem; /* Consistent padding from PDF */
            border-radius: 0.375rem; /* 6px in PDF looks like this */
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            font-size: 0.875rem; /* 14px */
            text-align: center;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
            cursor: pointer;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--text-light) !important;
            border: 1px solid var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
            border-color: var(--primary-hover-color);
        }
        .btn-secondary {
            background-color: #F3F4F6; /* Light gray from PDF */
            color: var(--primary-color);
            border: 1px solid #F3F4F6;
        }
        .btn-secondary:hover {
            background-color: #E5E7EB; /* Darker light gray */
        }
        body.dark-mode .btn-secondary {
            background-color: var(--input-border-dark);
            color: var(--secondary-color);
            border-color: var(--input-border-dark);
        }
         body.dark-mode .btn-secondary:hover {
            background-color: #4A5568; /* A bit lighter than input-border-dark */
        }


        /* Form Styles */
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        body.dark-mode .form-label {
            color: var(--text-light);
        }
        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--input-border-light);
            border-radius: 0.375rem;
            background-color: var(--input-bg-light);
            color: var(--text-dark);
            transition: border-color 0.3s ease;
            font-size: 0.875rem;
        }
        .form-input::placeholder, .form-textarea::placeholder {
            color: var(--placeholder-light);
        }
        body.dark-mode .form-input, body.dark-mode .form-textarea {
            background-color: var(--input-bg-dark);
            border-color: var(--input-border-dark);
            color: var(--text-light);
        }
        body.dark-mode .form-input::placeholder, body.dark-mode .form-textarea::placeholder {
            color: var(--placeholder-dark);
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); /* Focus ring with primary color */
        }
        body.dark-mode .form-input:focus, body.dark-mode .form-textarea:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        /* Specific to Contact Page layout */
        .contact-form-container {
            max-width: 800px; /* Adjust as needed */
            margin: 2rem auto;
            padding: 2rem;
            /* background-color: var(--bg-light); /* The page body handles this */
            /* border-radius: 0.5rem; */
            /* box-shadow: 0 4px 12px rgba(0,0,0,0.05); */
        }
        /* body.dark-mode .contact-form-container { */
            /* background-color: var(--input-bg-dark); /* Card-like background for form area in dark mode if desired */
            /* box-shadow: 0 4px 12px rgba(0,0,0,0.2); */
        /* } */

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem 1.5rem; /* row-gap column-gap */
        }

        @media (min-width: 768px) { /* md breakpoint */
            .form-grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .form-col-span-2 {
                grid-column: span 2 / span 2;
            }
        }

        #theme-toggle { cursor: pointer; }
    </style>

</head>

<body class="antialiased">
    <!-- Header -->
    <header class="py-4 shadow-sm dark:shadow-md fixed top-0 left-0 right-0 z-50 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ url('/') }}">
                 <img src="{{ asset('images/logo.png') }}" alt="Easy Logo" class="h-5">
            </a>
            <nav class="hidden md:flex items-center space-x-5">
                <a href="{{ url('/') }}" class="nav-link hover:text-primary-color dark:hover:text-secondary-color">Home</a>
                <a href="{{ url('/') }}#about-us" class="nav-link hover:text-primary-color dark:hover:text-secondary-color">About Us</a>
                <a href="{{ url('/') }}#popular-services" class="nav-link hover:text-primary-color dark:hover:text-secondary-color">Provide service</a>
                <a href="{{ route('contact') }}" class="nav-link active hover:text-primary-color dark:hover:text-secondary-color">Contact us</a>
            </nav>
            <div class="flex items-center space-x-2 sm:space-x-3">
                <button id="theme-toggle" class="p-2 rounded-md">
                    <i class="fas fa-sun text-lg text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-lg text-gray-200 hidden dark:inline"></i>
                </button>
                 @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary text-sm hidden sm:inline-block">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary text-sm">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main class="contact-form-container pt-8">
        @if(session('success'))
            <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                <strong style="font-weight: bold;">Oops! Something went wrong.</strong>
                <ul style="margin-top: 0.5rem; list-style-type: disc; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group md:form-grid-cols-2">
                    <div>
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-input" placeholder="enter your first name" value="{{ old('first_name') }}">
                    </div>
                    <div>
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-input" placeholder="enter your last name" value="{{ old('last_name') }}">
                    </div>
                </div>

                <div class="form-group md:form-col-span-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" placeholder="enter your email" value="{{ old('email') }}">
                </div>

                <div class="form-group md:form-col-span-2">
                    <label for="subject" class="form-label">Objet</label> {{-- Changed 'Object' to 'Objet' to match PDF --}}
                    <input type="text" name="subject" id="subject" class="form-input" placeholder="enter objet" value="{{ old('subject') }}">
                </div>

                <div class="form-group md:form-col-span-2">
                    <label for="message" class="form-label">message</label>
                    <textarea name="message" id="message" rows="5" class="form-textarea" placeholder="Input text">{{ old('message') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn btn-primary">
                    send
                </button>
            </div>
        </form>
    </main>

<script>
    // Dark Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sunIcon = themeToggle.querySelector('.fa-sun');
    const moonIcon = themeToggle.querySelector('.fa-moon');

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark');
            if (sunIcon) sunIcon.classList.add('dark:hidden');
            if (moonIcon) {
                moonIcon.classList.remove('hidden');
                moonIcon.classList.add('dark:inline');
            }
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark');
            if (sunIcon) sunIcon.classList.remove('dark:hidden');
            if (moonIcon) {
                moonIcon.classList.add('hidden');
                moonIcon.classList.remove('dark:inline');
            }
        }
    };

    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (prefersDark) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    themeToggle.addEventListener('click', () => {
        const isDarkMode = body.classList.contains('dark-mode');
        if (isDarkMode) {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
        } else {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
        }
    });
</script>

</body>
</html>
