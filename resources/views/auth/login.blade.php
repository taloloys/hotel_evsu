<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel Don Felipe - Staff & Guest Login Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-serif-display {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .bg-warm-radial {
            background: radial-gradient(circle at top left, #fffbf7 0%, #f6efe7 45%, #ebe0d0 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-warm-radial text-slate-800 antialiased selection:bg-[#a97142] selection:text-white flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="w-full border-b border-[#e8d9c7]/80 bg-white/70 backdrop-blur-md px-4 py-3 sm:px-8">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-[#e5d4bf] bg-white p-1.5 shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe Logo" class="h-full w-full object-contain" width="32" height="32">
                </div>
                <div>
                    <span class="block text-base font-bold text-[#2f1c16] font-serif-display">Hotel Don Felipe</span>
                    <span class="block text-[10px] font-semibold text-[#a97142] uppercase tracking-wider">Management System</span>
                </div>
            </a>

            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#dccdb7] bg-white px-4 py-2 text-xs font-bold text-[#4e342e] shadow-sm transition-all hover:bg-[#fffaf3] hover:border-[#a97142]">
                <i class="fa-solid fa-arrow-left text-xs text-[#a97142]"></i>
                <span>Back to Showcase</span>
            </a>
        </div>
    </header>

    <!-- Main Login Card Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div id="login-card" class="w-full max-w-md overflow-hidden rounded-3xl border border-[#e8d9c7] bg-white/95 p-8 shadow-2xl shadow-[#4e342e]/12 backdrop-blur-md transition-all duration-300">
            
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl border border-[#e5d4bf] bg-gradient-to-br from-[#fffaf5] to-[#f4ece1] shadow-inner">
                    <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe Logo" class="h-14 w-14 object-contain" width="56" height="56">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#2f1c16] font-serif-display">Welcome Back</h1>
                <p class="mt-1 text-xs sm:text-sm text-[#6d4c41]">Sign in to access your authorized staff dashboard</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-700 shadow-sm animate-fade-in" role="alert">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-base text-rose-500"></i>
                        <div>
                            <span class="font-semibold">Authentication Error</span>
                            <p class="mt-0.5 text-xs text-rose-600">{{ $errors->first('username') ?? 'Invalid credentials provided.' }}</p>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="username" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#4e342e]">Username</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#a97142]">
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
                            class="w-full rounded-2xl border border-[#dccdb7] bg-[#fcfaf7] py-3.5 pl-11 pr-4 text-sm text-slate-800 outline-none transition-all duration-200 focus:border-[#a97142] focus:bg-white focus:ring-4 focus:ring-[#a97142]/15">
                    </div>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#4e342e]">Password</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#a97142]">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Enter your password"
                            class="w-full rounded-2xl border border-[#dccdb7] bg-[#fcfaf7] py-3.5 pl-11 pr-12 text-sm text-slate-800 outline-none transition-all duration-200 focus:border-[#a97142] focus:bg-white focus:ring-4 focus:ring-[#a97142]/15">
                        <button
                            type="button"
                            id="togglePassword"
                            aria-label="Toggle password visibility"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition-colors hover:text-[#a97142]">
                            <i id="passwordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="group flex items-center gap-2.5 cursor-pointer text-xs font-medium text-slate-600 hover:text-[#2f1c16]">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#dccdb7] text-[#a97142] focus:ring-[#a97142]/20">
                        <span>Remember me</span>
                    </label>
                    <span class="text-xs text-slate-400">Restricted Access</span>
                </div>

                <button
                    type="submit"
                    class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-2xl bg-gradient-to-r from-[#4e342e] via-[#6d4c41] to-[#a97142] py-3.5 px-4 text-sm font-semibold text-white shadow-lg shadow-[#6d4c41]/25 transition-all duration-300 hover:opacity-95 hover:shadow-xl hover:shadow-[#a97142]/30 active:scale-[0.99]">
                    <span>Sign In to System</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1"></i>
                </button>

                <div class="rounded-2xl border border-[#f0dfc9] bg-[#fffaf3] p-3.5 text-center text-xs text-[#7a5738]">
                    <i class="fa-solid fa-shield-halved mr-1.5 text-[#a97142]"></i>
                    Authorized Staff Portal. Enter valid system credentials.
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 text-center text-xs text-slate-500 border-t border-[#e8d9c7]/60 bg-white/40">
        © {{ date('Y') }} Hotel Don Felipe. All rights reserved.
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
