<!DOCTYPE html>
{{--
    Optimization: Matches app locale for SEO
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'GIGA MALL CMS') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    {{--
        PERFORMANCE OPTIMIZATION: Prefetch Login & Register Pages
       This downloads the login page in the background so it opens INSTANTLY when clicked.
    --}}
    <link rel="prefetch" href="{{ route('login') }}">
    @if (Route::has('register'))
        <link rel="prefetch" href="{{ route('register') }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    {{--
       ⚠️ CRITICAL NOTE: You are using the Tailwind CDN.
       For maximum speed, run 'npm run build' and use @vite(['resources/css/app.css'])
    --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        /* --- THEME & PERFORMANCE VARIABLES --- */
        :root {
            --primary-color: #0A2342;
            --primary-dark: #071930;
            --secondary-color: #C8A951;
            --soft-bg: #f5f7fa;
            --text-primary: #0A2342;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--soft-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            text-rendering: optimizeLegibility; /* Improves text speed */
        }

        /* --- VISUAL UTILITIES --- */
        .text-gradient {
            background-image: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(200, 169, 81, 0.3);
            box-shadow: 0 4px 15px rgba(10, 35, 66, 0.1);
            will-change: transform; /* Hint to browser to optimize rendering */
        }

        .soft-card {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .soft-card:hover {
            transform: translateY(-5px);
            border-color: var(--secondary-color);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            transition: all 0.2s ease; /* Faster transition feels snappier */
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 35, 66, 0.25);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            filter: brightness(110%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(200, 169, 81, 0.25);
        }

        /* Mobile Menu */
        #mobile-menu {
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 0; overflow: hidden;
            background-color: white;
            border-bottom: 2px solid var(--secondary-color);
        }
        #mobile-menu.open { max-height: 400px; }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen">

    {{-- ================= HEADER ================= --}}
<header class="p-4 sticky top-0 z-50 glass-header">
    <div class="max-w-7xl mx-auto flex justify-between items-center">

        {{-- LEFT SIDE: Logo (Exact match to your requested code) --}}
        <a href="{{ url('/') }}" class="group flex items-center gap-3 focus:outline-none">
            {{-- Logo Image with Hover Glow & Tilt --}}
            <div class="relative">
                {{-- Glow effect behind logo (only visible on hover) --}}
                <div class="absolute inset-0 bg-blue-500/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <img src="{{ asset('images/logo2.png') }}"
                     class="relative z-10 w-16 md:w-20 object-contain drop-shadow-sm transition-transform duration-500 ease-out transform group-hover:scale-110 group-hover:-rotate-2"
                     alt="CMS Logo"
                     loading="eager">
            </div>

            {{-- Text with Gradient --}}
            <span class="font-extrabold text-3xl text-gradient tracking-tight transition-all duration-300 group-hover:tracking-wide">
                CMS
            </span>
        </a>

        {{-- RIGHT SIDE: Desktop Menu (Features + Buttons) --}}
        <div class="hidden md:flex items-center space-x-6">

            {{-- Features Link --}}
            <a href="#features" class="font-medium text-gray-600 hover:text-gray-900 transition">Features</a>

            {{-- Register Button --}}
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="px-6 py-2 rounded-lg text-sm font-bold shadow-md transform hover:scale-105 transition-all flex items-center gap-2"
                   style="background-color: var(--secondary-color); color: var(--primary-color);">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Register
                </a>
            @endif

            {{-- Login Button --}}
            <a href="{{ route('login') }}" class="btn-primary px-6 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Login
            </a>
        </div>

        {{-- MOBILE MENU BUTTON (Hamburger) --}}
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" class="focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <i data-lucide="menu" class="w-7 h-7" style="color: var(--primary-color);"></i>
            </button>
        </div>

    </div>

    {{-- Mobile Dropdown Menu (Hidden by default) --}}
    <div id="mobile-menu" class="md:hidden shadow-xl mt-2 rounded-lg overflow-hidden bg-white">
        <div class="px-4 pt-4 pb-6 space-y-3">
            <a href="#features" class="block px-3 py-2 rounded-md text-base font-bold text-gray-700 hover:bg-gray-50">Features</a>
            <div class="border-t border-gray-100 pt-4 flex flex-col gap-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-secondary w-full text-center px-4 py-3 rounded-lg font-bold">Register</a>
                @endif
                <a href="{{ route('login') }}" class="btn-primary w-full text-center px-4 py-3 rounded-lg font-bold">Login</a>
            </div>
        </div>
    </div>
</header>
    {{-- ================= HERO SECTION ================= --}}
    <main class="grow pt-32 md:pt-40 lg:pt-48 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 animate-fade-up">
                <span class="text-gradient">GIGA MALL CMS</span>
            </h1>

            <p class="text-lg md:text-2xl text-gray-600 font-medium max-w-3xl mx-auto mb-10 leading-relaxed animate-fade-up" style="animation-delay: 0.1s;">
                The official Complaint management ecosystem designed to streamline operations and boost productivity for Departments.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-up" style="animation-delay: 0.2s;">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-secondary px-8 py-4 rounded-xl text-lg font-bold shadow-lg flex items-center justify-center gap-2">
                        <i data-lucide="user-plus" class="w-5 h-5"></i> Register
                    </a>
                @endif
                <a href="{{ route('login') }}" class="btn-primary px-8 py-4 rounded-xl text-lg font-bold shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i> Log in
                </a>
            </div>
        </div>

        {{-- ================= FEATURES SECTION ================= --}}
        <section id="features" class="mt-24 md:mt-32 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fade-up" style="animation-delay: 0.3s;">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">System Capabilities</h2>
                <div class="w-24 h-1 bg-[#C8A951] mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 animate-fade-up" style="animation-delay: 0.4s;">
                {{-- Feature Cards --}}
                <div class="soft-card text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="check-circle" class="w-8 h-8 text-[#0A2342]"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-[#0A2342]">Complaint Management</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">End-to-end workflow tracking from initiation to completion.</p>
                </div>

                <div class="soft-card text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-50 mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="bar-chart-2" class="w-8 h-8 text-[#C8A951]"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-[#0A2342]">Live Analytics</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Real-time dashboards providing insights into team performance.</p>
                </div>

                <div class="soft-card text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="filter" class="w-8 h-8 text-[#0A2342]"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-[#0A2342]">Smart Filtering</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Dynamic sorting engines for rapid data access and reporting.</p>
                </div>

                <div class="soft-card text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-50 mb-6 group-hover:scale-110 transition-transform">
                        <i data-lucide="shield" class="w-8 h-8 text-[#C8A951]"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-[#0A2342]">Secure Core</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Enterprise-grade security ensuring data integrity and safety.</p>
                </div>
            </div>
        </section>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-gray-900 text-white py-10 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo7.png') }}" alt="CMS Logo" class="w-12 h-auto opacity-80" loading="lazy">
                <div class="text-left">
                    <span class="block text-xl font-bold text-gradient">CMS</span>
                    <p class="text-gray-500 text-xs">© {{ date('Y') }} Giga Mall IT Department</p>
                </div>
            </div>
            <div class="flex space-x-6 text-sm text-gray-400">
                <a href="#" class="hover:text-white transition">Privacy</a>
                <a href="#" class="hover:text-white transition">Terms</a>
                <a href="#" class="hover:text-white transition">Support</a>
            </div>
        </div>
    </footer>

    {{-- ================= SCRIPTS ================= --}}
    <script>
        lucide.createIcons();

        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = menuBtn.querySelector('i');

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('open');
                const isOpen = mobileMenu.classList.contains('open');
                menuIcon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
                lucide.createIcons();
            });
        }
    </script>
</body>
</html>
