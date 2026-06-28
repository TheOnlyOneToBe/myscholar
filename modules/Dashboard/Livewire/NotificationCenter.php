<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Dashboard\Services\RealtimePushNotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationCenter extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showNotifications = false;

    protected $listeners = ['studentNotified' => 'onStudentNotified'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $student = Auth::user()->student;
        if ($student) {
            $notificationService = app(RealtimePushNotificationService::class);
            $this->notifications = $notificationService->getNotifications($student->id);
            $this->unreadCount = count(array_filter($this->notifications, fn($n) => !$n['read']));
        }
    }

    public function onStudentNotified($notification)
    {
        $this->notifications = array_merge([$notification], $this->notifications);
        $this->unreadCount++;

        if (count($this->notifications) > 20) {
            $this->notifications = array_slice($this->notifications, 0, 20);
        }

        $this->dispatch('notificationReceived', notification: $notification);
    }

    public function markAsRead(string $notificationId)
    {
        $student = Auth::user()->student;
        if ($student) {
            $notificationService = app(RealtimePushNotificationService::class);
            $notificationService->markAsRead($student->id, $notificationId);

            foreach ($this->notifications as &$notification) {
                if ($notification['id'] === $notificationId) {
                    $notification['read'] = true;
                }
            }

            $this->unreadCount = max(0, $this->unreadCount - 1);
        }
    }

    public function clearAllNotifications()
    {
        $student = Auth::user()->student;
        if ($student) {
            $notificationService = app(RealtimePushNotificationService::class);
            $notificationService->clearNotifications($student->id);
            $this->notifications = [];
            $this->unreadCount = 0;
        }
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function render()
    {
        return view('livewire.notification-center');
    }
}
