<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'priority',
        'related_entity_type',
        'related_entity_id',
        'data',
        'is_read',
        'read_at',
        'actions',
        'action_target_route',
        'action_parameters',
        'action_status',
        'actioned_by_user_id',
        'actioned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'data' => 'array',
            'actions' => 'array',
            'action_parameters' => 'array',
            'actioned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public function actionedByUser(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'actioned_by_user_id');
    }

    public function actionsLog(): HasMany
    {
        return $this->hasMany(NotificationActionLog::class, 'notification_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function isRead(): bool
    {
        return $this->is_read === true;
    }

    public function hasActions(): bool
    {
        return !empty($this->actions);
    }

    public function approveAction(int $userId, ?string $reason = null, ?array $responseData = null): void
    {
        $this->update([
            'action_status' => 'approved',
            'actioned_by_user_id' => $userId,
            'actioned_at' => now(),
        ]);

        NotificationActionLog::create([
            'notification_id' => $this->id,
            'user_id' => $userId,
            'action' => 'approved',
            'status' => 'approved',
            'reason' => $reason,
            'response_data' => $responseData,
            'ip_address' => request()->ip(),
        ]);
    }

    public function rejectAction(int $userId, string $reason = ''): void
    {
        $this->update([
            'action_status' => 'rejected',
            'actioned_by_user_id' => $userId,
            'actioned_at' => now(),
        ]);

        NotificationActionLog::create([
            'notification_id' => $this->id,
            'user_id' => $userId,
            'action' => 'rejected',
            'status' => 'rejected',
            'reason' => $reason,
            'ip_address' => request()->ip(),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePending($query)
    {
        return $query->where('action_status', 'pending');
    }

    public static function getTypes(): array
    {
        return ['academic', 'financial', 'attendance', 'system', 'security', 'approval'];
    }

    public static function getPriorities(): array
    {
        return ['low', 'normal', 'high', 'critical'];
    }
}
