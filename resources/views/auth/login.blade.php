<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - Easy Services</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <style>
        /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com (Include your full Tailwind base if Vite is not working) */
        /* For brevity, I'll assume Vite is working or you have a way to include Tailwind base CSS. */
    </style>
    @endif

    <style>
        /* Custom CSS */
        :root {
            --primary-color: #4A55A2; /* Main purple-blue from PDF */
            --primary-hover-color: #3A4382;
            --secondary-color: #7895CB; /* Lighter accent for dark mode */
            --form-bg-light: #FFFFFF;
            --form-bg-dark: #1F2937; /* Tailwind gray-800 */
            --form-accent-bg-light: #F3F4F6; /* Light gray for input bg from PDF */
            --form-accent-bg-dark: #374151;  /* Tailwind gray-700 for input bg dark mode */
            --text-light: #F9FAFB; /* Tailwind gray-50 */
            --text-dark: #111827;  /* Tailwind gray-900 */
            --text-muted-light: #6B7280; /* Tailwind gray-500 */
            --text-muted-dark: #9CA3AF;  /* Tailwind gray-400 */
            --border-light: #D1D5DB; /* Tailwind gray-300 */
            --border-dark: #4B5563;   /* Tailwind gray-600 */
            --body-bg-light: #FFFFFF; /* White page background */
            --body-bg-dark: #111827;  /* Dark page background */
            --illustration-bg: #6366F1; /* Indigo-500 from Tailwind, similar to PDF right panel */
        }

        html.dark {
            color-scheme: dark;
        }

        body {
            font-family: 'Poppins', 'Instrument Sans', sans-serif;
            background-color: var(--body-bg-light);
            color: var(--text-dark);
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex; /* For full height layout */
        }
        body.dark-mode {
            background-color: var(--body-bg-dark);
            color: var(--text-light);
        }

        .auth-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .auth-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem; /* 32px */
            background-color: var(--form-bg-light);
        }
        body.dark-mode .auth-form-section {
            background-color: var(--form-bg-dark);
        }

        .auth-illustration-section {
            flex: 0 0 40%; /* Adjust width as needed, PDF looks around 40-45% */
            background-color: var(--illustration-bg);
            display: none; /* Hidden on small screens */
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative; /* For positioning elements if needed */
            overflow: hidden; /* Prevent illustration overflow */
        }
        @media (min-width: 1024px) { /* lg breakpoint */
            .auth-illustration-section {
                display: flex;
            }
        }
         .auth-illustration-section img { /* Simple image styling for the placeholder */
            max-width: 80%;
            height: auto;
            opacity: 0.8;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px; /* Adjust as needed */
        }

        .back-button {
            position: absolute;
            top: 2rem;
            left: 2rem;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: var(--text-light);
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: var(--primary-hover-color);
        }

        .form-title {
            font-size: 2rem; /* 32px */
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            color: var(--text-dark);
        }
        body.dark-mode .form-title {
            color: var(--text-light);
        }

        .form-label {
            display: block;
            font-size: 0.875rem; /* 14px */
            font-weight: 500;
            margin-bottom: 0.5rem; /* 8px */
            color: var(--text-dark);
        }
        body.dark-mode .form-label {
            color: var(--text-light);
        }

        .form-input-container {
            position: relative; /* For password toggle icon */
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem; /* 12px 16px */
            border: 1px solid var(--border-light);
            background-color: var(--form-accent-bg-light);
            border-radius: 0.375rem; /* 6px */
            font-size: 0.875rem;
            color: var(--text-dark);
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }
        .form-input::placeholder {
            color: var(--text-muted-light);
        }
        body.dark-mode .form-input {
            background-color: var(--form-accent-bg-dark);
            border-color: var(--border-dark);
            color: var(--text-light);
        }
        body.dark-mode .form-input::placeholder {
            color: var(--text-muted-dark);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2);
        }
        body.dark-mode .form-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted-light);
        }
        body.dark-mode .password-toggle {
            color: var(--text-muted-dark);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-dark);
        }
        body.dark-mode .checkbox-label {
            color: var(--text-light);
        }
        .checkbox-label input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: var(--primary-color); /* Styles the checkbox color */
        }

        .link {
            font-size: 0.875rem;
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .link:hover {
            text-decoration: underline;
            color: var(--primary-hover-color);
        }
        body.dark-mode .link {
            color: var(--secondary-color);
        }
         body.dark-mode .link:hover {
            color: var(--primary-color); /* Lighter shade for dark mode hover */
        }


        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem; /* 16px */
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: var(--primary-hover-color);
        }

        #theme-toggle-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1000; /* Ensure it's above everything */
        }
        #theme-toggle {
             background: var(--form-accent-bg-light);
             border: 1px solid var(--border-light);
             border-radius: 0.375rem;
             padding: 0.5rem;
        }
        body.dark-mode #theme-toggle {
             background: var(--form-accent-bg-dark);
             border: 1px solid var(--border-dark);
        }

        .error-message {
            color: #E53E3E; /* Tailwind red-600 */
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        body.dark-mode .error-message {
            color: #FC8181; /* Tailwind red-400 */
        }

    </style>
</head>
<body class="antialiased">

    <div id="theme-toggle-container">
        <button id="theme-toggle">
            <i class="fas fa-sun text-xl text-gray-700 dark:hidden"></i>
            <i class="fas fa-moon text-xl text-gray-200 hidden dark:inline"></i>
        </button>
    </div>

    <div class="auth-container">
        <div class="auth-form-section">
            <a href="{{ url('/') }}" class="back-button">Back</a>
            <div class="form-wrapper">
                <img src="{{ asset('images/logo.png') }}" alt="Easy Logo" class="h-10 mx-auto mb-8">
                <h1 class="form-title">Sign in</h1>

                @if ($errors->any())
                    <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                        <ul style="list-style-type: none; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status')) {{-- For password reset status --}}
                    <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                        {{ session('status') }}
                    </div>
                @endif


                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="example.email@gmail.com">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="form-input-container">
                            <input id="password" class="form-input pr-10" type="password" name="password" required autocomplete="current-password" placeholder="Enter at least 8+ characters">
                            <span class="password-toggle" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye-slash" id="password-toggle-icon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="checkbox-label">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="link" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn-submit">
                            Sign in
                        </button>
                    </div>
                     <p class="text-sm text-center text-muted-light dark:text-muted-dark">
                        Don't have an account? <a href="{{ route('register') }}" class="link font-medium">Sign up</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="auth-illustration-section">
            <!-- You can use an SVG or an image here -->
            <img src="{{ asset('images/login.png') }}" alt="Easy Login">
        </div>
    </div>

<script>
    // Dark Mode Toggle (Simplified, assuming same logic as welcome)
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sunIcon = themeToggle.querySelector('.fa-sun');
    const moonIcon = themeToggle.querySelector('.fa-moon');

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark');
            if (sunIcon) sunIcon.classList.add('dark:hidden');
            if (moonIcon) { moonIcon.classList.remove('hidden'); moonIcon.classList.add('dark:inline'); }
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark');
            if (sunIcon) sunIcon.classList.remove('dark:hidden');
            if (moonIcon) { moonIcon.classList.add('hidden'); moonIcon.classList.remove('dark:inline'); }
        }
    };
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme) { applyTheme(savedTheme); }
    else if (prefersDark) { applyTheme('dark'); }
    else { applyTheme('light'); }

    themeToggle.addEventListener('click', () => {
        const isDarkMode = body.classList.contains('dark-mode');
        if (isDarkMode) { applyTheme('light'); localStorage.setItem('theme', 'light'); }
        else { applyTheme('dark'); localStorage.setItem('theme', 'dark'); }
    });

    // Password Visibility Toggle
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('password-toggle-icon');
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            passwordInput.type = "password";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>

</body>
</html>
