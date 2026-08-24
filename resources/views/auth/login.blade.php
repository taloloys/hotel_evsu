<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HTM Department - Staff & Guest Login Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        .font-display {
            font-family: 'Franklin Gothic Medium', 'Franklin Gothic', 'Arial Black', sans-serif;
        }
        .font-body {
            font-family: 'Lucida Fax', 'Georgia', serif;
        }

        body {
            font-family: 'Lucida Fax', 'Georgia', serif;
        }
        .glass-header {
            background: rgba(194, 168, 137, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .bg-warm-radial {
            background: radial-gradient(circle at top left, #f8f3ed 0%, #e8dbcb 45%, #c2a889 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-[#504538] antialiased selection:bg-[#334c42] selection:text-white flex flex-col justify-between font-body">

    <!-- Top Navigation Header -->
    <header class="w-full border-b border-[#827567]/30 glass-header px-4 py-3 sm:px-8 shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="HTM Department Logo" class="h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105" width="48" height="48">
                <div>
                    <span class="block text-base font-bold tracking-tight text-[#504538] font-display">Hospitality & Tourism</span>
                    <span class="block text-[10px] font-semibold tracking-wider text-[#334c42] uppercase font-body">Management Department</span>
                </div>
            </a>

            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#827567] bg-white/90 px-4 py-2 text-xs font-bold text-[#504538] shadow-sm transition-all hover:bg-[#c2a889] hover:border-[#504538] font-body">
                <i class="fa-solid fa-arrow-left text-xs text-[#504538]"></i>
                <span>Back to Showcase</span>
            </a>
        </div>
    </header>

    <!-- Main Login Card Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div id="login-card" class="w-full max-w-md overflow-hidden rounded-3xl border border-[#827567]/30 bg-white/95 p-8 shadow-2xl shadow-[#504538]/10 backdrop-blur-md transition-all duration-300">
            
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="HTM Department Logo" class="h-24 w-auto object-contain" width="96" height="96">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#504538] font-display">Welcome Back</h1>
                <p class="mt-1 text-xs sm:text-sm text-[#827567] font-body">Sign in to access your authorized staff dashboard</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-700 shadow-sm animate-fade-in" role="alert">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-base text-rose-500"></i>
                        <div>
                            <span class="font-semibold font-display">Authentication Error</span>
                            <p class="mt-0.5 text-xs text-rose-600 font-body">{{ $errors->first('username') ?? 'Invalid credentials provided.' }}</p>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="username" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#504538] font-body">Username</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#334c42]">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            placeholder="Enter your username"
                            class="w-full rounded-2xl border border-[#827567]/40 bg-[#fcfaf7] py-3.5 pl-11 pr-4 text-sm text-[#504538] font-body outline-none transition-all duration-200 focus:border-[#334c42] focus:bg-white focus:ring-4 focus:ring-[#334c42]/15">
                    </div>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#504538] font-body">Password</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#334c42]">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Enter your password"
                            class="w-full rounded-2xl border border-[#827567]/40 bg-[#fcfaf7] py-3.5 pl-11 pr-12 text-sm text-[#504538] font-body outline-none transition-all duration-200 focus:border-[#334c42] focus:bg-white focus:ring-4 focus:ring-[#334c42]/15">
                        <button
                            type="button"
                            id="togglePassword"
                            aria-label="Toggle password visibility"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-[#827567] transition-colors hover:text-[#334c42]">
                            <i id="passwordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="group flex items-center gap-2.5 cursor-pointer text-xs font-medium text-[#827567] hover:text-[#504538] font-body">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#827567] text-[#334c42] focus:ring-[#334c42]/20">
                        <span>Remember me</span>
                    </label>
                    <span class="text-xs text-[#627e71] cursor-default hover:underline font-body">Restricted Access</span>
                </div>

                <button
                    type="submit"
                    class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-2xl bg-[#334c42] py-3.5 px-4 text-sm font-semibold text-white shadow-lg shadow-[#334c42]/25 transition-all duration-300 hover:bg-[#627e71] active:scale-[0.99] font-body">
                    <span>Sign In to System</span>
                    <i class="fa-solid fa-arrow-right text-xs text-[#c2a889] transition-transform duration-200 group-hover:translate-x-1"></i>
                </button>

                <div class="rounded-2xl border border-[#827567]/30 bg-[#c2a889]/30 p-3.5 text-center text-xs text-[#504538] font-body">
                    <i class="fa-solid fa-shield-halved mr-1.5 text-[#334c42]"></i>
                    Authorized Staff Portal. Enter valid system credentials.
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 text-center text-xs text-[#504538]/70 border-t border-[#827567]/30 bg-[#c2a889]/40 font-body">
        © {{ date('Y') }} EVSU Hospitality & Tourism Management Department. All rights reserved.
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof sessionStorage !== 'undefined') {
            sessionStorage.removeItem('isFullscreen');
        }
        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        const icon = document.getElementById('passwordIcon');

        if (toggle && password && icon) {
            toggle.addEventListener('click', function () {
                if (password.type === 'password') {
                    password.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    password.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.focus();
        }
    });
    </script>
</body>
</html>
