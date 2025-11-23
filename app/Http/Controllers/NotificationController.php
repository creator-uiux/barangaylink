<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $query = Notification::where('user_id', $request->user()->id);

        // Filter by read status if provided
        if ($request->has('read')) {
            $read = filter_var($request->read, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_read', $read);
        }

        // Filter by type if provided
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
                               ->paginate(20)
                               ->through(function ($notification) {
                                   return [
                                       'id' => $notification->id,
                                       'type' => $notification->type,
                                       'title' => $notification->title,
                                       'message' => $notification->message,
                                       'is_read' => $notification->is_read,
                                       'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                                       'action_url' => $notification->action_url,
                                       'action_text' => $notification->action_text,
                                       'icon' => $notification->icon,
                                       'color' => $notification->color,
                                       'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                                   ];
                               });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function show(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                                   ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'is_read' => $notification->is_read,
                'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                'action_url' => $notification->action_url,
                'action_text' => $notification->action_text,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                                   ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => [
                'id' => $notification->id,
                'is_read' => $notification->is_read,
                'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    public function markAsUnread(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                                   ->findOrFail($id);

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread',
            'data' => [
                'id' => $notification->id,
                'is_read' => $notification->is_read,
                'read_at' => null,
            ]
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
                   ->where('is_read', false)
                   ->update([
                       'is_read' => true,
                       'read_at' => now(),
                   ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
                            ->where('is_read', false)
                            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                                   ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}
