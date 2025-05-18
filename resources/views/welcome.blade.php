<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Easy Services - Order Services Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    {{-- Fallback Tailwind styles (your provided snippet) if Vite isn't running --}}
    <style>
        
    </style>
    @endif

    <style>
        /* Custom CSS */
        :root {
            --primary-color: #4A55A2; /* A blue-purple like in the PDF */
            --primary-hover-color: #3A4382;
            --secondary-color: #7895CB;
            --text-light: #F8F9FA;
            --text-dark: #343A40;
            --bg-light: #FFFFFF;
            --bg-dark: #1a202c; /* Tailwind gray-800 */
            --card-bg-light: #FFFFFF;
            --card-bg-dark: #2d3748; /* Tailwind gray-700 */
            --border-light: #E2E8F0; /* Tailwind gray-300 */
            --border-dark: #4A5568;  /* Tailwind gray-600 */
        }

        html.dark {
             color-scheme: dark;
        }

        body {
            font-family: 'Poppins', 'Instrument Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-light);
        }

        .nav-link {
            color: var(--text-dark);
        }
        body.dark-mode .nav-link {
            color: var(--text-light);
        }
        .nav-link:hover {
            color: var(--primary-color);
        }
        body.dark-mode .nav-link:hover {
            color: var(--secondary-color);
        }


        .btn-primary {
            background-color: var(--primary-color);
            color: var(--text-light) !important; /* Ensure text is light for primary buttons */
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem; /* Tailwind rounded-md */
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }
        .btn-secondary { /* For login button to look like PDF */
            background-color: #e2e8f0; /* Light gray, similar to Tailwind gray-200 */
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }
        .btn-secondary:hover {
            background-color: #cbd5e0; /* Tailwind gray-300 */
        }
        body.dark-mode .btn-secondary {
            background-color: var(--border-dark); /* Darker gray for dark mode */
            color: var(--secondary-color);
        }
        body.dark-mode .btn-secondary:hover {
            background-color: #718096; /* Tailwind gray-500 */
        }


        .btn-outline {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.6rem 1.25rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, color 0.3s ease;
             text-decoration: none;
            display: inline-block;
        }
        .btn-outline:hover {
            background-color: var(--primary-color);
            color: var(--text-light);
        }
        body.dark-mode .btn-outline {
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }
        body.dark-mode .btn-outline:hover {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }


        .hero-section {
            background-image: url('https://picsum.photos/seed/hero/1920/1080'); /* Replace with your image */
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .service-card {
            background-color: var(--card-bg-light);
            border: 1px solid var(--border-light);
            border-radius: 0.5rem; /* Tailwind rounded-lg */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden; /* To ensure rounded corners with image */
        }
        body.dark-mode .service-card {
            background-color: var(--card-bg-dark);
            border-color: var(--border-dark);
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); /* Tailwind shadow-lg */
        }
        body.dark-mode .service-card:hover {
             box-shadow: 0 10px 15px -3px rgba(255,255,255,0.05), 0 4px 6px -2px rgba(255,255,255,0.02);
        }


        /* Testimonial Slider */
        .testimonial-slider { overflow: hidden; position: relative; }
        .testimonial-slide { display: none; animation: fadeEffect 1.5s; }
        .testimonial-slide.active { display: flex; } /* Changed to flex for layout */
        @keyframes fadeEffect { from {opacity: .4} to {opacity: 1} }
        .testimonial-dots { text-align: center; padding: 10px; }
        .dot { cursor: pointer; height: 12px; width: 12px; margin: 0 3px; background-color: #bbb; border-radius: 50%; display: inline-block; transition: background-color 0.6s ease; }
        body.dark-mode .dot { background-color: #718096; } /* Tailwind gray-500 */
        .dot.active { background-color: var(--primary-color); }
        body.dark-mode .dot.active { background-color: var(--secondary-color); }

        #theme-toggle { cursor: pointer; }
    </style>

</head>

<body class="antialiased">
    <!-- Header -->
    <header class="py-4 shadow-sm dark:shadow-md fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-primary-color dark:text-secondary-color">
                 <img src="{{ asset('images/logo.png') }}" alt="Easy Logo" class="h-5">
            </a>
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ url('/') }}" class="nav-link hover:text-primary-color dark:hover:text-secondary-color font-medium">Home</a>
                <a href="#about-us" class="nav-link hover:text-primary-color dark:hover:text-secondary-color font-medium">About Us</a>
                <a href="#popular-services" class="nav-link hover:text-primary-color dark:hover:text-secondary-color font-medium">Provide service</a>
                <a href="{{ route('contact') }}" class="nav-link hover:text-primary-color dark:hover:text-secondary-color font-medium">Contact us</a>
            </nav>
            <div class="flex items-center space-x-2 sm:space-x-4">
                <button id="theme-toggle" class="p-2 rounded-md">
                    <i class="fas fa-sun text-xl text-gray-700 dark:hidden"></i>
                    <i class="fas fa-moon text-xl text-gray-200 hidden dark:inline"></i>
                </button>
                 @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">Dashboard</a>
                    @else
                        {{-- For mobile: Login might be hidden or a hamburger menu --}}
                        {{-- For desktop, per PDF: Login then Register --}}
                        <a href="{{ route('login') }}" class="btn-secondary text-sm hidden sm:inline-block">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary text-sm">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section text-white h-[60vh] md:h-[70vh] flex items-center justify-center text-center pt-16">
        <div class="relative z-10 max-w-3xl mx-auto px-4">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">Discover Services Now</h1>
            <p class="text-lg sm:text-xl mb-8">Find the best services tailored to your needs quickly and easily.</p>
            <a href="{{ route('login') }}" class="btn-primary text-lg px-8 py-3">Search</a>
        </div>
    </section>

    <!-- Popular Services Section -->
    <section id="popular-services" class="py-16 lg:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Popular Services</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Service Card 1 -->
                <div class="service-card shadow-lg">
                    <img src="https://picsum.photos/seed/painting/400/250" alt="House Painting" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">House Painting</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Build modern websites</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Learn coding techniques</p>
                        <a href="{{ route('register') }}" class="btn-outline text-sm w-full text-center">Discover</a>
                    </div>
                </div>
                <!-- Service Card 2 -->
                <div class="service-card shadow-lg">
                    <img src="https://picsum.photos/seed/cleaning/400/250" alt="Cleaning" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Cleaning</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Capture stunning moments</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Master editing skills</p>
                        <a href="{{ route('register') }}" class="btn-outline text-sm w-full text-center">Discover</a>
                    </div>
                </div>
                <!-- Service Card 3 -->
                <div class="service-card shadow-lg">
                    <img src="https://picsum.photos/seed/housekeeping/400/250" alt="House keeping" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">House keeping</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Unleash your creativity</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Learn design principles</p>
                        <a href="{{ route('register') }}" class="btn-outline text-sm w-full text-center">Discover</a>
                    </div>
                </div>
                <!-- Service Card 4 -->
                <div class="service-card shadow-lg">
                    <img src="https://picsum.photos/seed/transport/400/250" alt="Transportation" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Transportation</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Achieve your fitness goals</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Access wellness tips</p>
                        <a href="{{ route('register') }}" class="btn-outline text-sm w-full text-center">Discover</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="testimonial-slider max-w-3xl mx-auto text-center">
                <!-- Testimonial Slide 1 -->
                <div class="testimonial-slide active items-center">
                    <img src="https://picsum.photos/seed/emily/100/100" alt="Emily Carter" class="w-24 h-24 rounded-full mx-auto md:mx-0 md:mr-8 mb-4 md:mb-0 object-cover">
                    <div class="md:text-left">
                        <div class="text-yellow-400 mb-2">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-lg italic mb-4">"The service was exceptional! Everything was delivered on time and exceeded my expectations. Highly recommend!"</p>
                        <h4 class="font-semibold text-lg">Emily Carter</h4>
                        <p class="text-gray-600 dark:text-gray-400">Marketing Manager</p>
                    </div>
                </div>
                <!-- Testimonial Slide 2 -->
                <div class="testimonial-slide items-center">
                    <img src="https://picsum.photos/seed/john/100/100" alt="John Doe" class="w-24 h-24 rounded-full mx-auto md:mx-0 md:mr-8 mb-4 md:mb-0 object-cover">
                     <div class="md:text-left">
                        <div class="text-yellow-400 mb-2">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        </div>
                        <p class="text-lg italic mb-4">"A fantastic platform with very responsive providers. Made my life so much easier!"</p>
                        <h4 class="font-semibold text-lg">John Doe</h4>
                        <p class="text-gray-600 dark:text-gray-400">Small Business Owner</p>
                    </div>
                </div>
                <!-- Testimonial Slide 3 -->
                <div class="testimonial-slide items-center">
                    <img src="https://picsum.photos/seed/sarah/100/100" alt="Sarah Smith" class="w-24 h-24 rounded-full mx-auto md:mx-0 md:mr-8 mb-4 md:mb-0 object-cover">
                     <div class="md:text-left">
                        <div class="text-yellow-400 mb-2">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="text-lg italic mb-4">"Great variety of services and easy to use. Found a reliable plumber in minutes."</p>
                        <h4 class="font-semibold text-lg">Sarah Smith</h4>
                        <p class="text-gray-600 dark:text-gray-400">Homeowner</p>
                    </div>
                </div>
                <!-- Testimonial Slide 4 -->
                 <div class="testimonial-slide items-center">
                    <img src="https://picsum.photos/seed/mike/100/100" alt="Mike Brown" class="w-24 h-24 rounded-full mx-auto md:mx-0 md:mr-8 mb-4 md:mb-0 object-cover">
                     <div class="md:text-left">
                        <div class="text-yellow-400 mb-2">
                           <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-lg italic mb-4">"Absolutely stellar experience. The professionals are top-notch."</p>
                        <h4 class="font-semibold text-lg">Mike Brown</h4>
                        <p class="text-gray-600 dark:text-gray-400">Freelancer</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-dots mt-8">
                <span class="dot active" onclick="currentSlide(0)"></span>
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="py-16 lg:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">About Us</h2>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">
                        "Easy Services" (Prestataire) is dedicated to providing seamless solutions for service providers, ensuring efficiency and reliability.
                    </p>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Innovation</h3>
                            <p class="text-gray-600 dark:text-gray-400">Our pioneering approach leverages cutting-edge technology to streamline communication and decision-making processes.</p>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Customer-Centric</h3>
                            <p class="text-gray-600 dark:text-gray-400">We are committed to building long-lasting relationships with our clients by prioritizing their needs and exceeding expectations.</p>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Expertise</h3>
                            <p class="text-gray-600 dark:text-gray-400">With a team of seasoned professionals, we bring years of industry expertise and innovative thinking to every project.</p>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Integrity</h3>
                            <p class="text-gray-600 dark:text-gray-400">At Prestataire, transparency and honesty are at the heart of everything we do, ensuring trust and integrity in all our interactions.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <img src="https://picsum.photos/seed/team/600/400" alt="Our Team" class="rounded-lg shadow-xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Become a Service Provider Section -->
    <section id="become-provider" class="py-16 lg:py-24 bg-gray-100 dark:bg-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <img src="https://picsum.photos/seed/provider/600/400" alt="Service Provider" class="rounded-lg shadow-xl w-full">
                </div>
                <div class="order-1 md:order-2 text-center md:text-left">
                    <h2 class="text-3xl font-bold mb-6">Become a Service Provider</h2>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">
                        Occaecat est ipsum reprehenderit reprehenderit veniam anim laborum est esse duis occaecat reprehenderit pariatur. Join our platform and reach more clients.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('register') }}" class="btn-primary text-lg px-8 py-3">Serve</a>
                        <a href="{{ route('learn-more') }}" class="btn-outline text-lg px-8 py-3">Learn more</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-gray-800 dark:bg-black text-gray-300 dark:text-gray-400">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-6">
                 <a href="{{ url('/') }}" class="text-3xl font-bold text-white">Order Service</a>
            </div>
            <div class="mb-6 space-x-6">
                <a href="#" class="hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-white">Terms of Service</a>
                <a href="#" class="hover:text-white">Sitemap</a>
            </div>
            <div class="mb-6 text-xl space-x-6">
                <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-youtube"></i></a>
            </div>
            <p class="text-sm">© {{ date('Y') }} Easy Services. All rights reserved.</p>
            <p class="text-xs mt-2">Inspired by "Prestataire", made with passion.</p>
        </div>
    </footer>

<script>
    // Dark Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sunIcon = themeToggle.querySelector('.fa-sun');
    const moonIcon = themeToggle.querySelector('.fa-moon');

    // Check for saved theme preference
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        body.classList.add('dark-mode');
        document.documentElement.classList.add('dark'); // For Tailwind's dark: variant
        sunIcon.classList.add('dark:hidden');
        moonIcon.classList.remove('hidden');
        moonIcon.classList.add('dark:inline');

    } else {
        sunIcon.classList.remove('dark:hidden');
        moonIcon.classList.add('hidden');
        moonIcon.classList.remove('dark:inline');
    }

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        document.documentElement.classList.toggle('dark');

        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            sunIcon.classList.add('dark:hidden');
            moonIcon.classList.remove('hidden');
            moonIcon.classList.add('dark:inline');
        } else {
            localStorage.setItem('theme', 'light');
            sunIcon.classList.remove('dark:hidden');
            moonIcon.classList.add('hidden');
            moonIcon.classList.remove('dark:inline');
        }
    });

    // Testimonial Slider
    let slideIndex = 0;
    const slides = document.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.dot');

    function showSlides() {
        if (slides.length === 0 || dots.length === 0) return; // Don't run if no slides/dots

        slides.forEach((slide, index) => {
            slide.classList.remove('active');
            slide.style.display = 'none'; // Hide all
            dots[index].classList.remove('active');
        });

        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1 }

        slides[slideIndex - 1].style.display = 'flex'; // Show current, use flex to align image and text
        slides[slideIndex - 1].classList.add('active');
        dots[slideIndex - 1].classList.add('active');

        setTimeout(showSlides, 5000); // Change image every 5 seconds
    }

    function currentSlide(n) {
        if (slides.length === 0 || dots.length === 0) return;

        slides.forEach((slide, index) => {
            slide.classList.remove('active');
            slide.style.display = 'none';
            dots[index].classList.remove('active');
        });
        slideIndex = n + 1; // n is 0-indexed
        if (slideIndex > slides.length) { slideIndex = 1 }
        if (slideIndex < 1) { slideIndex = slides.length }

        slides[slideIndex - 1].style.display = 'flex';
        slides[slideIndex - 1].classList.add('active');
        dots[slideIndex - 1].classList.add('active');
    }

    // Ensure DOM is loaded before trying to access elements for slider
    document.addEventListener('DOMContentLoaded', (event) => {
        if (slides.length > 0 && dots.length > 0) {
             showSlides(); // Start the slideshow
        }
    });

</script>

</body>
</html>
