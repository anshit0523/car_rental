<div class="fixed lg:relative top-0 left-0 z-50 h-screen w-72 bg-[#ff5a1f] text-white overflow-y-auto lg:block hidden shadow-2xl">
    @php
        $baseLinkClass = 'flex items-center gap-3 px-4 py-3 rounded-xl text-white/85 transition-all duration-200 block';
        $sectionLabelClass = 'px-4 pt-5 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/50';

        $navSections = [
            [
                'label' => 'Overview',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('admin.dashboard'),
                        'pattern' => 'admin.dashboard',
                        'icon' => 'fas fa-chart-line',
                    ],
                ],
            ],
            [
                'label' => 'Rental Management',
                'items' => [
                    [
                        'label' => 'Bookings',
                        'route' => route('admin.bookings.index'),
                        'pattern' => 'admin.bookings*',
                        'icon' => 'fas fa-calendar-check',
                    ],
                    [
                        'label' => 'Fleet',
                        'route' => route('admin.cars'),
                        'pattern' => 'admin.cars*',
                        'icon' => 'fas fa-car',
                    ],
                    [
                        'label' => 'Vehicle Availability',
                        'route' => route('admin.calendar'),
                        'pattern' => 'admin.calendar*',
                        'icon' => 'fas fa-calendar-alt',
                    ],
                    [
                        'label' => 'Users',
                        'route' => route('admin.users.index'),
                        'pattern' => 'admin.users.*',
                        'icon' => 'fas fa-users',
                    ],
                    [
                        'label' => 'Damage Reports',
                        'route' => route('admin.return-issues.index'),
                        'pattern' => 'admin.return-issues*',
                        'icon' => 'fas fa-exclamation-triangle',
                    ],
                ],
            ],
            [
                'label' => 'Tracking & Monitoring',
                'items' => [
                    [
                        'label' => 'Live Map',
                        'route' => route('admin.live-map'),
                        'pattern' => 'admin.live-map',
                        'icon' => 'fas fa-map-marked-alt',
                    ],
                    [
                        'label' => 'Trackers',
                        'route' => route('admin.trackers.create'),
                        'pattern' => 'admin.trackers.*',
                        'icon' => 'fas fa-map-marker-alt',
                    ],
                ],
            ],
            [
                'label' => 'Finance & Settings',
                'items' => [
                    [
                        'label' => 'Revenue',
                        'route' => route('admin.revenue'),
                        'pattern' => 'admin.revenue*',
                        'icon' => 'fas fa-chart-bar',
                    ],
                    [
                        'label' => 'Payments',
                        'route' => route('admin.payments.index'),
                        'pattern' => 'admin.payments*',
                        'icon' => 'fas fa-credit-card',
                        'badge' => $pendingPaymentsCount ?? 0,
                    ],
                    [
                        'label' => 'Payment Settings',
                        'route' => route('admin.payment-settings.edit'),
                        'pattern' => 'admin.payment-settings*',
                        'icon' => 'fas fa-cog',
                    ],
                ],
            ],
        ];
    @endphp

    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-white/15 flex items-center justify-center shadow-md">
                <i class="fas fa-car-side text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Eze Car Rental</h1>
                <p class="text-xs text-white/70">Admin Control Panel</p>
            </div>
        </div>
    </div>

    <div class="px-3 py-4">
        @foreach($navSections as $section)
            <div class="{{ $sectionLabelClass }}">
                {{ $section['label'] }}
            </div>

            <nav class="space-y-1">
                @foreach($section['items'] as $item)
                    @php
                        $isActive = request()->routeIs($item['pattern']);
                    @endphp

                    <a href="{{ $item['route'] }}"
                       class="{{ $baseLinkClass }} {{ $isActive ? 'bg-white/20 text-white shadow-sm' : 'hover:bg-white/10 hover:text-white' }}">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-3">
                                <i class="{{ $item['icon'] }} w-5 text-center"></i>
                                <span class="font-medium">{{ $item['label'] }}</span>
                            </div>

                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="ml-3 inline-flex min-w-[20px] h-5 px-1.5 items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none">
                                    {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </nav>
        @endforeach

        <div class="px-4 pt-5 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/50">
            Account
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="{{ $baseLinkClass }} hover:bg-red-600/80 w-full justify-start">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Top Bar -->
<div class="lg:hidden bg-[#ff5a1f] text-white px-4 py-4 flex items-center justify-between shadow-md">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-white/15 flex items-center justify-center">
            <i class="fas fa-car-side"></i>
        </div>
        <div>
            <h1 class="text-base font-bold leading-tight">Eze Car Rental</h1>
            <p class="text-[11px] text-white/75">Admin Control Panel</p>
        </div>
    </div>

    <button class="text-2xl" id="menuToggle" type="button">
        <i class="fas fa-bars"></i>
    </button>
</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="hidden fixed inset-0 bg-black/50 z-50 lg:hidden">
    <div class="w-80 max-w-[85%] h-full bg-[#ff5a1f] text-white shadow-2xl overflow-y-auto">
        <div class="p-6 border-b border-white/10 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl bg-white/15 flex items-center justify-center">
                    <i class="fas fa-car-side text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Eze Car Rental</h1>
                    <p class="text-xs text-white/70">Admin Control Panel</p>
                </div>
            </div>

            <button class="text-2xl" id="menuClose" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="px-3 py-4">
            @foreach($navSections as $section)
                <div class="{{ $sectionLabelClass }}">
                    {{ $section['label'] }}
                </div>

                <nav class="space-y-1">
                    @foreach($section['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['pattern']);
                        @endphp

                        <a href="{{ $item['route'] }}"
                           class="{{ $baseLinkClass }} {{ $isActive ? 'bg-white/20 text-white shadow-sm' : 'hover:bg-white/10 hover:text-white' }}">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-3">
                                    <i class="{{ $item['icon'] }} w-5 text-center"></i>
                                    <span class="font-medium">{{ $item['label'] }}</span>
                                </div>

                                @if(isset($item['badge']) && $item['badge'] > 0)
                                    <span class="ml-3 inline-flex min-w-[20px] h-5 px-1.5 items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none">
                                        {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </nav>
            @endforeach

            <div class="px-4 pt-5 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/50">
                Account
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="{{ $baseLinkClass }} hover:bg-red-600/80 w-full justify-start">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>