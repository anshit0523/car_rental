<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class UserNotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $filter = strtolower($request->get('filter', 'all'));

        $notifications = Notification::where('user_id', $request->user()->id)
            ->when($filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($filter === 'read', fn ($q) => $q->where('is_read', true))
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'is_read' => (bool) $notification->is_read,
                    'time' => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at,
                ];
            });

        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'notification' => $notification->fresh(),
        ]);
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }
}