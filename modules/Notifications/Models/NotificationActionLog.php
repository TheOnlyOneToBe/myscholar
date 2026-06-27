<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationActionLog extends Model
{
    protected $table = 'notification_actions_log';

    protected $fillable = [
        'notification_id',
        'user_id',
        'action',
        'status',
        'reason',
        'response_data',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'response_data' => 'array',
        ];
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
