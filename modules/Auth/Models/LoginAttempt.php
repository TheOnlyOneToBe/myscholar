<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    protected $table = 'login_attempts';

    protected $fillable = [
        'user_id',
        'email_or_username',
        'ip_address',
        'user_agent',
        'success',
        'reason',
        'attempted_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Scopes
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeByIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeRecent($query, int $minutes = 15)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attempted_at', today());
    }

    // Methods
    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailed(): bool
    {
        return !$this->success;
    }

    public static function getFailureCountForUser(User $user, int $minutes = 15): int
    {
        return static::byUser($user)
            ->failed()
            ->recent($minutes)
            ->count();
    }

    public static function getFailureCountForIp(string $ip, int $minutes = 15): int
    {
        return static::byIp($ip)
            ->failed()
            ->recent($minutes)
            ->count();
    }
}
