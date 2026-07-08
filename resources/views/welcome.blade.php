<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Don Felipe</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_#fffaf5_0%,_#f6efe7_45%,_#efe2d0_100%)] text-slate-800">

<div class="min-h-screen flex items-center justify-center px-4 py-8 lg:px-8">
    <div class="w-full max-w-6xl overflow-hidden rounded-[2rem] border border-[#e8d9c7] bg-white/90 shadow-[0_25px_80px_rgba(70,45,30,0.16)] backdrop-blur">
        <div class="grid lg:grid-cols-[1.05fr_0.95fr]">

            <div class="hidden lg:flex relative items-center justify-center overflow-hidden bg-gradient-to-br from-[#4d3126] via-[#6d4c41] to-[#a97142] p-12 text-white">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.26),_transparent_45%)]"></div>
                <div class="relative z-10 max-w-md text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-white/30 bg-white/15 shadow-lg backdrop-blur">
                        <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe logo" class="h-16 w-16 object-contain">
                    </div>
                    <h1 class="text-4xl font-semibold tracking-tight">Hotel Don Felipe</h1>
                    <p class="mt-3 text-lg text-white/90">Hotel Management System</p>
                    
                </div>
            </div>

            <div class="flex items-center justify-center bg-[#fcf9f5] p-6 sm:p-8 lg:p-10">
                <div class="w-full max-w-md">
                    <div class="mb-6 flex flex-col items-center text-center">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-[#e5d4bf] bg-white shadow-sm">
                            <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe logo" class="h-14 w-14 object-contain">
                        </div>
                        <h2 class="text-3xl font-semibold text-slate-800">Welcome Back</h2>
                        <p class="mt-2 text-sm text-slate-500">Sign in to continue to your staff dashboard.</p>
                    </div>

                    <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first('username') ?? 'Invalid username or password.' }}
                            </div>
                        @endif

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Username</label>
                            <input
                                type="text"
                                name="username"
                                required
                                autofocus
                                placeholder="Enter username"
                                class="w-full rounded-xl border border-[#dccdb7] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#a97142] focus:ring-2 focus:ring-[#a97142]/20">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Enter password"
                                    class="w-full rounded-xl border border-[#dccdb7] bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-[#a97142] focus:ring-2 focus:ring-[#a97142]/20">
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700">
                                    <i id="passwordIcon" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-[#dccdb7] text-[#a97142] focus:ring-[#a97142]">
                                Remember me
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-[#6d4c41] to-[#a97142] px-4 py-3 font-semibold text-white shadow-lg shadow-[#a97142]/20 transition hover:opacity-95">
                            Sign In
                        </button>

                        <div class="rounded-xl border border-[#f0dfc9] bg-[#fff8ee] px-4 py-3 text-sm text-[#7a5738]">
                            This login uses your <span class="font-semibold">username</span> and <span class="font-semibold">password</span>.
                        </div>
                    </form>

                    <div class="mt-6 border-t border-[#eadfcf] pt-4 text-center text-xs text-slate-500">
                        © {{ date('Y') }} Hotel Don Felipe
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    sessionStorage.removeItem('isFullscreen');
    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const icon = document.getElementById('passwordIcon');

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
});
</script>
</body>
</html>