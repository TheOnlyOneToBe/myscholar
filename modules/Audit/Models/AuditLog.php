<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public static function getActions(): array
    {
        return ['create', 'read', 'update', 'delete', 'export', 'import', 'login', 'logout'];
    }

    public function getChangedFields(): array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];
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
