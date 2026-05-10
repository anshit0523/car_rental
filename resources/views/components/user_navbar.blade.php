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

<style>
    .user-header {
        background: #ffffff;
        border-bottom: 1px solid #ececec;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .user-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        min-height: 84px;
        flex-wrap: wrap;
    }

    .user-logo-wrapper {
        flex-shrink: 0;
    }

    .user-logo-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .user-logo-img {
        height: 60px;
        width: auto;
        display: block;
    }

    .user-logo-text {
        font-size: 30px;
        font-weight: 700;
        color: #111;
        font-family: 'Outfit', sans-serif;
        white-space: nowrap;
    }

    .user-center-nav {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .user-center-nav ul {
        display: flex;
        align-items: center;
        gap: 34px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .user-center-nav a {
        text-decoration: none;
        font-size: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .nav-icon {
        display: none;
    }

    .mobile-profile-nav-item {
        display: none;
    }

    .user-right-area {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-shrink: 0;
    }

    .notification-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: #f8f8f8;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        color: #444;
    }

    .notification-dropdown,
    .user-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 16px;
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.12);
        z-index: 9999;
        overflow: hidden;
    }

    .notification-dropdown {
        top: 54px;
        width: 340px;
    }

    .user-dropdown-wrapper {
        position: relative;
    }

    .user-dropdown-btn {
        border: none;
        background: #f8f8f8;
        border-radius: 14px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }

    .user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ff5a1f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .user-dropdown-info {
        text-align: left;
    }

    .user-dropdown-name {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        line-height: 1.2;
        white-space: nowrap;
    }

    .user-dropdown-role {
        font-size: 13px;
        color: #777;
        line-height: 1.2;
        white-space: nowrap;
    }

    .user-dropdown-menu {
        top: 58px;
        width: 230px;
    }

    .dropdown-profile-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        text-decoration: none;
        color: #222;
        font-size: 14px;
        font-weight: 600;
        border-bottom: 1px solid #f5f5f5;
    }

    .dropdown-logout-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        background: #fff;
        border: none;
        color: #ef4444;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-align: left;
    }

    @media (max-width: 768px) {
        body {
            padding-bottom: 86px;
        }

        .user-header {
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-header-inner {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 10px;
            min-height: 76px;
            padding: 10px 0;
        }

        .user-logo-wrapper {
            width: auto;
            display: flex;
            justify-content: flex-start;
            min-width: 0;
        }

        .user-logo-link {
            justify-content: flex-start;
            gap: 8px;
            min-width: 0;
        }

        .user-logo-img {
            height: 42px;
            flex-shrink: 0;
        }

        .user-logo-text {
            font-size: 22px;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-right-area {
            width: auto;
            justify-content: flex-end;
            gap: 8px;
            flex-shrink: 0;
        }

        .notification-btn,
        .user-dropdown-btn {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: #f8f8f8;
            padding: 0;
            justify-content: center;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }

        .user-dropdown-info,
        .user-chevron {
            display: none;
        }

        .mobile-profile-nav-item {
            display: block;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile Bottom Navigation
        |--------------------------------------------------------------------------
        */

        .user-center-nav {
            position: fixed;
            left: 14px;
            right: 14px;
            bottom: 14px;
            z-index: 998;
            width: auto;
            height: 68px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(229, 231, 235, 0.95);
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .user-center-nav ul {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
        }

        .user-center-nav li {
            list-style: none;
        }

        .user-center-nav a {
            width: 100%;
            height: 52px;
            border-radius: 18px;
            background: transparent;
            color: #64748b !important;
            font-size: 11px;
            font-weight: 700;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .user-center-nav a.mobile-active {
            background: #fff3ed;
            color: #ff5a1f !important;
        }

        .nav-text {
            display: block;
            font-size: 10px;
            line-height: 1;
            max-width: 64px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icon svg {
            width: 22px;
            height: 22px;
        }

        .notification-dropdown,
        .user-dropdown-menu {
            position: fixed;
            left: 16px;
            right: 16px;
            top: 88px;
            width: auto;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }
    }

    @media (max-width: 420px) {
        .user-logo-text {
            font-size: 20px;
        }

        .user-logo-img {
            height: 40px;
        }

        .notification-btn,
        .user-dropdown-btn {
            width: 44px;
            height: 44px;
            border-radius: 15px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            font-size: 15px;
        }

        .user-center-nav {
            left: 10px;
            right: 10px;
            bottom: 10px;
            height: 66px;
            border-radius: 22px;
        }

        .user-center-nav a {
            height: 50px;
            border-radius: 16px;
        }

        .nav-icon svg {
            width: 21px;
            height: 21px;
        }

        .nav-text {
            font-size: 9px;
        }
    }

    @media (max-width: 380px) {
        .user-logo-text {
            font-size: 18px;
        }

        .user-logo-img {
            height: 38px;
        }

        .notification-btn,
        .user-dropdown-btn {
            width: 42px;
            height: 42px;
            border-radius: 14px;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }

        .user-center-nav {
            left: 8px;
            right: 8px;
            bottom: 8px;
            height: 64px;
            padding: 7px;
        }

        .user-center-nav a {
            height: 48px;
            border-radius: 15px;
        }

        .nav-icon svg {
            width: 20px;
            height: 20px;
        }

        .nav-text {
            font-size: 8.5px;
            max-width: 58px;
        }
    }
</style>

<header class="user-header">
    <div class="container">
        <div class="user-header-inner">

            <!-- Logo -->
            <div class="user-logo-wrapper">
                <a href="{{ route('user.browse') }}" class="user-logo-link">
                    <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo" class="user-logo-img">

                    <span class="user-logo-text">
                        Eze Car Rental
                    </span>
                </a>
            </div>

            <!-- Center Nav -->
            <nav class="user-center-nav">
                <ul>
                    <li>
                        <a href="{{ route('user.browse') }}"
                           title="Browse Cars"
                           aria-label="Browse Cars"
                           class="{{ request()->routeIs('user.browse') || request()->routeIs('user.search') ? 'mobile-active' : '' }}"
                           style="{{ request()->routeIs('user.browse') || request()->routeIs('user.search') ? 'color:#ff5a1f; font-weight:700;' : 'color:#222; font-weight:500;' }}">

                            <span class="nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 13l2-5a3 3 0 012.8-2h8.4A3 3 0 0119 8l2 5M5 13h14M6 17h.01M18 17h.01M7 13l1-3h8l1 3M5 13v5m14-5v5" />
                                </svg>
                            </span>

                            <span class="nav-text">Browse</span>
                        </a>
                    </li>

                    @auth
                        <li>
                            <a href="{{ route('user.rentals.index') }}"
                               title="My Rentals"
                               aria-label="My Rentals"
                               class="{{ request()->routeIs('user.rentals.*') ? 'mobile-active' : '' }}"
                               style="{{ request()->routeIs('user.rentals.*') ? 'color:#ff5a1f; font-weight:700;' : 'color:#222; font-weight:500;' }}">

                                <span class="nav-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7h3a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h3m6 0V5a3 3 0 00-6 0v2m6 0H9m3 5v4" />
                                    </svg>
                                </span>

                                <span class="nav-text">Rentals</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('user.pending-payments') }}"
                               title="Payments"
                               aria-label="Payments"
                               class="{{ request()->routeIs('user.pending-payments') || request()->routeIs('user.payments') ? 'mobile-active' : '' }}"
                               style="{{ request()->routeIs('user.pending-payments') || request()->routeIs('user.payments') ? 'color:#ff5a1f; font-weight:700;' : 'color:#222; font-weight:500;' }}">

                                <span class="nav-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zM7 15h4" />
                                    </svg>
                                </span>

                                <span class="nav-text">Payments</span>
                            </a>
                        </li>

                        <li class="mobile-profile-nav-item">
                            <a href="{{ route('user.profile') }}"
                               title="Profile"
                               aria-label="Profile"
                               class="{{ request()->routeIs('user.profile') ? 'mobile-active' : '' }}"
                               style="{{ request()->routeIs('user.profile') ? 'color:#ff5a1f; font-weight:700;' : 'color:#222; font-weight:500;' }}">

                                <span class="nav-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5.121 17.804A9.003 9.003 0 0112 15a9.003 9.003 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>

                                <span class="nav-text">Profile</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>

            <!-- Right User Area -->
            <div class="user-right-area">

                @auth
                    <!-- Notification -->
                    <div style="position:relative;">
                        <button id="notificationBell"
                                type="button"
                                onclick="toggleNotifications(event)"
                                class="notification-btn"
                                title="Notifications"
                                aria-label="Notifications">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                         6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165
                                         6 8.388 6 11v3.159c0 .538-.214 1.055-.595
                                         1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>

                            @if($unreadCount > 0)
                                <span id="notificationBadge"
                                      style="position:absolute; top:8px; right:8px; min-width:10px; height:10px; background:#ef4444; border-radius:9999px;"></span>
                            @endif
                        </button>

                        <div id="notificationDropdown" class="notification-dropdown">
                            <div style="padding:16px 18px; border-bottom:1px solid #f1f1f1;">
                                <div style="font-size:15px; font-weight:700; color:#111;">
                                    Notifications
                                </div>
                            </div>

                            <div class="notification-list" style="max-height:320px; overflow-y:auto;">
                                @forelse($notifications as $notification)
                                    <a href="{{ route('user.notifications.read', $notification->id) }}"
                                       style="display:block; padding:14px 18px; border-bottom:1px solid #f5f5f5; text-decoration:none; background:{{ $notification->is_read ? '#fff' : '#f8faff' }};">
                                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:6px;">
                                            <p style="margin:0; font-size:14px; font-weight:600; color:#111; line-height:1.4;">
                                                {{ $notification->title }}
                                            </p>

                                            @if(!$notification->is_read)
                                                <span style="flex-shrink:0; font-size:10px; font-weight:700; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:9999px; padding:4px 7px; line-height:1;">
                                                    New
                                                </span>
                                            @endif
                                        </div>

                                        <p style="margin:0 0 6px; font-size:12px; color:#666; line-height:1.5;">
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

                            @if($notifications->count())
                                <div style="padding:12px 18px; border-top:1px solid #f1f1f1; background:#fafafa;">
                                    <a href="{{ route('user.notifications.index') }}"
                                       style="display:block; text-align:center; font-size:13px; font-weight:700; color:#4f46e5; text-decoration:none;">
                                       View All Notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="user-dropdown-wrapper">
                        <button id="userDropdownButton"
                                type="button"
                                onclick="toggleUserDropdown(event)"
                                class="user-dropdown-btn"
                                title="Account"
                                aria-label="Account">

                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="user-dropdown-info">
                                <div class="user-dropdown-name">
                                    {{ auth()->user()->name ?? 'User' }}
                                </div>
                                <div class="user-dropdown-role">
                                    Customer
                                </div>
                            </div>

                            <svg class="user-chevron" width="18" height="18" fill="none" stroke="#555" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="userDropdown" class="user-dropdown-menu">
                            <div style="padding:16px 18px; border-bottom:1px solid #f1f1f1;">
                                <div style="font-size:14px; font-weight:700; color:#111;">
                                    {{ auth()->user()->name ?? 'User' }}
                                </div>
                            </div>

                            <a href="{{ route('user.profile') }}"
                               class="dropdown-profile-link"
                               style="background:{{ request()->routeIs('user.profile') ? '#fff3ed' : '#fff' }};">
                                <span><i class="fas fa-user"></i></span>
                                <span>Profile</span>
                            </a>

                            <form action="{{ route('logout') }}?clear=1" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="dropdown-logout-btn">
                                    <span><i class="fas fa-sign-out-alt"></i></span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       style="background:#fff; color:#111; border:1px solid #ececec; border-radius:10px; padding:12px 22px; font-size:15px; font-weight:600; line-height:1; text-decoration:none;">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                       style="background:#ff5a1f; color:#fff; border:none; border-radius:10px; padding:12px 22px; font-size:15px; font-weight:600; line-height:1; text-decoration:none;">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

@section('scripts')
    @auth
        <script src="{{ asset('js/user/usernavbar.js') }}"></script>
    @endauth
@endsection