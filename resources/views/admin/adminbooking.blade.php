<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
       

       <!-- Sidebar/Navbar -->
<div class="fixed left-0 top-0 h-screen w-64 bg-gray-900 text-white p-6 text-white p-6 overflow-y-auto">
    <div class="mb-8 flex items-center gap-2 text-xl font-bold">
        <i class="fas fa-car"></i>
        <span>Car Rental</span>
    </div>
    
    <nav class="space-y-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 transition">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

       <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 transition">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        <a href="{{ route('admin.cars') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 transition">
            <i class="fas fa-car"></i>
            <span>Cars</span>
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 transition">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 transition">
            <i class="fas fa-chart-bar"></i>
            <span>Revenue</span>
        </a>
    </nav>
</div>

        <!-- Main Content -->
        <div class="ml-64 w-full p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Bookings Management</h1>
                <p class="text-gray-600">Manage all customer bookings</p>
            </div>

            <!-- Search & Filter -->
           <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form id="filterForm" method="GET" action="{{ route('admin.bookings.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Search by user or car..." 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >

            <select 
                name="status_id"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="">Filter by Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>

            <button 
                type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition flex items-center justify-center gap-2"
            >
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>
</div>


      <!-- Bookings Table -->
   <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Booking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Car</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Pickup Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Return Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            #{{ $booking->id }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $booking->user->name ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $booking->car->brand->name ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ optional($booking->pickup_at)->format('M d, Y') ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ optional($booking->return_at)->format('M d, Y') ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            ${{ number_format($booking->total_price, 2) }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @switch($booking->status->name)
                                    @case('completed') bg-green-100 text-green-800 @break
                                    @case('active') bg-blue-100 text-blue-800 @break
                                    @case('reserved') bg-yellow-100 text-yellow-800 @break
                                    @case('cancelled') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">
                                {{ $booking->status->name ?? 'Unknown' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm space-x-2">
                            <button class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-amber-600 hover:text-amber-800">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">
                            No bookings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-gray-200">
        {{ $bookings->links() }}
    </div>
</div>


            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <nav class="flex items-center gap-1">
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg">1</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">2</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">3</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const statusSelect = form.querySelector('select[name="status_id"]');

    // Auto-submit when typing stops (after 500ms)
    let typingTimer;
    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => form.submit(), 500);
    });

    // Submit immediately when status changes
    statusSelect.addEventListener('change', () => form.submit());
});
</script>

</html>