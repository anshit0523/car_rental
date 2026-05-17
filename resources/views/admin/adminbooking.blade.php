@extends('layouts.adminlayout')

@php
    $panelPrefix = $panelPrefix ?? (request()->is('manager*') ? 'manager' : 'admin');
@endphp

@section('content')
<div class="flex h-screen overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Bookings Management</h1>
                <p class="text-gray-600">Manage all customer bookings</p>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form id="filterForm" method="GET" action="{{ route($panelPrefix . '.bookings.index') }}">
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
                                @php
                                    $statusName = $booking->status->name ?? 'Unknown';
                                    $carName = trim(($booking->car->brand->name ?? '') . ' ' . ($booking->car->model ?? ''));
                                @endphp

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        #{{ $booking->id }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $booking->user->name ?? 'N/A' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $carName !== '' ? $carName : 'N/A' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ optional($booking->pickup_at)->format('M d, Y h:i A') ?? 'N/A' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ optional($booking->return_at)->format('M d, Y h:i A') ?? 'N/A' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        &#8369;{{ number_format($booking->total_price ?? 0, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @switch($statusName)
                                                @case('Pending') bg-gray-100 text-gray-800 @break
                                                @case('Pending Payment Verification') bg-orange-100 text-orange-800 @break
                                                @case('Reserved') bg-yellow-100 text-yellow-800 @break
                                                @case('Confirmed') bg-emerald-100 text-emerald-800 @break
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

                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-3">
                                            <button
                                                class="text-blue-600 hover:text-blue-800 viewBookingBtn"
                                                data-booking-id="{{ $booking->id }}"
                                                type="button"
                                                title="View booking"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <button
                                                class="text-amber-600 hover:text-amber-800 editBookingBtn"
                                                data-booking-id="{{ $booking->id }}"
                                                data-status-name="{{ trim($booking->status->name ?? '') }}"
                                                type="button"
                                                title="Update status"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            @if(($booking->status->name ?? '') === 'Return')
                                                <a
                                                    href="{{ route($panelPrefix . '.return-issues.create', $booking->id) }}"
                                                    class="text-rose-600 hover:text-rose-800"
                                                    title="Create return issue"
                                                >
                                                    <i class="fas fa-triangle-exclamation"></i>
                                                </a>
                                            @endif
                                        </div>
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

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>

        <!-- Edit Status Modal -->
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

                        <p class="text-sm text-gray-500 mt-2">
                            Damage, Needs Repair, and Checkup will open a detailed issue form.
                        </p>
                    </div>

                    <div id="adminMessageWrapper" class="mb-4 hidden">
                        <label for="adminMessage" class="block text-gray-700 font-medium mb-1">
                            Optional Message to User
                        </label>

                        <textarea
                            name="admin_message"
                            id="adminMessage"
                            rows="4"
                            placeholder="Example: Your return inspection has been completed."
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></textarea>

                        <p class="text-sm text-gray-500 mt-2">
                            Use this for simple updates only. Damage, repair, and checkup statuses will open a detailed issue form.
                        </p>
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

<!-- View Booking Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Booking Details</h2>

            <button type="button" id="closeViewModal" class="text-gray-500 hover:text-gray-700 text-xl">
                &times;
            </button>
        </div>

        <div class="space-y-3 text-sm text-gray-700">
            <p><strong>Booking ID:</strong> <span id="viewBookingId">-</span></p>
            <p><strong>User:</strong> <span id="viewUserName">-</span></p>
            <p><strong>Email:</strong> <span id="viewUserEmail">-</span></p>
            <p><strong>Phone:</strong> <span id="viewUserPhone">-</span></p>
                        <p>
                <strong>Car:</strong>
                <span id="viewCarBrand">-</span>
                <span id="viewCarModel">-</span>
                —
                <strong>Plate:</strong>
                <span id="viewPlateNumber">-</span>
                     </p>
            <p><strong>Service Type:</strong> <span id="viewServiceType">-</span></p>

            <p id="viewServiceLocationWrapper">
                <strong>Service Location:</strong> <span id="viewServiceLocation">-</span>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.bookingStatusMap = @json($statuses->pluck('id', 'name'));

    window.AdminBookingConfig = {
        bookingDetailsBaseUrl: @json(url($panelPrefix . '/bookings')),
        updateStatusBaseUrl: @json(url($panelPrefix . '/bookings')),
        returnIssueCreateBaseUrl: @json(url($panelPrefix . '/bookings')),
    };
</script>

<script src="{{ asset('js/admin/adminbooking.js') }}?v={{ time() }}"></script>
@endsection