<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'permission_id',
        'name',
        'description',
        'module',
    ];

    public $timestamps = true;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public static function findByPermissionId(string $permissionId): ?self
    {
        return static::where('permission_id', $permissionId)->first();
    }

    public static function getByModule(string $module): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('module', $module)->get();
    }
}
