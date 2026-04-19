@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .notifications-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        padding: 16px 0 24px;
    }

    .notifications-wrap {
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
        padding: 0 12px;
    }

    .notifications-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .notifications-header {
        padding: 18px 18px 14px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    }

    .notifications-title {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        color: #111827;
        margin: 0 0 6px;
    }

    .notifications-subtitle {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    .notifications-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px;
        margin-top: 14px;
    }

    .notifications-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .notifications-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 9999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        transition: 0.2s ease;
    }

    .notifications-filter:hover {
        background: #f9fafb;
        color: #111827;
    }

    .notifications-filter.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }

    .notifications-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        transition: 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .notifications-btn-primary {
        background: #4f46e5;
        color: #fff !important;
    }

    .notifications-btn-primary:hover {
        background: #4338ca;
        color: #fff !important;
    }

    .notifications-body {
        padding: 18px;
    }

    .notifications-success {
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 16px;
        border: 1px solid #86efac;
        background: #f0fdf4;
        color: #166534;
        font-size: 13px;
        font-weight: 700;
    }

    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .notification-item {
        display: block;
        text-decoration: none;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
        background: #fff;
        transition: 0.2s ease;
    }

    .notification-item:hover {
        border-color: #c7d2fe;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.08);
    }

    .notification-item.unread {
        border-color: #c7d2fe;
        background: #f8faff;
    }

    .notification-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 6px;
    }

    .notification-title {
        font-size: 15px;
        font-weight: 800;
        line-height: 1.4;
        color: #111827;
        margin: 0;
    }

    .notification-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }

    .notification-message {
        font-size: 13px;
        line-height: 1.6;
        color: #4b5563;
        margin-bottom: 8px;
    }

    .notification-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .notification-time {
        font-size: 12px;
        color: #6b7280;
    }

    .notification-link-text {
        font-size: 12px;
        font-weight: 700;
        color: #4f46e5;
    }

    .notifications-empty {
        text-align: center;
        padding: 28px 16px;
        color: #6b7280;
        font-size: 13px;
        border: 1px dashed #d1d5db;
        border-radius: 14px;
        background: #f9fafb;
    }

    .notifications-footer {
        margin-top: 18px;
    }

    @media (max-width: 640px) {
        .notifications-wrap {
            max-width: 100%;
            padding: 0 8px;
        }

        .notifications-header,
        .notifications-body {
            padding: 14px;
        }

        .notifications-title {
            font-size: 18px;
        }

        .notifications-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .notification-top {
            flex-direction: column;
            gap: 8px;
        }

        .notification-meta {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="notifications-page">
    <div class="notifications-wrap">
        <div class="notifications-card">
            <div class="notifications-header">
                <h1 class="notifications-title">Notifications</h1>
                <div class="notifications-subtitle">
                    View your latest booking, payment, and return issue updates.
                </div>

                <div class="notifications-toolbar">
                    <div class="notifications-filters">
                        <a
                            href="{{ route('user.notifications.index', ['filter' => 'all']) }}"
                            class="notifications-filter {{ $filter === 'all' ? 'active' : '' }}"
                        >
                            All
                        </a>

                        <a
                            href="{{ route('user.notifications.index', ['filter' => 'unread']) }}"
                            class="notifications-filter {{ $filter === 'unread' ? 'active' : '' }}"
                        >
                            Unread ({{ $unreadCount }})
                        </a>

                        <a
                            href="{{ route('user.notifications.index', ['filter' => 'read']) }}"
                            class="notifications-filter {{ $filter === 'read' ? 'active' : '' }}"
                        >
                            Read
                        </a>
                    </div>

                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('user.notifications.markAllRead') }}">
                            @csrf
                            <button type="submit" class="notifications-btn notifications-btn-primary">
                                Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="notifications-body">
                @if(session('success'))
                    <div class="notifications-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($notifications->count())
                    <div class="notifications-list">
                        @foreach($notifications as $notification)
                            <a
                                href="{{ route('user.notifications.read', $notification->id) }}"
                                class="notification-item {{ !$notification->is_read ? 'unread' : '' }}"
                            >
                                <div class="notification-top">
                                    <h2 class="notification-title">
                                        {{ $notification->title }}
                                    </h2>

                                    @if(!$notification->is_read)
                                        <span class="notification-badge">Unread</span>
                                    @endif
                                </div>

                                <div class="notification-message">
                                    {{ $notification->message }}
                                </div>

                                <div class="notification-meta">
                                    <span class="notification-time">
                                        {{ $notification->created_at->format('M d, Y h:i A') }}
                                    </span>

                                    <span class="notification-link-text">
                                        View Details
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="notifications-footer">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="notifications-empty">
                        No notifications found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection