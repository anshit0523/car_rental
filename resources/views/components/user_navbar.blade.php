@php
    $notifications = auth()->check()
        ? \App\Models\Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get()
        : collect();

    $unreadCount = auth()->check()
        ? \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count()
        : 0;
@endphp

<header style="background:#ffffff; border-bottom:1px solid #ececec; box-shadow:0 4px 18px rgba(0,0,0,0.04); position:sticky; top:0; z-index:999;">
    <div class="container">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; min-height:84px; flex-wrap:wrap;">
            <!-- Logo -->
            <div style="flex-shrink:0;">
                <a href="{{ route('user.browse') }}"
                   style="font-size:32px; font-weight:700; color:#111; font-family:'Outfit', sans-serif; text-decoration:none;">
                    EZE Car Rental
                </a>
            </div>

            <!-- Center Nav -->
            <nav style="flex:1; display:flex; justify-content:center;">
                <ul style="display:flex; align-items:center; gap:34px; margin:0; padding:0; list-style:none;">
                    <li>
                        <a href="{{ route('user.browse') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.browse') || request()->routeIs('user.search') ? 'color:#ff9913; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Browse Cars
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.rentals.index') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.rentals.*') ? 'color:#ff9913; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            My Rentals
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.payments') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.payments') ? 'color:#ff9913; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Payments
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.profile') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.profile') ? 'color:#ff9913; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Profile
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Right User Area -->
            <div style="display:flex; align-items:center; gap:18px; flex-shrink:0;">

                <!-- Notification -->
                <div class="relative" style="position:relative;">
                    <button id="notificationBell"
                            type="button"
                            onclick="toggleNotifications(event)"
                            style="width:44px; height:44px; border:none; background:#f8f8f8; border-radius:12px; display:flex; align-items:center; justify-content:center; cursor:pointer; position:relative; color:#444;">
                        <svg class="w-6 h-6" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165
                                     6 8.388 6 11v3.159c0 .538-.214 1.055-.595
                                     1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>

                        @if($unreadCount > 0)
                            <span id="notificationBadge"
                                  style="position:absolute; top:8px; right:8px; width:10px; height:10px; background:#ef4444; border-radius:9999px;"></span>
                        @endif
                    </button>

                    <div id="notificationDropdown"
                         class="hidden"
                         style="display:none; position:absolute; right:0; top:54px; width:320px; background:#fff; border:1px solid #ececec; border-radius:16px; box-shadow:0 14px 32px rgba(0,0,0,0.10); z-index:9999; overflow:hidden;">

                        <div style="padding:16px 18px; border-bottom:1px solid #f1f1f1; font-size:15px; font-weight:700; color:#111;">
                            Notifications
                        </div>

                        <div class="notification-list" style="max-height:320px; overflow-y:auto;">
                            @forelse($notifications as $notification)
                                <a href="{{ route('user.notifications.read', $notification->id) }}"
                                   style="display:block; padding:14px 18px; border-bottom:1px solid #f5f5f5; text-decoration:none;">
                                    <p style="margin:0 0 6px; font-size:14px; font-weight:600; color:#111;">
                                        {{ $notification->title }}
                                    </p>

                                    <p style="margin:0 0 6px; font-size:12px; color:#666;">
                                        {{ $notification->message }}
                                    </p>

                                    <p style="margin:0; font-size:11px; color:#aaa;">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </a>
                            @empty
                                <div style="padding:16px 18px; font-size:13px; color:#777;">
                                    No notifications
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; border-radius:50%; background:#ff9913; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div>
                        <div style="font-size:15px; font-weight:600; color:#111; line-height:1.2;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size:13px; color:#777; line-height:1.2;">
                            Customer
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit"
                            style="background:#ff2c3b; color:#fff; border:none; border-radius:10px; padding:12px 22px; font-size:15px; font-weight:600; line-height:1; cursor:pointer;">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</header>

@section('scripts')
    <script src="{{ asset('js/user/usernavbar.js') }}"></script>
@endsection

