<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Check for new notifications (for polling)
     */
    public function check(Request $request)
    {
        $user = Auth::user();

        // Get unread count
        $unreadCount = $user->unreadNotifications()->count();

        // Get new notifications since last check (last 1 minute)
        $newNotifications = $user->unreadNotifications()
            ->where('created_at', '>=', now()->subMinute())
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message'] ?? '',
                    'color' => $notification->data['color'] ?? 'info',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'action' => $notification->data['action'] ?? null,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        // Get ALL unread notifications for dropdown (10 data notifikasi terakhir)
        $allUnread = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message'] ?? '',
                    'color' => $notification->data['color'] ?? 'info',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'action' => $notification->data['action'] ?? null,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'new_notifications' => $newNotifications,
            'all_unread' => $allUnread,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sebagai dibaca.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get paginated notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications();

        // Filter by read status
        if ($request->has('unread_only')) {
            $query->whereNull('read_at');
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
}
