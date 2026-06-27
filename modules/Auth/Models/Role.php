<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'priority',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function givePermission(string $permissionId): void
    {
        $permission = Permission::where('permission_id', $permissionId)->first();
        if ($permission && !$this->hasPermission($permissionId)) {
            $this->permissions()->attach($permission);
        }
    }

    public function hasPermission(string $permissionId): bool
    {
        return $this->permissions()->where('permission_id', $permissionId)->exists();
    }

    public function getPermissionIds(): array
    {
        return $this->permissions()->pluck('permission_id')->toArray();
    }
}
