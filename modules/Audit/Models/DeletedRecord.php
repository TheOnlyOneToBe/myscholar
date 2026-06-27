<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

class DeletedRecord extends Model
{
    protected $fillable = [
        'model_class',
        'table_name',
        'record_id',
        'data',
        'deleted_by_user_id',
        'deletion_reason',
        'notes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }

    // Scopes
    public function scopeByModel($query, $modelClass)
    {
        return $query->where('model_class', $modelClass);
    }

    public function scopeByTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('deleted_by_user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Restore the deleted record
     */
    public function restore(array $additionalData = []): bool
    {
        try {
            if (!class_exists($this->model_class)) {
                return false;
            }

            $modelClass = $this->model_class;
            $data = array_merge($this->data, $additionalData);

            // Recreate the record
            $modelClass::create($data);

            // Archive the deletion record
            $this->update(['notes' => 'Restored at ' . now()]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to restore deleted record', [
                'model' => $this->model_class,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get summary of deleted records by model
     */
    public static function getSummary(int $days = 30): array
    {
        $deletions = static::recent($days)->get();

        return [
            'total_deleted' => $deletions->count(),
            'by_model' => $deletions->groupBy('model_class')->map->count(),
            'by_user' => $deletions->groupBy('deleted_by_user_id')->map->count(),
        ];
    }
}
