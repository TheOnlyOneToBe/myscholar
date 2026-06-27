<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeletedRecord extends Model
{
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'entity_data',
        'reason',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'entity_data' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    public function restore(array $additionalData = []): bool
    {
        $data = array_merge($this->entity_data, $additionalData);
        $modelClass = "Modules\\{$this->getModuleFromEntityType()}\\Models\\{$this->getModelFromEntityType()}";

        if (!class_exists($modelClass)) {
            return false;
        }

        $modelClass::create($data);
        $this->delete();

        return true;
    }

    private function getModuleFromEntityType(): string
    {
        $parts = explode('\\', $this->entity_type);
        return $parts[0] ?? '';
    }

    private function getModelFromEntityType(): string
    {
        $parts = explode('\\', $this->entity_type);
        return end($parts) ?: '';
    }
}
