@extends('layouts.adminlayout')

@section('content')

<div class="flex h-screen overflow-hidden">
    
    <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">

                <div class="mb-8">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Revenue Analytics</h1>
                    <p class="text-gray-600">Track your monthly revenue</p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Total Revenue</h6>
                        <div class="text-3xl font-bold text-gray-900">
                            &#8369;{{ number_format($monthlyRevenue->sum('total'), 2) }}
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-600">
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Average Monthly</h6>
                        <div class="text-3xl font-bold text-gray-900">
                            &#8369;{{ number_format($monthlyRevenue->count() > 0 ? $monthlyRevenue->sum('total') / $monthlyRevenue->count() : 0, 2) }}
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-amber-600">
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Peak Revenue</h6>
                        <div class="text-3xl font-bold text-gray-900">
                            &#8369;{{ number_format($monthlyRevenue->max('total') ?? 0, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Revenue Trend</h3>
                    <canvas id="revenueChart"
                        data-labels="{{ json_encode($monthlyRevenue->pluck('month_label')->values()) }}"
                        data-values="{{ json_encode($monthlyRevenue->pluck('total')->values()) }}">
                    </canvas>
                </div>

                <!-- Revenue Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Month</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Growth</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($monthlyRevenue as $revenue)
                                    @php
                                        $growth     = $revenue->growth;
                                        $isPositive = $growth !== null && $growth > 0;
                                        $isNegative = $growth !== null && $growth < 0;
                                        $isNeutral  = $growth === null || $growth == 0;

                                        $growthColor = $isPositive ? 'text-green-600' : ($isNegative ? 'text-red-500' : 'text-gray-400');
                                        $growthIcon  = $isPositive ? 'fa-arrow-up' : ($isNegative ? 'fa-arrow-down' : 'fa-minus');
                                        $growthText  = $isNeutral
                                            ? 'N/A'
                                            : ($isPositive ? '+' : '') . $growth . '%';

                                        if ($isPositive && $growth >= 10) {
                                            $statusBg   = 'bg-green-100 text-green-800';
                                            $statusText = 'Excellent';
                                        } elseif ($isPositive) {
                                            $statusBg   = 'bg-blue-100 text-blue-800';
                                            $statusText = 'Good';
                                        } elseif ($isNegative && $growth <= -10) {
                                            $statusBg   = 'bg-red-100 text-red-800';
                                            $statusText = 'Declining';
                                        } elseif ($isNegative) {
                                            $statusBg   = 'bg-amber-100 text-amber-800';
                                            $statusText = 'Slow';
                                        } else {
                                            $statusBg   = 'bg-gray-100 text-gray-600';
                                            $statusText = 'Baseline';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $revenue->month_label }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                &#8369;{{ number_format($revenue->total, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center gap-1 font-semibold {{ $growthColor }}">
                                                @if(!$isNeutral)
                                                    <i class="fas {{ $growthIcon }} text-xs"></i>
                                                @endif
                                                {{ $growthText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusBg }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                            No revenue data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ asset('js/admin/adminrevenue.js') }}"></script>
@endsection