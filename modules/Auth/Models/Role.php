<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'label',
        'description',
        'hierarchy_level',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hierarchy_level' => 'integer',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByHierarchy($query, $level)
    {
        return $query->where('hierarchy_level', $level);
    }

    public function scopeAboveLevel($query, $level)
    {
        return $query->where('hierarchy_level', '<', $level);
    }

    public function scopeBelowLevel($query, $level)
    {
        return $query->where('hierarchy_level', '>', $level);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Methods
    public function givePermission(string $permissionId): void
    {
        $permission = Permission::where('permission_id', $permissionId)->first();
        if ($permission && !$this->hasPermission($permissionId)) {
            $this->permissions()->attach($permission);
        }
    }

    public function givePermissionTo(Permission|array $permission): void
    {
        if (is_array($permission)) {
            $this->permissions()->syncWithoutDetaching(
                collect($permission)->mapWithKeys(fn($p) => [$p->id => []])->toArray()
            );
        } else {
            $this->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    public function removePermission(string $permissionId): void
    {
        $permission = Permission::where('permission_id', $permissionId)->first();
        if ($permission) {
            $this->permissions()->detach($permission);
        }
    }

    public function revokePermissionTo(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync(
            collect($permissions)->mapWithKeys(fn($p) => [$p->id => []])->toArray()
        );
    }

    public function hasPermission(string $permissionId): bool
    {
        return $this->permissions()->where('permission_id', $permissionId)->exists();
    }

    public function hasPermissionTo(Permission $permission): bool
    {
        return $this->permissions()->where('permissions.id', $permission->id)->exists();
    }

    public function getPermissionIds(): array
    {
        return $this->permissions()->pluck('permission_id')->toArray();
    }

    public function canCreateRole(Role $targetRole): bool
    {
        // Admin système (niveau 0) peut créer TOUS les rôles SAUF admin
        if ($this->hierarchy_level === 0) {
            return $targetRole->hierarchy_level !== 0;
        }

        // Les autres rôles hiérarchiques peuvent créer seulement les rôles EN DESSOUS
        // (niveau plus élevé = moins important)
        if ($this->isHierarchical()) {
            return $this->hierarchy_level < $targetRole->hierarchy_level;
        }

        // Les rôles externes (Parent, Élève) et autres ne peuvent créer personne
        return false;
    }

    public function canAssignRole(Role $targetRole): bool
    {
        // Admin système peut assigner TOUS les rôles
        if ($this->hierarchy_level === 0) {
            return true;
        }

        // Proviseur, Censeur, etc. ne peuvent assigner que les rôles EN DESSOUS
        if ($this->isHierarchical() && $targetRole->isHierarchical()) {
            return $this->hierarchy_level < $targetRole->hierarchy_level;
        }

        // Rôles non-hiérarchiques : aucun pouvoir
        return false;
    }

    public function isHierarchical(): bool
    {
        return $this->category === 'hierarchy';
    }

    public function isSystemAdmin(): bool
    {
        return $this->hierarchy_level === 0;
    }

    public function isHigherThan(Role $role): bool
    {
        return $this->hierarchy_level < $role->hierarchy_level;
    }

    public function isLowerThan(Role $role): bool
    {
        return $this->hierarchy_level > $role->hierarchy_level;
    }

    public function isSameLevel(Role $role): bool
    {
        return $this->hierarchy_level === $role->hierarchy_level;
    }
}
