@extends('layouts.adminlayout')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Payment Overview</h1>
            <p class="text-lg text-slate-600">Manage and monitor all payment transactions</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Total Received Card -->
            <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-600 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Total Received</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2">
                            ₱{{ number_format($totalReceived ?? 125450, 2) }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">All time</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- This Month Card -->
            <div class="bg-white rounded-lg shadow-md border-l-4 border-green-600 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">This Month</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2">
                            ₱{{ number_format($thisMonth ?? 18200, 2) }}
                        </p>
                        <p class="text-xs text-green-600 mt-2">
                            ↑ {{ $monthlyGrowth ?? '12.5' }}% vs last month
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L5.257 19.547a2 2 0 00.247 2.748c.684.684 1.897.471 2.748-.247L19 9"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Successful Payments Card -->
            <div class="bg-white rounded-lg shadow-md border-l-4 border-purple-600 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Successful Payments</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $successfulPayments ?? 143 }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">Completed transactions</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <!-- Status Filter -->
                    <select id="statusFilter" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    <!-- Date Range Filter -->
                    <input type="date" id="dateFrom" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="From">
                    <input type="date" id="dateTo" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="To">
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors text-sm font-semibold">
                        Filter
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Reference</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>
                   <tbody class="divide-y divide-slate-200">
    @forelse($payments as $payment)
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4 text-sm text-slate-900">
                {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d H:i') }}

            </td>
            <td class="px-6 py-4 text-sm font-semibold text-blue-600">
                #{{ $payment->booking_id }}
            </td>
            <td class="px-6 py-4 text-sm text-slate-900">
                {{ $payment->booking->user->name ?? 'N/A' }}
            </td>
            <td class="px-6 py-4 text-sm font-bold text-slate-900 text-right">
                ₱{{ number_format($payment->amount, 2) }}
            </td>
            <td class="px-6 py-4 text-sm text-slate-900">
                {{ $payment->paymentMethod->name ?? 'PayPal' }}
            </td>
            <td class="px-6 py-4 text-sm">
                @php
                    $status = $payment->paymentStatus->name ?? 'Unknown';
                    $colors = [
                        'Completed' => 'bg-green-100 text-green-800',
                        'Pending' => 'bg-yellow-100 text-yellow-800',
                        'Failed' => 'bg-red-100 text-red-800',
                        'Cancelled' => 'bg-slate-100 text-slate-800',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$status] ?? 'bg-slate-100 text-slate-800' }}">
                    {{ $status }}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 font-mono">
                {{ $payment->transaction_id ?? 'N/A' }}
            </td>
            <td class="px-6 py-4 text-center">
                <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">View</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-6 py-4 text-center text-slate-500">No payments found.</td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
    <p class="text-sm text-slate-600">
        Showing
        <span class="font-semibold">{{ $payments->firstItem() }}</span>
        to
        <span class="font-semibold">{{ $payments->lastItem() }}</span>
        of
        <span class="font-semibold">{{ $payments->total() }}</span>
        transactions
    </p>

    <div>
        {{ $payments->links() }}
    </div>
</div>

        </div>

    </div>
</div>

@endsection