<?php

namespace Tests\Feature\Notifications;

use Modules\Auth\Models\User;
use Modules\Notifications\Models\Notification;
use Modules\Notifications\Services\NotificationService;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
    }

    public function test_can_create_basic_notification()
    {
        $user = User::factory()->create();

        $notification = $this->notificationService->notify(
            $user,
            'Test Notification',
            'This is a test notification',
            'system',
            'normal'
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'type' => 'system',
            'priority' => 'normal',
        ]);
    }

    public function test_can_create_notification_with_action()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $notification = $this->notificationService->notifyWithAction(
            $admin,
            'Password Reset Request',
            'User requested password reset',
            ['approve' => 'Approve', 'reject' => 'Reject'],
            'approval',
            'high',
            'admin.password.approve',
            ['user_id' => $user->id]
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type' => 'approval',
            'action_status' => 'pending',
        ]);

        $this->assertNotNull($notification->actions);
        $this->assertNotNull($notification->action_target_route);
    }

    public function test_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $notification = $this->notificationService->notify(
            $user,
            'Test',
            'Test message'
        );

        $notification->markAsRead();

        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);
    }

    public function test_can_mark_notification_as_unread()
    {
        $user = User::factory()->create();
        $notification = $this->notificationService->notify(
            $user,
            'Test',
            'Test message'
        );

        $notification->markAsRead();
        $notification->markAsUnread();

        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);
    }

    public function test_can_get_unread_count()
    {
        $user = User::factory()->create();

        $this->notificationService->notify($user, 'Test 1', 'Message 1');
        $this->notificationService->notify($user, 'Test 2', 'Message 2');
        $this->notificationService->notify($user, 'Test 3', 'Message 3');

        $count = $this->notificationService->getUnreadCount($user->id);
        $this->assertEquals(3, $count);

        $this->notificationService->markAsRead(1);
        $count = $this->notificationService->getUnreadCount($user->id);
        $this->assertEquals(2, $count);
    }

    public function test_can_approve_action()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $notification = $this->notificationService->notifyWithAction(
            $admin,
            'Password Reset Request',
            'User requested password reset',
            ['approve' => 'Approve'],
            'approval',
            'high',
            'admin.password.approve',
            ['user_id' => $user->id],
            ['action_type' => 'password_reset']
        );

        $this->assertEquals('pending', $notification->action_status);

        $notification->approveAction($admin->id);

        $this->assertEquals('approved', $notification->action_status);
        $this->assertEquals($admin->id, $notification->actioned_by_user_id);

        $this->assertDatabaseHas('notification_actions_log', [
            'notification_id' => $notification->id,
            'user_id' => $admin->id,
            'status' => 'approved',
        ]);
    }

    public function test_can_reject_action()
    {
        $admin = User::factory()->create();

        $notification = $this->notificationService->notifyWithAction(
            $admin,
            'Test Action',
            'Test action message',
            ['approve' => 'Approve'],
            'approval'
        );

        $notification->rejectAction($admin->id, 'Rejected for testing');

        $this->assertEquals('rejected', $notification->action_status);

        $this->assertDatabaseHas('notification_actions_log', [
            'notification_id' => $notification->id,
            'status' => 'rejected',
            'reason' => 'Rejected for testing',
        ]);
    }

    public function test_can_get_user_notifications()
    {
        $user = User::factory()->create();

        $this->notificationService->notify($user, 'Test 1', 'Message 1');
        $this->notificationService->notify($user, 'Test 2', 'Message 2');

        $notifications = $this->notificationService->getUserNotifications($user->id);

        $this->assertEquals(2, $notifications->count());
    }

    public function test_can_get_pending_actions()
    {
        $admin = User::factory()->create();

        $this->notificationService->notifyWithAction(
            $admin,
            'Test 1',
            'Message 1',
            ['approve' => 'Approve'],
            'approval'
        );

        $this->notificationService->notifyWithAction(
            $admin,
            'Test 2',
            'Message 2',
            ['approve' => 'Approve'],
            'approval'
        );

        $pending = $this->notificationService->getPendingActions($admin->id);

        $this->assertEquals(2, $pending->count());
    }

    public function test_can_delete_notification()
    {
        $user = User::factory()->create();
        $notification = $this->notificationService->notify($user, 'Test', 'Message');

        $result = $this->notificationService->deleteNotification($notification->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_can_mark_all_as_read()
    {
        $user = User::factory()->create();

        $this->notificationService->notify($user, 'Test 1', 'Message 1');
        $this->notificationService->notify($user, 'Test 2', 'Message 2');
        $this->notificationService->notify($user, 'Test 3', 'Message 3');

        $this->notificationService->markAllAsRead($user->id);

        $unreadCount = $this->notificationService->getUnreadCount($user->id);
        $this->assertEquals(0, $unreadCount);
    }
}
