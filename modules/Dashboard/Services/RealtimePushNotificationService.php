<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Broadcasting\PendingBroadcast;

class RealtimePushNotificationService
{
    const BROADCAST_CHANNEL_PREFIX = 'student.';
    const NOTIFICATION_CACHE_PREFIX = 'notifications_';

    public function sendGradeNotification(int $studentId, string $subject, float $score, string $gradeValue): void
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                \Log::warning("Élève non trouvé pour notification de note: $studentId");
                return;
            }

        $notification = [
            'id' => uniqid('notif_'),
            'type' => 'grade',
            'title' => "Nouvelle note en $subject",
            'message' => "Vous avez reçu un $gradeValue ($score/20) en $subject",
            'icon' => 'fa-file-alt',
            'color' => $this->getGradeColor($score),
            'timestamp' => now()->toIso8601String(),
            'read' => false,
        ];

        $this->storeNotification($studentId, $notification);

        broadcast(new \Modules\Dashboard\Events\StudentNotified(
            channel: self::BROADCAST_CHANNEL_PREFIX . $student->user_id,
            notification: $notification
        ))->toOthers();
    }

    public function sendAttendanceNotification(int $studentId, string $message, string $type = 'warning'): void
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                \Log::warning("Élève non trouvé pour notification de présence: $studentId");
                return;
            }

        $notification = [
            'id' => uniqid('notif_'),
            'type' => 'attendance',
            'title' => 'Alerte de présence',
            'message' => $message,
            'icon' => 'fa-check',
            'color' => $type === 'warning' ? 'warning' : 'danger',
            'timestamp' => now()->toIso8601String(),
            'read' => false,
        ];

        $this->storeNotification($studentId, $notification);

        broadcast(new \Modules\Dashboard\Events\StudentNotified(
            channel: self::BROADCAST_CHANNEL_PREFIX . $student->user_id,
            notification: $notification
        ))->toOthers();
    }

    public function sendInvoiceNotification(int $studentId, float $amount, string $dueDate): void
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                \Log::warning("Élève non trouvé pour notification de facturation: $studentId");
                return;
            }

        $notification = [
            'id' => uniqid('notif_'),
            'type' => 'billing',
            'title' => 'Nouvelle facture',
            'message' => "Facture de {$amount} XAF à payer avant le $dueDate",
            'icon' => 'fa-money-bill-alt',
            'color' => 'danger',
            'timestamp' => now()->toIso8601String(),
            'read' => false,
        ];

        $this->storeNotification($studentId, $notification);

        broadcast(new \Modules\Dashboard\Events\StudentNotified(
            channel: self::BROADCAST_CHANNEL_PREFIX . $student->user_id,
            notification: $notification
        ))->toOthers();
    }

    public function sendExamNotification(int $studentId, string $subject, string $examDate, string $examTime): void
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                \Log::warning("Élève non trouvé pour notification d'examen: $studentId");
                return;
            }

        $notification = [
            'id' => uniqid('notif_'),
            'type' => 'exam',
            'title' => "Examen de $subject",
            'message' => "Examen le $examDate à $examTime",
            'icon' => 'fa-clock',
            'color' => 'info',
            'timestamp' => now()->toIso8601String(),
            'read' => false,
        ];

        $this->storeNotification($studentId, $notification);

        broadcast(new \Modules\Dashboard\Events\StudentNotified(
            channel: self::BROADCAST_CHANNEL_PREFIX . $student->user_id,
            notification: $notification
        ))->toOthers();
    }

    public function getNotifications(int $studentId, bool $unreadOnly = false): array
    {
        try {
            // Vérifier que l'élève existe
            if (!Student::find($studentId)) {
                \Log::warning("Élève non trouvé pour récupération de notifications: $studentId");
                return [];
            }

            $cacheKey = self::NOTIFICATION_CACHE_PREFIX . $studentId;
            $notifications = Cache::get($cacheKey, []);

        if ($unreadOnly) {
            $notifications = array_filter($notifications, fn($n) => !$n['read']);
        }

        return array_values(array_slice($notifications, -20, 20, true));
    }

    public function markAsRead(int $studentId, string $notificationId): bool
    {
        try {
            if (!Student::find($studentId)) {
                \Log::warning("Élève non trouvé pour marquer notification comme lue: $studentId");
                return false;
            }

            $cacheKey = self::NOTIFICATION_CACHE_PREFIX . $studentId;
            $notifications = Cache::get($cacheKey, []);

            foreach ($notifications as &$notification) {
                if ($notification['id'] === $notificationId) {
                    $notification['read'] = true;
                    Cache::put($cacheKey, $notifications, now()->addDays(7));
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error("Erreur lors du marquage de notification comme lue: " . $e->getMessage());
            return false;
        }
    }

    public function clearNotifications(int $studentId): void
    {
        try {
            if (!Student::find($studentId)) {
                \Log::warning("Élève non trouvé pour effacer notifications: $studentId");
                return;
            }

            $cacheKey = self::NOTIFICATION_CACHE_PREFIX . $studentId;
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'effacement des notifications: " . $e->getMessage());
        }
    }

    private function storeNotification(int $studentId, array $notification): void
    {
        $cacheKey = self::NOTIFICATION_CACHE_PREFIX . $studentId;
        $notifications = Cache::get($cacheKey, []);
        $notifications[$notification['id']] = $notification;

        Cache::put($cacheKey, $notifications, now()->addDays(7));
    }

    private function getGradeColor(float $score): string
    {
        if ($score >= 18) return 'success';
        if ($score >= 16) return 'info';
        if ($score >= 12) return 'warning';
        return 'danger';
    }
}
