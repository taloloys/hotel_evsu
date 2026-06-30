<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Don Felipe</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body{
            background:#f3f4f6;
        }

        .login-bg{
            background-image: url("{{ asset('images/larrazabal.png') }}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body>

<div class="min-h-screen grid lg:grid-cols-2">

    <!-- LEFT SIDE -->
    <div
        class="hidden lg:flex relative bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/picture.jpg') }}');">

        <div class="relative z-10 flex flex-col justify-end p-12 text-white">

            <h1 class="text-4xl font-bold drop-shadow-lg">
                Hotel Don Felipe
            </h1>

            <p class="mt-3 max-w-md text-white drop-shadow-md">
                Hotel Management System
            </p>

            <p class="mt-2 text-sm text-white/95 drop-shadow-md">
                Manage reservations, guests, billing and daily hotel operations.
            </p>

        </div>

    </div>

    <!-- RIGHT SIDE -->
    <div class="flex items-center justify-center p-6">

        <div class="w-full max-w-md bg-white rounded-lg shadow-lg border border-black-200">

            <!-- Header -->
            <div class="px-7 pt-8 pb-2 border-b">

                <h2 class="text-2xl font-bold text-gray-800">
                    Sign In
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Enter your staff account to continue.
                </p>

            </div>

            <!-- Form -->
            <form method="POST"
                  action="{{ url('/login') }}"
                  class="p-4 space-y-5">

                @csrf

                @if ($errors->any())
                    <div class="border border-red-200 bg-red-50 text-red-700 rounded-md px-4 py-3 text-sm">
                        {{ $errors->first('username') ?? 'Invalid username or password.' }}
                    </div>
                @endif

                <!-- Username -->

                <div>

                    <label
                        class="block text-sm font-medium text-gray-700 mb-2" required>

                        Username

                    </label>

                    <input
                        type="text"
                        name="username"
                        required
                        autofocus
                        placeholder="Enter username"
                        class="w-full border border-gray-300 rounded-md px-4 py-3 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">

                </div>

                <!-- Password -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2" required>
                        Password
                    </label>

                    <div class="relative">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Enter password"
                            class="w-full border border-gray-300 rounded-md px-4 py-3 pr-12 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">

                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-gray-700">

                            <i id="passwordIcon" class="fa-solid fa-eye"></i>

                        </button>

                    </div>

                </div>

                <!-- Remember -->

                <div class="flex items-center justify-between">

                    <label class="flex items-center gap-2 text-sm text-gray-600">

                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-300">

                        Remember me

                    </label>

                </div>

                <!-- Button -->

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-md transition">

                    Sign In

                </button>

                <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    The login route expects a <span class="font-semibold">username</span> and <span class="font-semibold">password</span>, not email.
                </div>


            </form>
            <!-- Footer -->

            <div class="border-t bg-gray-50 px-8 py-4 text-center text-xs text-gray-500">

                © {{ date('Y') }} Hotel Don Felipe

            </div>

        </div>

    </div>

</div>

</body>
<script>
document.addEventListener('DOMContentLoaded', function () {

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
</html>