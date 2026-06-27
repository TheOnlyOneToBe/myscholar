<?php

namespace Modules\Notifications\Services;

use Modules\Notifications\Models\Notification;
use Modules\Notifications\Models\NotificationActionLog;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notify(
        User|int $user,
        string $title,
        string $message,
        string $type = 'system',
        string $priority = 'normal',
        ?array $data = null,
        ?string $entityType = null,
        ?int $entityId = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'data' => $data,
            'related_entity_type' => $entityType,
            'related_entity_id' => $entityId,
        ]);
    }

    public function notifyWithAction(
        User|int $user,
        string $title,
        string $message,
        array $actions,
        string $type = 'approval',
        string $priority = 'high',
        ?string $targetRoute = null,
        ?array $actionParameters = null,
        ?array $data = null,
        ?string $entityType = null,
        ?int $entityId = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'actions' => $actions,
            'action_target_route' => $targetRoute,
            'action_parameters' => $actionParameters,
            'action_status' => 'pending',
            'data' => $data,
            'related_entity_type' => $entityType,
            'related_entity_id' => $entityId,
        ]);
    }

    public function notifyAdminsForPasswordReset(User $user): Notification
    {
        $admins = User::role('super_administrator')->get();

        if ($admins->isEmpty()) {
            Log::warning('No super administrators found for password reset notification');
            return null;
        }

        $admin = $admins->first();

        return $this->notifyWithAction(
            $admin,
            __('notifications.labels.password_reset_request'),
            __('notifications.messages.user_requested_password_reset', ['name' => $user->full_name]),
            ['approve' => __('notifications.actions.approve'), 'reject' => __('notifications.actions.reject')],
            type: 'approval',
            priority: 'high',
            targetRoute: 'admin.users.reset-password-approve',
            actionParameters: ['user_id' => $user->id],
            data: [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'action_type' => 'password_reset',
            ]
        );
    }

    public function approvePasswordReset(int $notificationId, int $adminId): bool
    {
        $notification = Notification::find($notificationId);

        if (!$notification || $notification->action_status !== 'pending') {
            return false;
        }

        $notification->approveAction($adminId);

        if (isset($notification->action_parameters['user_id'])) {
            $user = User::find($notification->action_parameters['user_id']);
            if ($user) {
                $tempPassword = $this->generateTemporaryPassword();
                $user->update(['password' => bcrypt($tempPassword)]);

                $this->notify(
                    $user,
                    __('notifications.labels.password_reset'),
                    __('notifications.messages.password_reset_by_admin', ['password' => $tempPassword]),
                    type: 'security',
                    priority: 'high',
                    data: ['temporary_password' => $tempPassword]
                );

                return true;
            }
        }

        return false;
    }

    public function rejectPasswordReset(int $notificationId, int $adminId, string $reason = ''): bool
    {
        $notification = Notification::find($notificationId);

        if (!$notification || $notification->action_status !== 'pending') {
            return false;
        }

        $notification->rejectAction($adminId, $reason);

        if (isset($notification->action_parameters['user_id'])) {
            $user = User::find($notification->action_parameters['user_id']);
            if ($user) {
                $this->notify(
                    $user,
                    __('notifications.labels.password_reset_rejected'),
                    __('notifications.messages.password_reset_rejected', ['reason' => $reason]),
                    type: 'security',
                    priority: 'normal',
                    data: ['rejection_reason' => $reason]
                );
            }
        }

        return false;
    }

    public function getUserNotifications(int $userId, int $limit = 50): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function getPendingActions(int $userId, int $limit = 50): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->where('action_status', 'pending')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public function markAsUnread(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsUnread();
            return true;
        }
        return false;
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function deleteNotification(int $notificationId): bool
    {
        return Notification::destroy($notificationId) > 0;
    }

    protected function generateTemporaryPassword(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
