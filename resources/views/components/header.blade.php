<header id="siteHeader" class="fixed top-0 left-0 w-full z-50 transition-all duration-300">
    <div id="navbarContainer" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-all duration-300">
        <div id="navbarShell"
            class="mt-4 flex items-center justify-between rounded-2xl border border-white/10 bg-white/10 px-4 py-3 shadow-lg backdrop-blur-md md:px-6 transition-all duration-300">

            <a href="{{ route('landing') }}" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo"
                    class="h-12 md:h-14 w-auto object-contain">
                <div class="hidden sm:block leading-tight">
                    <p class="text-white font-extrabold text-sm md:text-base tracking-wide">Dumaguete EZE</p>
                    <p class="text-white/70 text-xs md:text-sm">Car Rental Services</p>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-7">
                <a href="{{ route('landing') }}"
                    class="nav-link {{ request()->routeIs('landing') ? 'active text-white' : 'text-white/90 hover:text-white' }} text-sm font-medium transition">
                    Home
                </a>

                <a href="{{ route('user.browse') }}"
                    class="nav-link {{ request()->routeIs('user.browse') ? 'active text-white' : 'text-white/90 hover:text-white' }} text-sm font-medium transition">
                    Browse Cars
                </a>

                <a href="{{ route('about') }}"
                    class="nav-link {{ request()->routeIs('about') ? 'active text-white' : 'text-white/90 hover:text-white' }} text-sm font-medium transition">
                    About Us
                </a>

                <a href="{{ route('contact') }}"
                    class="nav-link {{ request()->routeIs('contact') ? 'active text-white' : 'text-white/90 hover:text-white' }} text-sm font-medium transition">
                    Contact
                </a>
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="rounded-full border border-orange-300/70 bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white hover:text-slate-900 transition">
                    Login
                </a>

                <a href="{{ route('register') }}"
                    class="rounded-full bg-brand-orange px-5 py-2 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">
                    Register
                </a>
            </div>

            <button id="mobileMenuBtn"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white hover:text-slate-900 transition">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>

        <div id="mobileMenu"
            class="lg:hidden hidden mt-3 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/90 shadow-xl backdrop-blur-md">
            <div class="px-4 py-4 flex flex-col gap-2">
                <a href="{{ route('landing') }}"
                    class="rounded-xl px-4 py-3 {{ request()->routeIs('landing') ? 'text-white bg-white/10' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition">
                    Home
                </a>

                <a href="{{ route('user.browse') }}"
                    class="rounded-xl px-4 py-3 {{ request()->routeIs('user.browse') ? 'text-white bg-white/10' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition">
                    Browse Cars
                </a>

                <a href="{{ route('about') }}"
                    class="rounded-xl px-4 py-3 {{ request()->routeIs('about') ? 'text-white bg-white/10' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition">
                    About Us
                </a>

                <a href="{{ route('contact') }}"
                    class="rounded-xl px-4 py-3 {{ request()->routeIs('contact') ? 'text-white bg-white/10' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition">
                    Contact
                </a>

                <div class="my-2 h-px bg-white/10"></div>

                <a href="{{ route('admin.login') }}"
                    class="rounded-xl border border-white/10 px-4 py-3 text-white text-center hover:bg-white hover:text-slate-900 transition">
                    Admin / Staff Login
                </a>

                <a href="{{ route('login') }}"
                    class="rounded-xl border border-orange-300/40 px-4 py-3 text-white text-center hover:bg-white hover:text-slate-900 transition">
                    Customer Login
                </a>

                <a href="{{ route('register') }}"
                    class="rounded-xl bg-brand-orange px-4 py-3 text-white text-center font-semibold hover:opacity-90 transition">
                    Register
                </a>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const siteHeader = document.getElementById('siteHeader');

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenu?.classList.toggle('hidden');
    });

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            siteHeader?.classList.add('scrolled');
        } else {
            siteHeader?.classList.remove('scrolled');
        }
    });
});
</script>