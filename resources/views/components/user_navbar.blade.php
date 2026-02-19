
@php
    $baseLinkClass = 'w-full flex items-center gap-3 px-4 py-3 rounded-lg transition block';
@endphp

<nav class="fixed lg:relative z-40 top-0 left-0 right-0 bg-white shadow-md border-b border-gray-200 lg:hidden">
    <div class="flex items-center justify-between p-4">
        <h1 class="font-bold text-lg">Car Rental</h1>
        <button id="toggleSidebar" 
            class="p-2 hover:bg-gray-100 rounded-lg transition"
            aria-label="Toggle sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
</nav>

<!-- Mobile Navigation Sidebar -->
<aside id="sidebar"
    class="fixed lg:relative z-50 w-56 h-screen bg-gray-900 shadow-md transition-all duration-300 flex flex-col border-r border-gray-800 -translate-x-full lg:translate-x-0 top-0 left-0 pt-20 lg:pt-0">
    
    <!-- Logo Section (Hidden on mobile, shown on desktop) -->
    <div class="hidden lg:flex p-5 items-center justify-between border-b border-gray-800">
        <h1 class="font-bold text-lg text-white">Car Rental</h1>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-3 space-y-2 overflow-y-auto">
        <a href="{{ route('user.dashboard') }}"
            class="{{ $baseLinkClass }} {{ request()->routeIs('user.dashboard') ? 'bg-black text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <span class="text-lg flex-shrink-0">📊</span>
            <span class="menu-label">Dashboard</span>
        </a>

        <a href="{{ route('user.rentals.index') }}"
            class="{{ $baseLinkClass }} {{ request()->routeIs('user.rentals') ? 'bg-black text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <span class="text-lg flex-shrink-0">🚗</span>
            <span class="menu-label">My Rentals</span>
        </a>

        <a href="{{ route('user.browse') }}"
            class="{{ $baseLinkClass }} {{ request()->routeIs('user.browse') ? 'bg-black text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <span class="text-lg flex-shrink-0">🔍</span>
            <span class="menu-label">Browse Cars</span>
        </a>


        <a href="{{ route('user.browse') }}"
            class="{{ $baseLinkClass }} text-gray-300 hover:bg-gray-800">
            <span class="text-lg flex-shrink-0">💳</span>
            <span class="menu-label">Payments</span>
        </a>

        <a href="{{ route('user.browse') }}"
            class="{{ $baseLinkClass }} text-gray-300 hover:bg-gray-800">
            <span class="text-lg flex-shrink-0">👤</span>
            <span class="menu-label">Profile</span>
        </a>

        <a href="{{ route('user.browse') }}"
            class="{{ $baseLinkClass }} text-gray-300 hover:bg-gray-800">
            <span class="text-lg flex-shrink-0">⚙️</span>
            <span class="menu-label">Settings</span>
        </a>
    </nav>

    <!-- Logout Section -->
    <div class="p-3 border-t border-gray-800">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="{{ $baseLinkClass }} text-gray-300 hover:bg-gray-800">
             <svg class="w-[22px] h-[22px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
</svg>

                <span class="menu-label">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Overlay for mobile -->
<div id="sidebarOverlay"
  class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>


<script src="{{ asset('js/sidebar.js') }}"></script>