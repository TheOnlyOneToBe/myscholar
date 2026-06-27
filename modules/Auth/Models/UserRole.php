<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'started_at',
        'ended_at',
        'assigned_by_user_id',
        'reason',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('ended_at')
              ->orWhere('ended_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('ended_at', '<=', now());
    }

    public function scopeTemporary($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopePermanent($query)
    {
        return $query->whereNull('ended_at');
    }

    // Methods
    public function isActive(): bool
    {
        if ($this->ended_at === null) {
            return true;
        }
        return now()->lessThan($this->ended_at);
    }

    public function isExpired(): bool
    {
        return !$this->isActive();
    }

    public function isTemporary(): bool
    {
        return $this->ended_at !== null;
    }

    public function isPermanent(): bool
    {
        return $this->ended_at === null;
    }

    public function daysUntilExpiration(): ?int
    {
        if ($this->isPermanent()) {
            return null;
        }

        return now()->diffInDays($this->ended_at, false);
    }

    public function hasExpiredSoon(int $days = 7): bool
    {
        if ($this->isPermanent()) {
            return false;
        }

        $daysLeft = $this->daysUntilExpiration();
        return $daysLeft !== null && $daysLeft <= $days && $daysLeft > 0;
    }

    public function autoRemoveIfExpired(): bool
    {
        if ($this->isExpired()) {
            $this->delete();
            return true;
        }
        return false;
    }
}
