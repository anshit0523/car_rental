<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin / Staff Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
        }

        .brand-orange {
            color: #ff5a1f;
        }

        .bg-brand-orange {
            background-color: #ff5a1f;
        }

        .border-brand-orange {
            border-color: #ff5a1f;
        }

        .hero-overlay {
            background:
                linear-gradient(to right, rgba(0, 0, 0, .78), rgba(0, 0, 0, .50)),
                url("{{ asset('storage/cars/toyota-bg.png') }}") center/cover no-repeat;
        }

        .glass {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="min-h-screen hero-overlay text-white">

    <header class="absolute top-0 left-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between py-4">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo" class="h-16 md:h-20 w-auto object-contain">
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="rounded-full border border-white/40 px-5 py-2 text-sm md:text-base text-white hover:bg-white hover:text-slate-900 transition">
                        Customer Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="rounded-full bg-brand-orange px-5 py-2 text-sm md:text-base text-white hover:opacity-90 transition">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center px-4 py-24">
        <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center pt-19">
            <div class="hidden lg:block">
                <p class="uppercase tracking-[0.18em] text-orange-400 font-bold text-sm mb-4">Back Office Access</p>
                <h1 class="text-5xl xl:text-6xl font-extrabold leading-tight mb-5">
                    Secure Login for
                    <span class="text-orange-400">Admin & Staff</span>
                </h1>
                <p class="text-white/80 text-lg max-w-xl mb-8 leading-relaxed">
                    Access the rental management dashboard, booking approvals, payment verification,
                    vehicle monitoring, and daily operations in one place.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('landing') }}"
                        class="rounded-full border border-white/40 px-7 py-3 text-white font-semibold hover:bg-white hover:text-slate-900 transition">
                        Back to Home
                    </a>
                    <a href="{{ route('login') }}"
                        class="rounded-full bg-brand-orange px-7 py-3 text-white font-semibold hover:opacity-90 transition">
                        Customer Portal
                    </a>
                </div>
            </div>

            <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl w-full max-w-md mx-auto lg:mx-0 lg:ml-auto">
                <div class="mb-8 text-center lg:text-left">
                    <p class="uppercase tracking-[0.18em] text-orange-400 font-bold text-xs mb-3">Admin / Staff Login</p>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Welcome Back</h2>
                    <p class="text-white/75 text-sm md:text-base">Sign in to access the back office dashboard.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-300/40 bg-red-500/20 px-4 py-3 text-sm text-red-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-emerald-300/40 bg-emerald-500/20 px-4 py-3 text-sm text-emerald-100">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-white/85 text-sm mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none border border-transparent focus:border-orange-400"
                            placeholder="Enter your email"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-white/85 text-sm mb-2">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none border border-transparent focus:border-orange-400"
                            placeholder="Enter your password"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-brand-orange px-5 py-3 text-center text-white font-semibold hover:opacity-90 transition"
                    >
                        Login to Back Office
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-white/75">
                    Need the customer portal?
                    <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300 font-semibold">
                        Customer Login
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>