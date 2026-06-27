<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

class AuditLog extends Model
{
    protected $fillable = [
        'action',
        'entity_type',
        'entity_id',
        'user_id',
        'changes',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'http_status',
        'error_message',
        'stack_trace',
        'severity',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes pour le monitoring et filtering
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByEntity($query, $entityType, $entityId = null)
    {
        return $query->where('entity_type', $entityType)
            ->when($entityId, fn($q) => $q->where('entity_id', $entityId));
    }

    public function scopeByEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeErrors($query)
    {
        return $query->whereIn('severity', ['error', 'critical']);
    }

    public function scopeByRoute($query, $url)
    {
        return $query->where('url', 'like', "%{$url}%");
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeHttpErrors($query)
    {
        return $query->whereNotNull('http_status')
            ->where('http_status', '>=', 400);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('http_status', $status);
    }

    // Methods
    public function isError(): bool
    {
        return in_array($this->severity, ['error', 'critical']);
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isHttpError(): bool
    {
        return $this->http_status && $this->http_status >= 400;
    }

    public static function getActions(): array
    {
        return [
            'create', 'read', 'update', 'delete', 'export', 'import',
            'login', 'logout', 'auth_failed', 'permission_denied',
            'error', 'crash', 'system_event'
        ];
    }

    public static function getSeverityLevels(): array
    {
        return ['info', 'warning', 'error', 'critical'];
    }

    public function getChangedFields(): array
    {
        if (!$this->changes) {
            return [];
        }

        $changes = $this->changes;
        $old = $changes['old_values'] ?? [];
        $new = $changes['new_values'] ?? [];
        $changed = [];

        foreach (array_keys(array_merge($old, $new)) as $field) {
            if (($old[$field] ?? null) !== ($new[$field] ?? null)) {
                $changed[$field] = [
                    'old' => $old[$field] ?? null,
                    'new' => $new[$field] ?? null,
                ];
            }
        }

        return $changed;
    }
}
