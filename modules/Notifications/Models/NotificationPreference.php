<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'sms_enabled',
        'in_app_enabled',
        'push_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'push_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public function getEnabledChannels(): array
    {
        $channels = [];
        if ($this->email_enabled) {
            $channels[] = 'email';
        }
        if ($this->sms_enabled) {
            $channels[] = 'sms';
        }
        if ($this->in_app_enabled) {
            $channels[] = 'in_app';
        }
        if ($this->push_enabled) {
            $channels[] = 'push';
        }
        return $channels;
    }

    public function hasChannel(string $channel): bool
    {
        return in_array($channel, $this->getEnabledChannels());
    }
}
