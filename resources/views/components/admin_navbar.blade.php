<div class="fixed lg:relative top-0 left-0 z-50 h-screen w-64 bg-gray-900 text-white p-6 overflow-y-auto lg:block hidden">
            <div class="mb-8 flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-car"></i>
                <span>Car Rental</span>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white/20">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-calendar-check w-5"></i>
                    <span>Bookings</span>
                </a>
                <a href="{{ route('admin.cars') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-car w-5"></i>
                    <span>Fleet</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Revenue</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                 @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-red-600/80 transition" >
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span> </button>
           </form>
            </nav>
        </div>

        <!-- Mobile Menu Toggle -->
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
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white/20 block">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>Bookings</span>
                    </a>
                    <a href="{{ route('admin.cars') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-car w-5"></i>
                        <span>Fleet</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-users w-5"></i>
                        <span>Users</span>
                    </a>
                    <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Revenue</span>
                    </a> 
                    <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button 
        type="submit" 
        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-red-600/80 transition block"
    >
        <i class="fas fa-sign-out-alt w-5"></i>
        <span>Logout</span>
    </button>
</form>

                </nav>
            </div>
        </div>
