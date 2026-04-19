<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $notifications = Notification::where('user_id', auth()->id())
            ->when($filter === 'unread', function ($query) {
                $query->where('is_read', false);
            })
            ->when($filter === 'read', function ($query) {
                $query->where('is_read', true);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('user.user_notifications', compact(
            'notifications',
            'filter',
            'unreadCount'
        ));
    }

    // notification mark as read
    public function markRead($id)
    {
        $notification = Notification::findOrFail($id);

        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update([
            'is_read' => true
        ]);

        return redirect($notification->link ?: route('user.browse'));
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return redirect()
            ->route('user.notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    // Fetch latest notifications
    public function latestNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'read_url' => route('user.notifications.read', $notification->id),
                    'link' => $notification->link,
                    'is_read' => $notification->is_read,
                ];
            });

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread' => $unreadCount
        ]);
    }
}