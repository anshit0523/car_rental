<header style="background:#ffffff; border-bottom:1px solid #ececec; box-shadow:0 4px 18px rgba(0,0,0,0.04); position:sticky; top:0; z-index:999;">
    <div class="container">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; min-height:84px;">

            <!-- Logo -->
            <div style="flex-shrink:0;">
                <a href="{{ route('user.browse') }}"
                   style="font-size:32px; font-weight:700; color:#111; font-family:'Outfit', sans-serif; text-decoration:none;">
                    Car Rental
                </a>
            </div>

            <!-- Center Nav -->
            <nav style="flex:1; display:flex; justify-content:center;">
                <ul style="display:flex; align-items:center; gap:34px; margin:0; padding:0; list-style:none;">
                    <li>
                        <a href="{{ route('user.browse') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.browse') || request()->routeIs('user.search') ? 'color:#ff2c3b; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Browse Cars
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.rentals.index') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.rentals.*') ? 'color:#ff2c3b; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            My Rentals
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.payments') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.payments') ? 'color:#ff2c3b; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Payments
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.profile') }}"
                           style="text-decoration:none; font-size:17px; {{ request()->routeIs('user.profile') ? 'color:#ff2c3b; font-weight:700;' : 'color:#222; font-weight:500;' }}">
                            Profile
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Right User Area -->
            <div style="display:flex; align-items:center; gap:14px; flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:44px; height:44px; border-radius:50%; background:#ff2c3b; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px;">
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