@extends('layouts.adminlayout')

@section('content')
<style>
    .points-page {
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 24px;
        background: #f8fafc;
        font-family: 'Outfit', sans-serif;
    }

    .points-content-wrap {
        max-width: 100%;
        padding-bottom: 40px;
    }

    .points-title {
        font-size: 28px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 4px;
    }

    .points-subtitle {
        color: #64748b;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .points-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 18px;
    }

    .points-tab {
        border: 1px solid #e5e7eb;
        padding: 12px 18px;
        border-radius: 12px;
        background: #fff;
        color: #111827;
        font-weight: 780;
        cursor: pointer;
    }

    .points-tab.active {
        background: #ff5a1f;
        color: #fff;
        border-color: #ff5a1f;
    }

    .points-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .points-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .points-search,
    .transaction-search,
    .transaction-filter {
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 11px 14px;
        outline: none;
        font-weight: 600;
        background: #fff;
    }

    .points-search {
        width: 320px;
        max-width: 100%;
    }

    .transaction-search {
        width: 260px;
        max-width: 100%;
    }

    .points-search:focus,
    .transaction-search:focus,
    .transaction-filter:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 4px rgba(255, 90, 31, 0.12);
    }

    .transaction-tools {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .points-table-wrap {
        overflow-x: auto;
    }

    .points-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .points-table th {
        text-align: left;
        padding: 14px 18px;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .points-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        font-size: 14px;
        font-weight: 650;
        vertical-align: middle;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        background: #ff5a1f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 750;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 750;
        color: #111827;
    }

    .user-email {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .text-orange { color: #ff5a1f; font-weight: 900; }
    .text-green { color: #16a34a; font-weight: 900; }
    .text-red { color: #dc2626; font-weight: 900; }

    .adjust-btn {
        border: none;
        background: #ff5a1f;
        color: #fff;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .badge {
        padding: 6px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .badge-earned,
    .badge-manual_add,
    .badge-return {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge-redeemed,
    .badge-manual_deduct {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-default {
        background: #fff7ed;
        color: #ff5a1f;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .points-modal {
        width: 100%;
        max-width: 460px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 24px 70px rgba(0,0,0,0.25);
        overflow: hidden;
    }

    .points-modal-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .points-modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .modal-close {
        border: none;
        background: #f3f4f6;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 900;
    }

    .points-modal-body {
        padding: 20px;
    }

    .modal-user-box {
        padding: 14px;
        border-radius: 16px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #111827;
        font-size: 13px;
        font-weight: 780;
    }

    .form-control {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 12px 14px;
        outline: none;
        font-weight: 600;
    }

    .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 4px rgba(255, 90, 31, 0.12);
    }

    .radio-row {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
    }

    .radio-option {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        font-weight: 650;
    }

    .deduct-warning {
        display: none;
        margin-top: -4px;
        margin-bottom: 16px;
        padding: 12px;
        border-radius: 12px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 18px;
    }

    .btn-cancel {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #111827;
        padding: 11px 16px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-save {
        border: none;
        background: #ff5a1f;
        color: #fff;
        padding: 11px 16px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .pagination-wrap {
        padding: 18px 20px;
    }
</style>

<div class="points-page">
    <div class="points-content-wrap">
        <h1 class="points-title">Rewards & Points Management</h1>
        <p class="points-subtitle">Manage customer reward points and transactions.</p>

        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;padding:14px 16px;border-radius:12px;margin-bottom:16px;font-weight:700;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:14px 16px;border-radius:12px;margin-bottom:16px;font-weight:700;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="points-tabs">
            <button type="button" class="points-tab active" data-tab="balances">User Balances</button>
            <button type="button" class="points-tab" data-tab="transactions">Transactions History</button>
        </div>

        <div id="balancesPanel" class="tab-panel active">
            <div class="points-card">
                <div class="points-card-header">
                    <form method="GET" action="{{ route('admin.points.index') }}">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="points-search"
                            placeholder="Search user..."
                        >
                    </form>

                    <strong style="color:#111827;">Total Users: {{ $users->total() }}</strong>
                </div>

                <div class="points-table-wrap">
                    <table class="points-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Current Balance</th>
                                <th>Total Earned</th>
                                <th>Total Redeemed</th>
                                <th>Last Activity</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-sm">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $user->name }}</div>
                                                <div class="user-email">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="text-orange">{{ number_format($user->points_balance ?? 0) }}</span>
                                        <small>Points</small>
                                    </td>

                                    <td>
                                        <span class="text-green">{{ number_format($user->total_earned ?? 0) }}</span>
                                        <small>Points</small>
                                    </td>

                                    <td>
                                        <span class="text-red">{{ number_format($user->total_redeemed ?? 0) }}</span>
                                        <small>Points</small>
                                    </td>

                                    <td>
                                        {{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->format('M d, Y h:i A') : 'N/A' }}
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="adjust-btn"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-balance="{{ $user->points_balance ?? 0 }}"
                                            data-action="{{ route('admin.points.adjust', $user->id) }}"
                                        >
                                            Adjust Points
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;color:#64748b;padding:24px;">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <div id="transactionsPanel" class="tab-panel">
            <div class="points-card">
                <div class="points-card-header">
                    <strong style="font-size:18px;color:#111827;">Points Transactions History</strong>

                    <div class="transaction-tools">
                        <input
                            type="text"
                            id="transactionSearch"
                            class="transaction-search"
                            placeholder="Search user, booking, note..."
                        >

                        <select id="transactionFilter" class="transaction-filter">
                            <option value="all">All</option>
                            <option value="earn">Earned</option>
                            <option value="redeem">Redeemed</option>
                            <option value="return">Returned</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                </div>

                <div class="points-table-wrap">
                    <table class="points-table" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
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
                                    $type = $transaction->type_name ?? 'default';
                                    $isPositive = $transaction->points_change > 0;
                                    $filterType = in_array($type, ['manual_add', 'manual_deduct']) ? 'manual' : $type;

                                    $badgeClass = match($type) {
                                        'earn' => 'badge-earned',
                                        'redeem' => 'badge-redeemed',
                                        'return' => 'badge-return',
                                        'manual_add' => 'badge-manual_add',
                                        'manual_deduct' => 'badge-manual_deduct',
                                        default => 'badge-default',
                                    };
                                @endphp

                                <tr data-type="{{ $filterType }}">
                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="user-name">{{ $transaction->user_name ?? 'N/A' }}</div>
                                        <div class="user-email">{{ $transaction->user_email ?? '' }}</div>
                                    </td>
                                    <td>{{ $transaction->booking_id ? '#' . $transaction->booking_id : 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucwords(str_replace('_', ' ', $type)) }}
                                        </span>
                                    </td>
                                    <td class="{{ $isPositive ? 'text-green' : 'text-red' }}">
                                        {{ $isPositive ? '+' : '' }}{{ number_format($transaction->points_change) }}
                                    </td>
                                    <td>{{ number_format($transaction->balance_after) }}</td>
                                    <td>{{ $transaction->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;color:#64748b;padding:24px;">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="noTransactionMatch" style="display:none;">
                                <td colspan="7" style="text-align:center;color:#64748b;padding:24px;">
                                    No matching transactions found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="adjustPointsModal" class="modal-backdrop">
    <div class="points-modal">
        <div class="points-modal-header">
            <h3>Adjust Reward Points</h3>
            <button type="button" class="modal-close" id="closeAdjustModal">×</button>
        </div>

        <form method="POST" id="adjustPointsForm">
            @csrf

            <div class="points-modal-body">
                <div class="modal-user-box">
                    <div class="user-name" id="modalUserName">Customer</div>
                    <div class="user-email" id="modalUserEmail">email@example.com</div>
                    <div style="margin-top:8px;font-weight:700;">
                        Current Balance:
                        <span class="text-orange" id="modalUserBalance">0 Points</span>
                    </div>
                </div>

                <label class="form-label">Action</label>
                <div class="radio-row">
                    <label class="radio-option">
                        <input type="radio" name="action" value="add" checked>
                        Add Points
                    </label>

                    <label class="radio-option">
                        <input type="radio" name="action" value="deduct">
                        Deduct Points
                    </label>
                </div>

                <div id="deductWarning" class="deduct-warning">
                    ⚠️ Deducting points will reduce the customer’s balance. Please confirm before saving.
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Points Amount</label>
                    <input type="number" name="points" class="form-control" min="1" required placeholder="Enter points amount">
                </div>

                <div>
                    <label class="form-label">Reason / Notes</label>
                    <textarea name="note" class="form-control" rows="4" maxlength="500" required placeholder="Enter reason or notes"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelAdjustModal">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.points-tab');
        const panels = {
            balances: document.getElementById('balancesPanel'),
            transactions: document.getElementById('transactionsPanel'),
        };

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const tab = this.dataset.tab;

                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                Object.values(panels).forEach(panel => panel.classList.remove('active'));
                panels[tab].classList.add('active');
            });
        });

        const modal = document.getElementById('adjustPointsModal');
        const form = document.getElementById('adjustPointsForm');
        const closeBtn = document.getElementById('closeAdjustModal');
        const cancelBtn = document.getElementById('cancelAdjustModal');
        const deductWarning = document.getElementById('deductWarning');

        document.querySelectorAll('.adjust-btn').forEach(button => {
            button.addEventListener('click', function () {
                form.action = this.dataset.action;

                document.getElementById('modalUserName').textContent = this.dataset.userName;
                document.getElementById('modalUserEmail').textContent = this.dataset.userEmail;
                document.getElementById('modalUserBalance').textContent =
                    Number(this.dataset.balance || 0).toLocaleString() + ' Points';

                deductWarning.style.display = 'none';
                modal.classList.add('show');
            });
        });

        function closeModal() {
            modal.classList.remove('show');
            form.reset();
            deductWarning.style.display = 'none';
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        form.querySelectorAll('input[name="action"]').forEach(radio => {
            radio.addEventListener('change', function () {
                deductWarning.style.display = this.value === 'deduct' ? 'block' : 'none';
            });
        });

        form.addEventListener('submit', function (e) {
            const selectedAction = form.querySelector('input[name="action"]:checked')?.value;

            if (selectedAction === 'deduct') {
                const confirmed = confirm('Are you sure you want to deduct points from this customer?');

                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });

        const transactionSearch = document.getElementById('transactionSearch');
        const transactionFilter = document.getElementById('transactionFilter');
        const transactionRows = document.querySelectorAll('#transactionsTable tbody tr[data-type]');
        const noMatchRow = document.getElementById('noTransactionMatch');

        function filterTransactions() {
            const searchValue = (transactionSearch?.value || '').toLowerCase().trim();
            const selectedType = transactionFilter?.value || 'all';
            let visibleCount = 0;

            transactionRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowType = row.dataset.type;

                const matchesSearch = rowText.includes(searchValue);
                const matchesType = selectedType === 'all' || rowType === selectedType;

                if (matchesSearch && matchesType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noMatchRow) {
                noMatchRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        transactionSearch?.addEventListener('input', filterTransactions);
        transactionFilter?.addEventListener('change', filterTransactions);
    });
</script>
@endsection