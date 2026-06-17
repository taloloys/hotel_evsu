<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Don Felipe - Operations Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-amber-500/20 blur-3xl"></div>
        <div class="absolute top-24 right-0 h-80 w-80 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
    </div>

    <main class="relative mx-auto flex min-h-screen max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <aside class="w-full max-w-md rounded-3xl border border-white/10 bg-white p-6 shadow-2xl shadow-black/30 sm:p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-950">Sign in</h2>
                    <p class="mt-2 text-sm text-slate-500">Use your staff credentials to continue into the dashboard.</p>
                </div>

                <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('username') ?? 'Please check your login details and try again.' }}
                        </div>
                    @endif

                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium text-slate-700">Username</label>
                        <input type="text" name="username" id="username" required autofocus class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Enter your username">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <input type="password" name="password" id="password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Enter your password">
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                        <label for="remember" class="text-sm text-slate-600">Remember me</label>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-3 font-semibold text-white transition hover:bg-slate-800">
                        Sign In
                    </button>

                    <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        The login route expects a <span class="font-semibold">username</span> and <span class="font-semibold">password</span>, not email.
                    </div>
                </form>

            </aside>
        </div>
    </main>
</body>
</html>