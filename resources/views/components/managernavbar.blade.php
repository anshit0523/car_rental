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
                        'route' => route('manager.dashboard'),
                        'pattern' => 'manager.dashboard',
                        'icon' => 'fas fa-chart-line',
                    ],
                ],
            ],
            [
                'label' => 'Rental Management',
                'items' => [
                    [
                        'label' => 'Bookings',
                        'route' => route('manager.bookings.index'),
                        'pattern' => 'manager.bookings*',
                        'icon' => 'fas fa-calendar-check',
                    ],
                    [
                        'label' => 'Vehicle Availability',
                        'route' => route('manager.calendar'),
                        'pattern' => 'manager.calendar*',
                        'icon' => 'fas fa-calendar-alt',
                    ],
                    [
                        'label' => 'Fleet',
                        'route' => route('manager.cars'),
                        'pattern' => 'manager.cars*',
                        'icon' => 'fas fa-car-side',
                    ],
                    [
                        'label' => 'Damage Reports',
                        'route' => route('manager.return-issues.index'),
                        'pattern' => 'manager.return-issues*',
                        'icon' => 'fas fa-triangle-exclamation',
                    ],
                ],
            ],
            [
                'label' => 'Tracking',
                'items' => [
                    [
                        'label' => 'Live Map',
                        'route' => route('manager.live-map'),
                        'pattern' => 'manager.live-map*',
                        'icon' => 'fas fa-map-marked-alt',
                    ],
                    [
                        'label' => 'Replay',
                        'route' => route('manager.replay'),
                        'pattern' => 'manager.replay*',
                        'icon' => 'fas fa-route',
                    ],
                ],
            ],
            [
                'label' => 'Finance',
                'items' => [
                    [
                        'label' => 'Payments',
                        'route' => route('manager.payments.index'),
                        'pattern' => 'manager.payments*',
                        'icon' => 'fas fa-credit-card',
                    ],
                    [
                        'label' => 'Revenue',
                        'route' => route('manager.revenue'),
                        'pattern' => 'manager.revenue*',
                        'icon' => 'fas fa-coins',
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
                <p class="text-xs text-white/70">Manager Control Panel</p>
            </div>
        </div>
    </div>

    <div class="px-3 py-4">
        @foreach($navSections as $section)
            @if(!empty($section['items']))
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
                            <div class="flex items-center gap-3">
                                <i class="{{ $item['icon'] }} w-5 text-center"></i>
                                <span class="font-medium">{{ $item['label'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </nav>
            @endif
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
            <p class="text-[11px] text-white/75">Manager Control Panel</p>
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
                    <p class="text-xs text-white/70">Manager Control Panel</p>
                </div>
            </div>

            <button class="text-2xl" id="menuClose" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="px-3 py-4">
            @foreach($navSections as $section)
                @if(!empty($section['items']))
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
                                <div class="flex items-center gap-3">
                                    <i class="{{ $item['icon'] }} w-5 text-center"></i>
                                    <span class="font-medium">{{ $item['label'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </nav>
                @endif
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