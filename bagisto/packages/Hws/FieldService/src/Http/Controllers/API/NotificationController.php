<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hws\FieldService\Models\Notification;

class NotificationController extends Controller
{
    /**
     * List all notifications for the authenticated employee.
     */
    public function index(Request $request)
    {
        $employeeId = auth()->guard('admin-api')->id();

        $notifications = Notification::where('admin_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('admin_id', $employeeId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    /**
     * Mark notification(s) as read.
     */
    public function markRead(Request $request)
    {
        $employeeId = auth()->guard('admin-api')->id();
        $id = $request->input('id');

        $query = Notification::where('admin_id', $employeeId);

        if ($id) {
            $query->where('id', $id)->update(['is_read' => true]);
        } else {
            $query->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
