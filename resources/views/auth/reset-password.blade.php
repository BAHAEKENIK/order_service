<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Easy Services</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

     <style>
        /* Shared Auth Page Styles (same as login.blade.php) */
        :root {
            --primary-color: #4A55A2; --primary-hover-color: #3A4382; --secondary-color: #7895CB;
            --form-bg-light: #FFFFFF; --form-bg-dark: #1F2937; --form-accent-bg-light: #F3F4F6;
            --form-accent-bg-dark: #374151; --text-light: #F9FAFB; --text-dark: #111827;
            --text-muted-light: #6B7280; --text-muted-dark: #9CA3AF; --border-light: #D1D5DB;
            --border-dark: #4B5563; --body-bg-light: #FFFFFF; --body-bg-dark: #111827;
            --illustration-bg: #6366F1;
        }
        html.dark { color-scheme: dark; }
        body { font-family: 'Poppins', 'Instrument Sans', sans-serif; background-color: var(--body-bg-light); color: var(--text-dark); transition: background-color 0.3s ease, color 0.3s ease; margin: 0; padding: 0; min-height: 100vh; display: flex; }
        body.dark-mode { background-color: var(--body-bg-dark); color: var(--text-light); }
        .auth-container { display: flex; width: 100%; min-height: 100vh; }
        .auth-form-section { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; background-color: var(--form-bg-light); }
        body.dark-mode .auth-form-section { background-color: var(--form-bg-dark); }
        .auth-illustration-section { flex: 0 0 40%; background-color: var(--illustration-bg); display: none; justify-content: center; align-items: center; padding: 2rem; position: relative; overflow: hidden; }
        @media (min-width: 1024px) { .auth-illustration-section { display: flex; } }
        .auth-illustration-section img { max-width: 80%; height: auto; opacity: 0.8; }
        .form-wrapper { width: 100%; max-width: 400px; }
        .back-button { position: absolute; top: 2rem; left: 2rem; padding: 0.5rem 1rem; background-color: var(--primary-color); color: var(--text-light); border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem; transition: background-color 0.3s ease; }
        .back-button:hover { background-color: var(--primary-hover-color); }
        .form-title { font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; text-align: center; color: var(--text-dark); }
        body.dark-mode .form-title { color: var(--text-light); }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-dark); }
        body.dark-mode .form-label { color: var(--text-light); }
        .form-input-container { position: relative; }
        .form-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-light); background-color: var(--form-accent-bg-light); border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-dark); transition: border-color 0.3s ease, background-color 0.3s ease; }
        .form-input::placeholder { color: var(--text-muted-light); }
        body.dark-mode .form-input { background-color: var(--form-accent-bg-dark); border-color: var(--border-dark); color: var(--text-light); }
        body.dark-mode .form-input::placeholder { color: var(--text-muted-dark); }
        .form-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
        body.dark-mode .form-input:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
        .password-toggle { position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); cursor: pointer; color: var(--text-muted-light); }
        body.dark-mode .password-toggle { color: var(--text-muted-dark); }
        .btn-submit { width: 100%; padding: 0.75rem; background-color: var(--primary-color); color: var(--text-light); border: none; border-radius: 0.375rem; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-submit:hover { background-color: var(--primary-hover-color); }
        #theme-toggle-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 1000; }
        #theme-toggle { background: var(--form-accent-bg-light); border: 1px solid var(--border-light); border-radius: 0.375rem; padding: 0.5rem; }
        body.dark-mode #theme-toggle { background: var(--form-accent-bg-dark); border: 1px solid var(--border-dark); }
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
             <a href="{{ route('login') }}" class="back-button">Back to Sign in</a>
            <div class="form-wrapper">
                <img src="{{ asset('images/logo.png') }}" alt="Easy Logo" class="h-10 mx-auto mb-8">
                <h1 class="form-title">Reset Password</h1>

                 <!-- Session Status (for success messages from controller) -->
                @if (session('status'))
                    <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem;">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                        <ul style="list-style-type: none; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email', request()->email) }}" required autofocus placeholder="example.email@gmail.com">
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="form-label">New Password</label>
                        <div class="form-input-container">
                            <input id="password" class="form-input pr-10" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password">
                            <span class="password-toggle" onclick="togglePasswordVisibility('password', 'password-toggle-icon')">
                                <i class="fas fa-eye-slash" id="password-toggle-icon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                         <div class="form-input-container">
                            <input id="password_confirmation" class="form-input pr-10" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
                            <span class="password-toggle" onclick="togglePasswordVisibility('password_confirmation', 'password-confirm-toggle-icon')">
                                <i class="fas fa-eye-slash" id="password-confirm-toggle-icon"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-submit">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="auth-illustration-section">
            <img src="{{ asset('images/login.png') }}" alt="Reset Password Illustration"> {{-- You might want a different image --}}
        </div>
    </div>

<script>
    // Dark Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sunIcon = themeToggle.querySelector('.fa-sun');
    const moonIcon = themeToggle.querySelector('.fa-moon');
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode'); document.documentElement.classList.add('dark');
            if(sunIcon) sunIcon.classList.add('dark:hidden');
            if(moonIcon) { moonIcon.classList.remove('hidden'); moonIcon.classList.add('dark:inline'); }
        } else {
            body.classList.remove('dark-mode'); document.documentElement.classList.remove('dark');
            if(sunIcon) sunIcon.classList.remove('dark:hidden');
            if(moonIcon) { moonIcon.classList.add('hidden'); moonIcon.classList.remove('dark:inline'); }
        }
    };
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme) { applyTheme(savedTheme); } else if (prefersDark) { applyTheme('dark'); } else { applyTheme('light'); }
    themeToggle.addEventListener('click', () => {
        const isDarkMode = body.classList.contains('dark-mode');
        if (isDarkMode) { applyTheme('light'); localStorage.setItem('theme', 'light'); }
        else { applyTheme('dark'); localStorage.setItem('theme', 'dark'); }
    });

    // Password Visibility Toggle (generic for multiple fields)
    function togglePasswordVisibility(fieldId, iconId) {
        const passwordInput = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
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
