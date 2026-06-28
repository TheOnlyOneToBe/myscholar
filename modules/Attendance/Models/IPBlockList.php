<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPBlockList extends Model
{
    use HasFactory;

    protected $table = 'attendance_ip_blocklist';

    protected $fillable = [
        'ip_address',
        'reason',
        'is_active',
        'blocked_at',
        'unblock_at',
        'blocked_by_user_id',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'blocked_at' => 'datetime',
        'unblock_at' => 'datetime',
    ];

    /**
     * Check if an IP is currently blocked
     */
    public static function isBlocked(string $ipAddress): bool
    {
        return self::where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('unblock_at')
                    ->orWhere('unblock_at', '>', now());
            })
            ->exists();
    }

    /**
     * Get block reason for an IP
     */
    public static function getBlockReason(string $ipAddress): ?string
    {
        return self::where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('unblock_at')
                    ->orWhere('unblock_at', '>', now());
            })
            ->value('reason');
    }

    /**
     * Block an IP address
     */
    public static function block(
        string $ipAddress,
        string $reason,
        ?int $blockedByUserId = null,
        ?\DateTime $unblockAt = null,
        ?string $notes = null
    ): self {
        return self::updateOrCreate(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'is_active' => true,
                'blocked_at' => now(),
                'unblock_at' => $unblockAt,
                'blocked_by_user_id' => $blockedByUserId,
                'notes' => $notes,
            ]
        );
    }

    /**
     * Unblock an IP address
     */
    public static function unblock(string $ipAddress): bool
    {
        return self::where('ip_address', $ipAddress)
            ->update(['is_active' => false]);
    }

    /**
     * Get all active blocks
     */
    public static function active()
    {
        return self::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('unblock_at')
                    ->orWhere('unblock_at', '>', now());
            });
    }
}
