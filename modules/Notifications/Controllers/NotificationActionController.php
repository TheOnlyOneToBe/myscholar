<?php

namespace Modules\Notifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Notifications\Models\Notification;
use Modules\Notifications\Services\NotificationService;

class NotificationActionController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function approve(Notification $notification, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        if ($notification->action_status !== 'pending') {
            alert_warning(__('notifications.errors.action_already_processed'), 'ACTION_ALREADY_PROCESSED');
            return response()->json(['message' => __('notifications.errors.action_already_processed')], 400);
        }

        $notification->approveAction(
            $user->id,
            $request->input('reason'),
            $request->input('response_data')
        );

        $this->executeAction($notification, 'approve');

        alert_success(__('notifications.messages.action_approved'), 'ACTION_APPROVED');

        return response()->json(['message' => __('notifications.messages.action_approved')]);
    }

    public function reject(Notification $notification, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => __('notifications.errors.permission_denied')], 403);
        }

        if ($notification->action_status !== 'pending') {
            alert_warning(__('notifications.errors.action_already_processed'), 'ACTION_ALREADY_PROCESSED');
            return response()->json(['message' => __('notifications.errors.action_already_processed')], 400);
        }

        $notification->rejectAction(
            $user->id,
            $request->input('reason', '')
        );

        alert_success(__('notifications.messages.action_rejected'), 'ACTION_REJECTED');

        return response()->json(['message' => __('notifications.messages.action_rejected')]);
    }

    private function executeAction(Notification $notification, string $action): void
    {
        $data = $notification->data ?? [];

        if ($notification->type === 'approval' && $action === 'approve') {
            if ($data['action_type'] ?? null === 'password_reset') {
                $this->notificationService->approvePasswordReset(
                    $notification->id,
                    auth('sanctum')->user()->id
                );
                $notification->update(['action_status' => 'executed']);
            }
        }
    }
}
