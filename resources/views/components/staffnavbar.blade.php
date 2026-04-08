    <div class="fixed lg:relative top-0 left-0 z-50 h-screen w-64 bg-[#ff5a1f] text-white p-6 overflow-y-auto lg:block hidden">
        <div class="mb-8 flex items-center gap-2 text-xl font-bold">
            <i class="fas fa-car"></i>
            <span>Eze Car Rental</span>
        </div>

        @php
            $baseLinkClass = 'flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 transition block';
        @endphp

        <nav class="space-y-2">
            <a href="{{ route('staff.dashboard') }}"
            class="{{ $baseLinkClass }} {{ request()->routeIs('staff.dashboard') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                <i class="fas fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>

            @if(Route::has('staff.bookings.index'))
                <a href="{{ route('staff.bookings.index') }}"
                class="{{ $baseLinkClass }} {{ request()->routeIs('staff.bookings*') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                    <i class="fas fa-calendar-check w-5"></i>
                    <span>Bookings</span>
                </a>
            @endif

            @if(Route::has('staff.calendar'))
                <a href="{{ route('staff.calendar') }}"
                class="{{ $baseLinkClass }} {{ request()->routeIs('staff.calendar') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                    <i class="fas fa-calendar w-5"></i>
                    <span>Vehicle Availability</span>
                </a>
            @endif

            @if(Route::has('staff.payments.index'))
                <a href="{{ route('staff.payments.index') }}"
                class="{{ $baseLinkClass }} {{ request()->routeIs('staff.payments*') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                    <i class="fas fa-credit-card w-5"></i>
                    <span>Payments</span>
                </a>
            @endif

            @if(Route::has('staff.live-map'))
                <a href="{{ route('staff.live-map') }}"
                class="{{ $baseLinkClass }} {{ request()->routeIs('staff.live-map') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                    <i class="fas fa-map-marked-alt w-5"></i>
                    <span>Live Map</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="{{ $baseLinkClass }} hover:bg-red-600/80 w-full justify-start">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </div>

    <!-- Mobile Topbar -->
    <div class="lg:hidden bg-gray-900 text-white p-4 flex items-center justify-between">
        <h1 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-car"></i>
            <span>Car Rental</span>
        </h1>
        <button class="text-2xl" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden fixed top-0 left-0 w-full h-screen bg-gray-900 text-white z-40 p-6 lg:hidden">
        <button class="absolute top-4 right-4 text-2xl" id="menuClose">
            <i class="fas fa-times"></i>
        </button>

        <div class="mt-8">
            @php
                $baseLinkClass = 'flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 transition block';
            @endphp

            <nav class="space-y-2">
                <a href="{{ route('staff.dashboard') }}"
                class="{{ $baseLinkClass }} {{ request()->routeIs('staff.dashboard') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>

                @if(Route::has('staff.bookings.index'))
                    <a href="{{ route('staff.bookings.index') }}"
                    class="{{ $baseLinkClass }} {{ request()->routeIs('staff.bookings*') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>Bookings</span>
                    </a>
                @endif

                @if(Route::has('staff.calendar'))
                    <a href="{{ route('staff.calendar') }}"
                    class="{{ $baseLinkClass }} {{ request()->routeIs('staff.calendar') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                        <i class="fas fa-calendar w-5"></i>
                        <span>Vehicle Availability</span>
                    </a>
                @endif

                @if(Route::has('staff.payments.index'))
                    <a href="{{ route('staff.payments.index') }}"
                    class="{{ $baseLinkClass }} {{ request()->routeIs('staff.payments*') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Payments</span>
                    </a>
                @endif

                @if(Route::has('staff.live-map'))
                    <a href="{{ route('staff.live-map') }}"
                    class="{{ $baseLinkClass }} {{ request()->routeIs('staff.live-map') ? 'bg-white/20 text-white' : 'hover:bg-white/10' }}">
                        <i class="fas fa-map-marked-alt w-5"></i>
                        <span>Live Map</span>
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="{{ $baseLinkClass }} hover:bg-red-600/80 w-full justify-start">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </nav>
        </div>
    </div>