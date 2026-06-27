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
        'category',
        'scope',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    // Methods
    public static function findByPermissionId(string $permissionId): ?self
    {
        return static::where('permission_id', $permissionId)->first();
    }

    public static function getByModule(string $module): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('module', $module)->where('is_active', true)->get();
    }

    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)->where('is_active', true)->get();
    }

    public static function getByScope(string $scope): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('scope', $scope)->where('is_active', true)->get();
    }

    public function isGlobal(): bool
    {
        return $this->scope === 'global';
    }

    public function isByClass(): bool
    {
        return $this->scope === 'by_class';
    }

    public function isBySubject(): bool
    {
        return $this->scope === 'by_subject';
    }

    public function isByStudent(): bool
    {
        return $this->scope === 'by_student';
    }
}
