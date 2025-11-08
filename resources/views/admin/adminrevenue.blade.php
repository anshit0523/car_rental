@extends('layouts.adminlayout')

@section('content')

    <div class="flex-1 p-6 lg:p-8 w-full lg:ml-0">
 <div class="mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Revenue Analytics</h1>
                <p class="text-gray-600">Track your monthly revenue</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Total Revenue</h6>
                    <div class="text-3xl font-bold text-gray-900">&#8369;{{ number_format($monthlyRevenue->sum('total') ?? 156890, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-600">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Average Monthly</h6>
                    <div class="text-3xl font-bold text-gray-900">&#8369;{{ number_format($monthlyRevenue->count() > 0 ? $monthlyRevenue->sum('total') / $monthlyRevenue->count() : 13074, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-amber-600">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Peak Revenue</h6>
                    <div class="text-3xl font-bold text-gray-900">&#8369;{{ number_format($monthlyRevenue->max('total') ?? 18500, 2) }}</div>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Revenue Trend</h3>
                <canvas id="revenueChart" 
                   data-labels="{{ json_encode($monthlyRevenue->pluck('month')) }}"
                data-values="{{ json_encode($monthlyRevenue->pluck('total')) }}">
                    </canvas>
            </div>

            <!-- Revenue Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
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
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $revenue->month }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            &#8369;{{ number_format($revenue->total, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <i class="fas fa-arrow-up text-green-600"></i> +5.2%
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Good</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No revenue data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
@endsection

@section('scripts')
<script src="{{ asset('js/adminrevenue.js') }}"></script>
@endsection
