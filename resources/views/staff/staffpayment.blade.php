@extends('layouts.adminlayout')

@section('content')
<div class="flex h-screen overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="p-6">
            <div class="max-w-7xl mx-auto">

                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-slate-900 mb-2">Staff Payment Overview</h1>
                    <p class="text-lg text-slate-600">Review and manage payment transactions</p>
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-600 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Total Received</p>
                                <p class="text-3xl font-bold text-slate-900 mt-2">₱{{ number_format($totalReceived, 2) }}</p>
                                <p class="text-xs text-slate-500 mt-2">All time</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md border-l-4 border-green-600 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">This Month</p>
                                <p class="text-3xl font-bold text-slate-900 mt-2">₱{{ number_format($thisMonth, 2) }}</p>
                                <p class="text-xs mt-2">
                                    @if($monthlyGrowth !== null)
                                        <span class="{{ $monthlyGrowth > 0 ? 'text-green-600' : ($monthlyGrowth < 0 ? 'text-red-600' : 'text-slate-500') }}">
                                            @if($monthlyGrowth > 0) ↑ @elseif($monthlyGrowth < 0) ↓ @else — @endif
                                            {{ abs($monthlyGrowth) }}% vs last month
                                        </span>
                                    @else
                                        <span class="text-slate-500">Filtered view</span>
                                    @endif
                                </p>
                            </div>
                            <div class="bg-green-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8L5.257 19.547a2 2 0 00.247 2.748c.684.684 1.897.471 2.748-.247L19 9">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md border-l-4 border-purple-600 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Successful Payments</p>
                                <p class="text-3xl font-bold text-slate-900 mt-2">{{ $successfulPayments }}</p>
                                <p class="text-xs text-slate-500 mt-2">Completed transactions</p>
                            </div>
                            <div class="bg-purple-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                            <select id="statusFilter"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>

                            <select id="paymentTypeFilter"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Types</option>
                                <option value="booking" {{ request('payment_type') == 'booking' ? 'selected' : '' }}>Booking</option>
                                <option value="issue" {{ request('payment_type') == 'issue' ? 'selected' : '' }}>Issue</option>
                            </select>

                            <input type="date" id="dateFrom" value="{{ request('date_from') }}"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                            <input type="date" id="dateTo" value="{{ request('date_to') }}"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h2 class="text-xl font-bold text-slate-900">Recent Transactions</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Booking ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Customer</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Amount</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Method</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Verified By</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Verified At</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @forelse($payments as $payment)
                                    @php
                                        $status = $payment->paymentStatus->name ?? 'Unknown';
                                        $colors = [
                                            'Completed' => 'bg-green-100 text-green-800',
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Failed' => 'bg-red-100 text-red-800',
                                            'Cancelled' => 'bg-slate-100 text-slate-800',
                                        ];
                                        $isCompleted = strtolower($status) === 'completed';
                                        $paymentType = $payment->return_issue_id ? 'issue' : 'booking';
                                    @endphp

                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d H:i') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex flex-col gap-1">
                                                <span class="font-semibold text-blue-600">
                                                    #{{ $payment->booking_id }}
                                                </span>

                                                @if($payment->return_issue_id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-orange-100 text-orange-800 w-fit">
                                                        Issue
                                                    </span>

                                                    <span class="text-xs text-slate-500 leading-5">
                                                        {{ $payment->returnIssue->title ?? 'Return Issue' }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 w-fit">
                                                        Booking
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            {{ $payment->booking->user->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm font-bold text-slate-900 text-right">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            {{ $payment->paymentMethod->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$status] ?? 'bg-slate-100 text-slate-800' }}">
                                                {{ $status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $payment->verifiedByUser->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $payment->verified_at ? \Carbon\Carbon::parse($payment->verified_at)->format('M d, Y h:i A') : 'N/A' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-center">
                                            <div class="inline-flex items-center gap-3">
                                                @if($payment->booking->photoReceipt)
                                                    <button
                                                        onclick="openReceiptModal('{{ asset('storage/' . $payment->booking->photoReceipt->image_path) }}')"
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="View Receipt">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">No Receipt</span>
                                                @endif

                                                @if(!$isCompleted)
                                                    <button
                                                        type="button"
                                                        onclick="openApproveModal(
                                                            {{ $payment->id }},
                                                            '{{ $payment->booking_id }}',
                                                            '{{ $paymentType }}',
                                                            @js($payment->returnIssue->title ?? '')
                                                        )"
                                                        class="text-green-600 hover:text-green-800"
                                                        title="Approve Payment">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @else
                                                    <span class="inline-block text-gray-300 cursor-not-allowed" title="Already completed">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif

                                                @if(!$isCompleted)
                                                    <button
                                                        type="button"
                                                        onclick="openRejectModal(
                                                            {{ $payment->id }},
                                                            '{{ $payment->booking_id }}',
                                                            '{{ $paymentType }}',
                                                            @js($payment->returnIssue->title ?? '')
                                                        )"
                                                        class="text-red-600 hover:text-red-800"
                                                        title="Reject Payment">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @else
                                                    <span class="inline-block text-gray-300 cursor-not-allowed" title="Completed payments can no longer be rejected">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                            No payments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-2 border-t border-gray-200">
                        {{ $payments->onEachSide(1)->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-lg w-full relative">
        <button onclick="closeReceiptModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">
            ✕
        </button>

        <h3 class="text-lg font-bold mb-4">Payment Receipt</h3>
        <img id="receiptImage" class="w-full max-h-[500px] object-contain rounded border">
    </div>
</div>

<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 id="approveModalTitle" class="text-lg font-bold text-slate-900">Approve Payment</h3>
                <p id="approveModalSubtitle" class="text-sm text-slate-500 mt-1">Are you sure you want to approve this payment?</p>
            </div>
            <button type="button" onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-700 text-xl">✕</button>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span id="approveTypeLabel"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    Booking Payment
                </span>
            </div>

            <p class="text-sm text-green-800">
                Booking:
                <span id="approveBookingLabel" class="font-semibold"></span>
            </p>

            <p id="approveIssueText" class="text-sm text-green-800 mt-1 hidden"></p>

            <p id="approveTypeHint" class="text-xs text-green-700 mt-2">
                This will mark the payment as completed and confirm the booking.
            </p>
        </div>

        <form id="approveForm" method="POST">
            @csrf
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Yes, Approve
                </button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 id="rejectModalTitle" class="text-lg font-bold text-slate-900">Reject Payment</h3>
                <p id="rejectModalSubtitle" class="text-sm text-slate-500 mt-1">Are you sure you want to reject this payment?</p>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-700 text-xl">✕</button>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span id="rejectTypeLabel"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    Booking Payment
                </span>
            </div>

            <p class="text-sm text-red-800">
                Booking:
                <span id="rejectBookingLabel" class="font-semibold"></span>
            </p>

            <p id="rejectIssueText" class="text-sm text-red-800 mt-1 hidden"></p>

            <p id="rejectTypeHint" class="text-xs text-red-700 mt-2">
                Please provide the reason for rejection before continuing.
            </p>
        </div>

        <form id="rejectForm" method="POST">
            @csrf

            <label class="block text-sm font-medium mb-2">Admin Note</label>
            <textarea name="admin_note" class="w-full border rounded p-2" rows="4"
                placeholder="Explain why the receipt is rejected..." required></textarea>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Reject Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/staff/staffpayment.js') }}"></script>
@endsection