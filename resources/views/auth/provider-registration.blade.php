<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Provider Registration - Easy Services</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom CSS - you can reuse variables from welcome.blade.php or login/register if centralized */
        :root {
            --primary-color: #4A55A2; --primary-hover-color: #3A4382; --secondary-color: #7895CB;
            --text-light: #F8F9FA; --text-dark: #1F2A37; /* Slightly lighter dark text for better contrast on forms */
            --bg-light: #FFFFFF; --bg-dark: #111827; /* Main background */
            --form-container-bg-light: #FFFFFF;
            --form-container-bg-dark: #1F2937; /* Tailwind gray-800 */
            --input-bg-light: #FFFFFF;
            --input-bg-dark: #2D3748; /* Tailwind gray-700 for input fields in dark mode */
            --input-border-light: #D1D5DB; /* Tailwind gray-300 */
            --input-border-dark: #4B5563;   /* Tailwind gray-600 */
            --placeholder-light: #9CA3AF; /* Tailwind gray-400 */
            --placeholder-dark: #6B7280;  /* Tailwind gray-500 */
        }
        html.dark { color-scheme: dark; }
        body { font-family: 'Poppins', 'Instrument Sans', sans-serif; background-color: var(--bg-light); color: var(--text-dark); transition: background-color 0.3s ease, color 0.3s ease; margin: 0; padding-top: 80px; /* Space for fixed header */ }
        body.dark-mode { background-color: var(--bg-dark); color: var(--text-light); }

        /* Header from welcome.blade.php */
        header { background-color: var(--bg-light); box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: fixed; top: 0; left: 0; right: 0; z-index: 50; }
        body.dark-mode header { background-color: var(--input-bg-dark); } /* Similar to contact header dark mode */
        .nav-link { color: var(--text-dark); padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-weight: 500; }
        body.dark-mode .nav-link { color: var(--text-light); }
        .nav-link:hover, .nav-link.active { color: var(--primary-color); }
        body.dark-mode .nav-link:hover, body.dark-mode .nav-link.active { color: var(--secondary-color); }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light) !important; padding: 0.625rem 1.25rem; border-radius: 0.375rem; text-decoration: none; display: inline-block; font-weight: 500; font-size: 0.875rem; text-align: center; transition: background-color 0.3s ease; cursor: pointer; }
        .btn-primary:hover { background-color: var(--primary-hover-color); }
        .btn-secondary { background-color: #F3F4F6; color: var(--primary-color); border: 1px solid #F3F4F6; padding: 0.625rem 1.25rem; border-radius: 0.375rem; text-decoration: none; display: inline-block; font-weight: 500; font-size: 0.875rem; text-align: center; }
        body.dark-mode .btn-secondary { background-color: var(--input-border-dark); color: var(--secondary-color); border-color: var(--input-border-dark); }

        /* Form specific styles for provider registration */
        .provider-form-container { max-width: 800px; margin: 2rem auto; padding: 2rem; background-color: var(--form-container-bg-light); border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        body.dark-mode .provider-form-container { background-color: var(--form-container-bg-dark); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .form-section-title { font-size: 1.5rem; font-weight: 600; color: var(--primary-color); margin-bottom: 0.5rem; }
        body.dark-mode .form-section-title { color: var(--secondary-color); }
        .form-section-subtitle { font-size: 0.9rem; color: var(--text-muted-light); margin-bottom: 2rem; }
        body.dark-mode .form-section-subtitle { color: var(--text-muted-dark); }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size:0.875rem; color: var(--text-dark); }
        body.dark-mode .form-label { color: var(--text-light); }
        .form-input, .form-select, .form-textarea, .form-file-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--input-border-light); border-radius: 0.375rem; background-color: var(--input-bg-light); color: var(--text-dark); transition: border-color 0.3s ease; font-size: 0.875rem; }
        .form-input::placeholder, .form-textarea::placeholder { color: var(--placeholder-light); }
        body.dark-mode .form-input, body.dark-mode .form-select, body.dark-mode .form-textarea, body.dark-mode .form-file-input { background-color: var(--input-bg-dark); border-color: var(--input-border-dark); color: var(--text-light); }
        body.dark-mode .form-input::placeholder, body.dark-mode .form-textarea::placeholder { color: var(--placeholder-dark); }
        .form-input:focus, .form-select:focus, .form-textarea:focus, .form-file-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(74, 85, 162, 0.2); }
        body.dark-mode .form-input:focus, body.dark-mode .form-select:focus, body.dark-mode .form-textarea:focus, body.dark-mode .form-file-input:focus { border-color: var(--secondary-color); box-shadow: 0 0 0 2px rgba(120, 149, 203, 0.2); }
        .form-group { margin-bottom: 1.5rem; }
        .form-file-input { padding: 0.5rem; } /* Adjust padding for file input for better look */
        .browse-files-btn { /* Could be a styled label for file input */ display: inline-block; padding: 0.5rem 1rem; border: 1px solid var(--primary-color); color: var(--primary-color); border-radius: 0.375rem; cursor: pointer; font-size:0.875rem; }
        body.dark-mode .browse-files-btn { border-color:var(--secondary-color); color:var(--secondary-color); }
        #theme-toggle-container { position: fixed; top: 1rem; right: 1.5rem; z-index: 1000; }
        #theme-toggle { background: var(--form-bg-light); border: 1px solid var(--input-border-light); border-radius: 0.375rem; padding: 0.5rem; }
        body.dark-mode #theme-toggle { background: var(--input-bg-dark); border: 1px solid var(--input-border-dark); }
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
                <a href="{{ url('/') }}#about-us" class="nav-link">About</a>
                <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                {{-- Theme Toggle in header is more consistent --}}
                <button id="theme-toggle" class="p-2 rounded-md -mr-2">
                    <i class="fas fa-sun text-lg text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-lg text-gray-200 hidden dark:inline"></i>
                </button>
            </nav>
            <div class="md:hidden"> {{-- Mobile menu button placeholder --}}
                <button id="theme-toggle-mobile" class="p-2 rounded-md">
                     <i class="fas fa-sun text-lg text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-lg text-gray-200 hidden dark:inline"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="provider-form-container">
        <div class="text-center mb-8">
            <h1 class="form-section-title">Start Offering Your Services</h1>
            <p class="form-section-subtitle">Tell us about your business to reach new customers.</p>
        </div>

        <h2 class="text-xl font-semibold mb-6 text-gray-700 dark:text-gray-300">Service Provider Registration Form</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Oops! Something went wrong.</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('provider.register.storeDetails') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="name" value="{{ session('registration_data.name', old('name')) }}">
            <input type="hidden" name="email" value="{{ session('registration_data.email', old('email')) }}">
            <input type="hidden" name="password" value="{{ session('registration_data.password') }}">
            <input type="hidden" name="password_confirmation" value="{{ session('registration_data.password') }}">
            <input type="hidden" name="is_provider" value="1">


            <div class="form-group">
                <label for="provider_full_name" class="form-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="provider_full_name" id="provider_full_name" class="form-input" placeholder="Enter your full name" value="{{ old('provider_full_name', session('registration_data.name')) }}" required>
            </div>

            <div class="form-group">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" name="company_name" id="company_name" class="form-input" placeholder="Enter company name" value="{{ old('company_name') }}">
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Choose Category <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address <span class="text-red-500">*</span></label>
                <input type="text" name="address" id="address" class="form-input" placeholder="Enter address" value="{{ old('address') }}" required>
            </div>

            <div class="form-group">
                <label for="phone_number" class="form-label">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone_number" id="phone_number" class="form-input" placeholder="Enter phone number" value="{{ old('phone_number') }}" required>
            </div>

            <div class="form-group">
                <label for="provider_email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="provider_email" id="provider_email" class="form-input" placeholder="Enter email address" value="{{ old('provider_email', session('registration_data.email')) }}" required>
            </div>

            <div class="form-group">
                <label for="professional_description" class="form-label">Short Description/About you <span class="text-red-500">*</span></label>
                <textarea name="professional_description" id="professional_description" rows="4" class="form-textarea" placeholder="Enter a short description" required>{{ old('professional_description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="certificates" class="form-label">Upload any certifications or licenses (Optional)</label>
                <input type="file" name="certificates[]" id="certificates" class="form-file-input" multiple>
                 <small class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">You can select multiple files. Max 2MB per file.</small>
            </div>

            <div class="mt-8 text-right">
                <button type="submit" class="btn-primary px-8 py-3 text-base">
                    Complete Registration
                </button>
            </div>
        </form>
    </main>

<script>
    // Dark Mode Toggle
    const themeToggleDesktop = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    const body = document.body;

    const applyTheme = (theme) => {
        const sunIcons = document.querySelectorAll('.fa-sun');
        const moonIcons = document.querySelectorAll('.fa-moon');
        if (theme === 'dark') {
            body.classList.add('dark-mode'); document.documentElement.classList.add('dark');
            sunIcons.forEach(icon => icon.classList.add('dark:hidden'));
            moonIcons.forEach(icon => { icon.classList.remove('hidden'); icon.classList.add('dark:inline'); });
        } else {
            body.classList.remove('dark-mode'); document.documentElement.classList.remove('dark');
            sunIcons.forEach(icon => icon.classList.remove('dark:hidden'));
            moonIcons.forEach(icon => { icon.classList.add('hidden'); icon.classList.remove('dark:inline'); });
        }
    };
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme) { applyTheme(savedTheme); } else if (prefersDark) { applyTheme('dark'); } else { applyTheme('light'); }

    function toggleTheme() {
        const isDarkMode = body.classList.contains('dark-mode');
        if (isDarkMode) { applyTheme('light'); localStorage.setItem('theme', 'light'); }
        else { applyTheme('dark'); localStorage.setItem('theme', 'dark'); }
    }
    if(themeToggleDesktop) themeToggleDesktop.addEventListener('click', toggleTheme);
    if(themeToggleMobile) themeToggleMobile.addEventListener('click', toggleTheme);
</script>
</body>
</html>
