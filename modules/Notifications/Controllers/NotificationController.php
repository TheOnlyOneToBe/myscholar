<?php

namespace Modules\Notifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Notifications\Models\Notification;
use Modules\Notifications\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || !$user->hasPermission('notifications.view')) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $notifications = $this->notificationService->getUserNotifications(
            $user->id,
            $request->input('per_page', 50)
        );

        return response()->json($notifications);
    }

    public function show(Notification $notification): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        return response()->json(['data' => $notification]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $notification->markAsRead();

        return response()->json(['message' => __('notifications.messages.marked_as_read')]);
    }

    public function markAsUnread(Notification $notification): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $notification->markAsUnread();

        return response()->json(['message' => __('notifications.messages.marked_as_unread')]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $this->notificationService->markAllAsRead($user->id);

        return response()->json(['message' => __('notifications.messages.all_marked_as_read')]);
    }

    public function delete(Notification $notification): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $this->notificationService->deleteNotification($notification->id);

        return response()->json(['message' => __('notifications.messages.notification_deleted')]);
    }

    public function unreadCount(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json(['unread_count' => $count]);
    }

    public function pending(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || !$user->hasPermission('notifications.view')) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        $notifications = $this->notificationService->getPendingActions(
            $user->id,
            $request->input('per_page', 50)
        );

        return response()->json($notifications);
    }
}
