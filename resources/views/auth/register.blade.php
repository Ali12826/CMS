<!DOCTYPE html>
{{--
    1. SEO & ACCESSIBILITY:
    We set the language dynamically based on the app's configuration.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | {{ config('app.name', 'GIGA MALL GMS') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    {{--
        2. PERFORMANCE OPTIMIZATION:
        'preconnect' tells the browser to establish a connection to Google Fonts
        BEFORE the page finishes loading. This makes text appear faster.
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    {{--
        NOTE: Tailwind CDN is used here for easy testing.
        For production speed, replace this with: @vite(['resources/css/app.css'])
    --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Icons Library (Lucide) --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        /* --- THEME CONFIGURATION --- */
        :root {
            --primary-color: #0A2342;   /* Navy Blue */
            --secondary-color: #C8A951; /* Gold */
            --soft-bg: #f5f7fa;
            --text-primary: #0A2342;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--soft-bg);
            color: var(--text-primary);
        }

        /* --- VISUAL EFFECTS --- */
        .text-gradient {
            background-image: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .register-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .glass-header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(200, 169, 81, 0.3);
            box-shadow: 0 4px 15px rgba(10, 35, 66, 0.1);
        }

        /* --- FORM STYLING --- */
        .input-group { position: relative; margin-bottom: 1.5rem; }

        /* Floating Label Logic */
        .input-label {
            position: absolute; left: 3.25rem; top: 1rem;
            font-size: 1rem; font-weight: 500; color: #718096;
            pointer-events: none; transition: all 0.3s;
            background-color: white; padding: 0 0.25rem; z-index: 5;
        }

        /* Move label up when:
           1. Input is focused
           2. Input has text (placeholder not shown)
           3. Select box has a valid option picked
        */
        .input-group:focus-within .input-label,
        .input-group .form-input:not(:placeholder-shown) ~ .input-label,
        .input-group .form-select:valid ~ .input-label {
            top: -0.6rem; left: 2.75rem; font-size: 0.75rem;
            font-weight: 600; color: var(--secondary-color);
        }

        /* Input Icons */
        .input-icon {
            position: absolute; left: 1.25rem; top: 50%;
            transform: translateY(-50%); color: #A0AEC0;
            transition: color 0.3s; z-index: 10;
        }
        .input-group:focus-within .input-icon { color: var(--secondary-color); }

        /* Input Fields */
        .form-input, .form-select {
            width: 100%; padding: 1rem 1.25rem 1rem 3.25rem;
            border: 2px solid #E2E8F0; border-radius: 0.875rem;
            background-color: white; font-size: 1rem;
            transition: all 0.3s; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            outline: none;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(200, 169, 81, 0.12);
            transform: translateY(-1px);
        }

        /* Custom Select Arrow */
        .form-select {
            appearance: none; /* Removes default browser arrow */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23A0AEC0'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.25rem;
        }

        /* Password Eye Toggle */
        .password-toggle {
            position: absolute; right: 1.25rem; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            color: #A0AEC0; transition: all 0.3s;
            z-index: 10; padding: 0.25rem;
        }
        .password-toggle:hover { color: var(--secondary-color); }

        /* Submit Button */
        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #0d2d55;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col">

    {{-- ================= HEADER ================= --}}
    <header class="p-4 sticky top-0 z-100 glass-header">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center space-x-3 transition duration-300 hover:scale-105">
                <img src="{{ asset('images/logo2.png') }}" class="w-16 md:w-20 object-contain" alt="GMS Logo">
                <span class="font-extrabold text-3xl text-gradient">GMS</span>
            </a>

            {{-- Login Button (Visible on Desktop) --}}
            <nav class="hidden md:flex items-center">
                <a href="{{ route('login') }}"
                   class="px-6 py-2 rounded-lg text-sm font-bold shadow-md text-white flex items-center gap-2 transition-transform hover:scale-105"
                   style="background-color: var(--primary-color);">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Login
                </a>
            </nav>
        </div>
    </header>

    {{-- ================= MAIN CONTENT ================= --}}
    <main class="grow flex items-center justify-center py-12 px-4">

        <div class="max-w-md w-full register-card">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gradient">Create Account</h2>
                <p class="mt-2 text-sm text-gray-600">Register for GMS Employee Access</p>
            </div>

            {{-- 3. ERROR HANDLING: Display Validation Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r flex items-center gap-3">
                    <i data-lucide="alert-circle" class="text-red-500 w-5 h-5 shrink-0"></i>
                    <span class="text-red-700 text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" autocomplete="off">
                @csrf {{-- Laravel Security Token --}}

                {{-- Full Name --}}
                <div class="input-group">
                    <i data-lucide="user" class="input-icon w-5 h-5"></i>
                    {{-- value="{{ old(...) }}" keeps the typed text if validation fails elsewhere --}}
                    <input type="text" class="form-input" id="fullname" name="fullname" placeholder=" " required value="{{ old('fullname') }}"/>
                    <label for="fullname" class="input-label">Full Name</label>
                </div>

                {{-- Username --}}
                <div class="input-group">
                    <i data-lucide="at-sign" class="input-icon w-5 h-5"></i>
                    <input type="text" class="form-input" id="username" name="username" placeholder=" " required pattern="[a-zA-Z0-9_-]{3,20}" value="{{ old('username') }}"/>
                    <label for="username" class="input-label">Username</label>
                </div>

                {{-- Contact --}}
                <div class="input-group">
                    <i data-lucide="phone" class="input-icon w-5 h-5"></i>
                    <input type="tel" class="form-input" id="contact" name="contact" placeholder=" " required value="{{ old('contact') }}"/>
                    <label for="contact" class="input-label">Contact Number</label>
                </div>

                {{-- Department Select --}}
                <div class="input-group">
                    <i data-lucide="briefcase" class="input-icon w-5 h-5"></i>
                    {{--
                       IMPORTANT: 'required' is needed for the CSS :valid selector to float the label.
                       The first option has an empty value to ensure the user forces a selection.
                    --}}
                    <select class="form-select" id="dept_id" name="dept_id" required>
                        <option value="" disabled selected></option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}" {{ old('dept_id') == $dept->dept_id ? 'selected' : '' }}>
                                {{ $dept->dept_name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="dept_id" class="input-label">Department</label>
                </div>

                {{-- Password --}}
                <div class="input-group">
                    <i data-lucide="lock" class="input-icon w-5 h-5"></i>
                    <input type="password" class="form-input" id="password" name="password" placeholder=" " required minlength="6"/>
                    <label for="password" class="input-label">Password</label>
                    <span class="password-toggle" onclick="togglePass('password', 'toggleIcon')">
                        <i data-lucide="eye" class="w-5 h-5" id="toggleIcon"></i>
                    </span>
                </div>

                {{-- Confirm Password --}}
                <div class="input-group">
                    <i data-lucide="check-circle" class="input-icon w-5 h-5"></i>
                    <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" placeholder=" " required minlength="6"/>
                    <label for="password_confirmation" class="input-label">Confirm Password</label>
                    <span class="password-toggle" onclick="togglePass('password_confirmation', 'toggleConfirmIcon')">
                        <i data-lucide="eye" class="w-5 h-5" id="toggleConfirmIcon"></i>
                    </span>
                </div>

                {{-- Register Button --}}
                <button type="submit" class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg flex justify-center items-center gap-2 mt-2">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    <span>Create Account</span>
                </button>

                {{-- Login Link --}}
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold hover:underline" style="color: var(--secondary-color);">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="py-8 bg-gray-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center items-center gap-3 mb-4">
                 <img src="{{ asset('images/logo7.png') }}" alt="GMS Logo" class="w-16 h-auto object-contain opacity-90">
                <span class="text-gray-200 font-extrabold text-2xl tracking-wide">GMS</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} GMS. Built for GIGA MALL IT Operations.</p>
        </div>
    </footer>

    {{-- ================= SCRIPTS ================= --}}
    <script>
        // 1. Initialize Icons
        lucide.createIcons();

        // 2. Optimized Password Toggle Function
        // One function handles both 'password' and 'confirm password' fields
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input && icon) {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                lucide.createIcons(); // Refresh icon
            }
        }
    </script>
</body>
</html>
