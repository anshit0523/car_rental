@extends('layouts.adminlayout')

@section('content')
<div class="flex h-screen overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Staff Bookings</h1>
                <p class="text-gray-600">Manage customer bookings</p>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form id="filterForm" method="GET" action="{{ route('staff.bookings.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by user"
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
                                @php $statusName = $booking->status->name ?? 'Unknown'; @endphp

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $booking->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->car->brand->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ optional($booking->pickup_at)->format('M d, Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ optional($booking->return_at)->format('M d, Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">&#8369;{{ number_format($booking->total_price, 2) }}</td>

                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @switch($statusName)
                                                @case('Pending') bg-gray-100 text-gray-800 @break
                                                @case('Reserved') bg-yellow-100 text-yellow-800 @break
                                                @case('Active') bg-blue-100 text-blue-800 @break
                                                @case('Return') bg-orange-100 text-orange-800 @break
                                                @case('Completed') bg-green-100 text-green-800 @break
                                                @case('Cancelled') bg-red-100 text-red-800 @break
                                                @case('Checkup') bg-purple-100 text-purple-800 @break
                                                @case('Damage') bg-rose-100 text-rose-800 @break
                                                @case('Needs Repair') bg-amber-100 text-amber-800 @break
                                                @case('Failed') bg-gray-300 text-gray-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $statusName }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <button
                                            class="text-amber-600 hover:text-amber-800 editBookingBtn"
                                            data-booking-id="{{ $booking->id }}"
                                            data-status-name="{{ trim($booking->status->name ?? '') }}"
                                        >
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

                <div class="p-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>

        <div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Update Booking Status</h2>

                <form id="updateStatusForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Select Status</label>
                        <select
                            name="status_id"
                            id="statusDropdown"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></select>
                        <p id="statusHelpText" class="text-sm text-gray-500 mt-2 hidden"></p>
                    </div>

                    <div id="adminMessageWrapper" class="mb-4 hidden">
                        <label for="adminMessage" class="block text-gray-700 font-medium mb-1">
                            Optional Message to User
                        </label>

                        <textarea
                            name="admin_message"
                            id="adminMessage"
                            rows="4"
                            placeholder="Example: The car was returned with low fuel level."
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            id="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            id="saveStatusBtn"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.bookingStatusMap = @json($statuses->pluck('id', 'name'));
    window.staffBookingUpdateUrlTemplate = "{{ route('staff.bookings.update-status', ':id') }}";
</script>
<script src="{{ asset('js/staff/staffbooking.js') }}"></script>
@endsection