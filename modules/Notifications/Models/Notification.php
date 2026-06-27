<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_entity_type',
        'related_entity_id',
        'is_read',
        'read_at',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public static function getTypes(): array
    {
        return ['info', 'warning', 'error', 'success', 'grade_posted', 'attendance_alert', 'fee_reminder'];
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
}
