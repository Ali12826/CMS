<!DOCTYPE html>
{{--
    Optimization: 'str_replace' ensures the language attribute matches the app's locale (e.g., 'en-US').
    This is good for SEO and accessibility.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO & Branding: Title dynamically pulls the app name from .env file --}}
    <title>Login | {{ config('app.name', 'GIGA MALL CMS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    {{--
        PERFORMANCE TIP:
        We are using 'preconnect' to tell the browser to connect to Google Fonts early.
        This makes the text appear faster on slow connections.
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    {{--
        NOTE: You are using the Tailwind CDN.
        For a production website, this is slow.
        Recommendation: Run 'npm install' and use @vite(['resources/css/app.css']) for 10x speed.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Icons Library --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        /* --- 1. THEME CONFIGURATION --- */
        :root {
            /* Centralized colors so you can change the whole theme in one place */
            --primary-color: #0A2342;   /* Dark Blue */
            --secondary-color: #C8A951; /* Gold */
            --soft-bg: #f5f7fa;
            --text-primary: #0A2342;
        }

        /* --- 2. GLOBAL STYLES --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--soft-bg);
            color: var(--text-primary);
        }

        /* Gradient Text Effect for "CMS" */
        .text-gradient {
            background-image: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Glassmorphism Header */
        .glass-header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(200, 169, 81, 0.3);
            box-shadow: 0 4px 15px rgba(10, 35, 66, 0.1);
        }

        /* The Main Login Card */
        .login-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        /* --- 3. CUSTOM INPUT FIELDS (Floating Labels) --- */
        .input-group { position: relative; margin-bottom: 1.75rem; }

        /* The Label styling */
        .input-label {
            position: absolute; left: 3.25rem; top: 1rem;
            font-size: 1rem; font-weight: 500; color: #718096;
            pointer-events: none; transition: all 0.3s;
            background-color: white; padding: 0 0.25rem; z-index: 5;
        }

        /* Animation: Move label up when input is focused or has text */
        .input-group:focus-within .input-label,
        .input-group .form-input:not(:placeholder-shown) ~ .input-label {
            top: -0.6rem; left: 2.75rem; font-size: 0.75rem;
            font-weight: 600; color: var(--secondary-color);
        }

        /* Icons inside input */
        .input-icon {
            position: absolute; left: 1.25rem; top: 50%;
            transform: translateY(-50%); color: #A0AEC0;
            transition: color 0.3s; z-index: 10;
        }
        .input-group:focus-within .input-icon { color: var(--secondary-color); }

        /* The actual input field */
        .form-input {
            width: 100%; padding: 1rem 1.25rem 1rem 3.25rem;
            border: 2px solid #E2E8F0; border-radius: 0.875rem;
            background-color: white; font-size: 1rem;
            transition: all 0.3s; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            outline: none;
        }
        .form-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(200, 169, 81, 0.12);
            transform: translateY(-1px);
        }

        /* Password Eye Icon */
        .password-toggle {
            position: absolute; right: 1.25rem; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            color: #A0AEC0; transition: all 0.3s; z-index: 10;
            padding: 0.25rem;
        }
        .password-toggle:hover { color: var(--secondary-color); }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #0d2d55; /* Slightly lighter blue on hover */
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col">

 {{-- ================= HEADER ================= --}}
    <header class="p-4 sticky top-0 z-50 glass-header">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            {{-- LEFT SIDE: New Stylish Logo --}}
            <a href="{{ url('/') }}" class="group flex items-center gap-3 focus:outline-none">
                {{-- Logo Image with Hover Glow & Tilt --}}
                <div class="relative">
                    {{-- Glow effect behind logo (only visible on hover) --}}
                    <div class="absolute inset-0 bg-blue-500/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <img src="{{ asset('images/logo2.png') }}"
                         class="relative z-10 w-16 md:w-20 object-contain drop-shadow-sm transition-transform duration-500 ease-out transform group-hover:scale-110 group-hover:-rotate-2"
                         alt="CMS Logo">
                </div>

                {{-- Text with Gradient --}}
                <span class="font-extrabold text-3xl text-gradient tracking-tight transition-all duration-300 group-hover:tracking-wide">
                    CMS
                </span>
            </a>

            {{-- RIGHT SIDE: Register Button --}}
            <nav class="hidden md:flex items-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-6 py-2 rounded-lg text-sm font-bold shadow-md transform hover:scale-105 transition-all flex items-center gap-2"
                       style="background-color: var(--secondary-color); color: var(--primary-color);">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Register
                    </a>
                @endif
            </nav>

        </div>
    </header>

    {{-- ================= MAIN CONTENT ================= --}}
    <main class="grow flex items-center justify-center py-12 px-4">

        <div class="max-w-md w-full login-card">

            {{-- Title Section --}}
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gradient">Secure Login</h2>
                <p class="mt-2 text-sm text-gray-600">Access the CMS Command Interface</p>
            </div>

            {{--
                ERROR HANDLING:
                Checks if there are any global errors (like "Invalid password") and displays them.
            --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r flex items-center gap-3">
                    <i data-lucide="alert-circle" class="text-red-500 w-5 h-5"></i>
                    <span class="text-red-700 text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            {{--
                SUCCESS MESSAGE:
                Checks if the user was redirected here with a 'success' flash message (e.g., after registering).
            --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r flex items-center gap-3">
                    <i data-lucide="check-circle" class="text-green-500 w-5 h-5"></i>
                    <span class="text-green-700 text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{--
                LOGIN FORM:
                Action: Points to the 'login.post' route to match web.php.
                Method: POST (Secure).
                Autocomplete: Off (Prevents browser clutter).
            --}}
            <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                @csrf {{-- CRITICAL: Laravel Security Token --}}

                {{-- Username Field --}}
                <div class="input-group">
                    <i data-lucide="user" class="input-icon w-5 h-5"></i>
                    {{--
                        value="{{ old('username') }}" ensures that if login fails,
                        the user doesn't have to re-type their username.
                    --}}
                    <input type="text"
                           class="form-input"
                           id="username"
                           name="username"
                           placeholder=" "
                           required
                           autofocus
                           value="{{ old('username') }}"/>
                    <label for="username" class="input-label">Username</label>
                </div>

                {{-- Password Field --}}
                <div class="input-group">
                    <i data-lucide="lock" class="input-icon w-5 h-5"></i>
                    {{--
                        Note: Name is 'admin_password' to match your AuthController logic.
                    --}}
                    <input type="password"
                           class="form-input"
                           id="password"
                           name="admin_password"
                           placeholder=" "
                           required/>
                    <label for="password" class="input-label">Password</label>

                    {{-- Toggle Eye Icon --}}
                    <span class="password-toggle" id="togglePassword">
                        <i data-lucide="eye" class="w-5 h-5" id="toggleIcon"></i>
                    </span>
                </div>

                {{-- Remember Me Checkbox --}}
                <div class="flex justify-between items-center mb-6 text-sm">
                    <label class="flex items-center gap-2 text-gray-600 font-medium cursor-pointer select-none">
                        <input type="checkbox" name="remember_me" class="rounded border-gray-300 text-indigo-900 focus:ring-indigo-500"/>
                        <span>Remember me</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg flex justify-center items-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Login to Dashboard</span>
                </button>

                {{-- Register Link --}}
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-bold hover:underline" style="color: var(--secondary-color);">Register here</a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="py-8 bg-gray-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center items-center gap-3 mb-4">
                <img src="{{ asset('images/logo7.png') }}" alt="CMS Logo" class="w-16 h-auto object-contain opacity-90">
                <span class="text-gray-200 font-extrabold text-2xl tracking-wide">CMS</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} CMS. Built by GIGA MALL IT Department.</p>
        </div>
    </footer>

    {{-- ================= SCRIPTS ================= --}}
    <script>
        // 1. Initialize Icons (Lucide)
        lucide.createIcons();

        // 2. Password Toggle Logic (Optimized)
        const toggleBtn = document.getElementById('togglePassword');
        const passInput = document.getElementById('password');
        const icon     = document.getElementById('toggleIcon');

        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', () => {
                // Check current type
                const isPassword = passInput.getAttribute('type') === 'password';

                // Switch type
                passInput.setAttribute('type', isPassword ? 'text' : 'password');

                // Switch icon
                icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');

                // Refresh icon rendering
                lucide.createIcons();
            });
        }
    </script>
</body>
</html>
