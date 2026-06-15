<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiNotificationController extends ApiBaseController
{
    /**
     * Get user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(20);

        return $this->sendResponse($notifications, 'Notifications retrieved successfully');
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->sendResponse(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->sendResponse(null, 'All notifications marked as read');
    }
}
