@extends('layouts.userlayout')

@section('content')
<style>
    .rewards-page {
        padding: 28px;
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Outfit', sans-serif;
    }

    .rewards-title {
        font-size: 30px;
        font-weight: 750;
        color: #111827;
        margin-bottom: 2px;
    }

    .rewards-breadcrumb {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 28px;
    }

    .rewards-breadcrumb span {
        color: #ff5a1f;
        font-weight: 800;
    }

    .summary-card,
    .history-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 28px 24px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 16px;
        border-right: 1px solid #e5e7eb;
        padding: 0 18px;
    }

    .summary-item:last-child {
        border-right: none;
    }

    .summary-icon {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: #fff7ed;
        color: #ff5a1f;
    }

    .summary-label {
        font-size: 14px;
        color: #111827;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 30px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .summary-unit {
        font-size: 13px;
        font-weight: 800;
        color: #ff5a1f;
        margin-top: 6px;
    }

    .text-green { color: #16a34a; }
    .text-red { color: #dc2626; }
    .text-orange { color: #ff5a1f; }

    .reward-info {
        padding: 16px 24px;
        background: #fff7ed;
        color: #f04b00;
        font-size: 14px;
        font-weight: 500;
        border-top: 1px solid #fed7aa;
    }

    .history-card {
        margin-top: 26px;
        padding: 24px;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }

    .history-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #111827;
    }

    .filter-btn {
        border: 1px solid #d1d5db;
        background: #fff;
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 800;
        color: #111827;
    }

    .points-table {
        width: 100%;
        border-collapse: collapse;
    }

    .points-table th {
        text-align: left;
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        padding: 14px 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .points-table td {
        padding: 16px 10px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        color: #111827;
        font-weight: 650;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 750;
    }

    .badge-earned {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge-redeemed {
        background: #fee2e2;
        color: #dc2626;
    }

    .points-positive {
        color: #16a34a;
        font-weight: 900;
    }

    .points-negative {
        color: #dc2626;
        font-weight: 900;
    }

    @media (max-width: 900px) {
        .summary-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .summary-item {
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 18px;
        }

        .points-table {
            min-width: 760px;
        }

        .table-wrap {
            overflow-x: auto;
        }
    }
</style>

<div class="rewards-page">
    <h1 class="rewards-title">My Rewards / Points History</h1>
    <div class="rewards-breadcrumb">
        <span>Home</span> &nbsp;›&nbsp; My Rewards
    </div>

    <div class="summary-card">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-icon">☆</div>
                <div>
                    <div class="summary-label">Current Balance</div>
                    <div class="summary-value">{{ number_format($currentBalance ?? 0) }}</div>
                    <div class="summary-unit">Points</div>
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-icon" style="color:#16a34a;background:#dcfce7;">↗</div>
                <div>
                    <div class="summary-label">Total Earned</div>
                    <div class="summary-value text-green">{{ number_format($totalEarned ?? 0) }}</div>
                    <div class="summary-unit text-green">Points</div>
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-icon" style="color:#dc2626;background:#fee2e2;">↘</div>
                <div>
                    <div class="summary-label">Total Redeemed</div>
                    <div class="summary-value text-red">{{ number_format($totalRedeemed ?? 0) }}</div>
                    <div class="summary-unit text-red">Points</div>
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-icon">🎟</div>
                <div>
                    <div class="summary-label">Available Discount</div>
                    <div class="summary-value text-orange">
                        ₱{{ number_format(($currentBalance ?? 0) / 10, 2) }}
                    </div>
                    <div class="summary-unit" style="color:#111827;">
                        {{ number_format($currentBalance ?? 0) }} points
                    </div>
                </div>
            </div>
        </div>

        <div class="reward-info">
            ⓘ You earn 1 point for every ₱100 spent. Use your points to get discounts on your next booking.
        </div>
    </div>

    <div class="history-card">
        <div class="history-header">
            <h3>Points History</h3>
            <button class="filter-btn">All Transactions ▾</button>
        </div>

        <div class="table-wrap">
            <table class="points-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Booking ID</th>
                        <th>Type</th>
                        <th>Points</th>
                        <th>Balance After</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $isEarned = $transaction->points_change > 0;
                        @endphp

                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}</td>
                            <td>#{{ $transaction->booking_id ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $isEarned ? 'badge-earned' : 'badge-redeemed' }}">
                                    {{ $isEarned ? 'Earned' : 'Redeemed' }}
                                </span>
                            </td>
                            <td class="{{ $isEarned ? 'points-positive' : 'points-negative' }}">
                                {{ $isEarned ? '+' : '' }}{{ number_format($transaction->points_change) }}
                            </td>
                            <td>{{ number_format($transaction->balance_after) }}</td>
                            <td>{{ $transaction->note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:#64748b;">
                                No points history yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection