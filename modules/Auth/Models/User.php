<?php

namespace Modules\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasPermissions;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'profile_picture',
        'is_active',
        'last_password_change',
        'failed_login_attempts',
        'account_locked_until',
        'two_factor_enabled',
        'two_factor_secret',
        'ip_whitelist',
        'password_history',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'password_history',
        'ip_whitelist',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'last_password_change' => 'datetime',
            'account_locked_until' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'ip_whitelist' => 'array',
            'password_history' => 'array',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    public function giveRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role);
        }
    }

    public function getPermissions(): array
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn($role) => $role->permissions)
            ->pluck('permission_id')
            ->unique()
            ->values()
            ->toArray();
    }

    public function hasPermission(string $permissionId): bool
    {
        return in_array($permissionId, $this->getPermissions());
    }

    public function hasAnyPermission(array $permissionIds): bool
    {
        $userPermissions = $this->getPermissions();
        return count(array_intersect($permissionIds, $userPermissions)) > 0;
    }

    public function isAccountLocked(): bool
    {
        if (!$this->account_locked_until) {
            return false;
        }
        return now()->lessThan($this->account_locked_until);
    }

    public function lockAccount(int $minutesToLock = 30): void
    {
        $this->update([
            'account_locked_until' => now()->addMinutes($minutesToLock),
        ]);
    }

    public function unlockAccount(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'account_locked_until' => null,
        ]);
    }

    public function incrementFailedLoginAttempts(): void
    {
        $attempts = ($this->failed_login_attempts ?? 0) + 1;
        $maxAttempts = 5;

        if ($attempts >= $maxAttempts) {
            $this->lockAccount();
        }

        $this->update(['failed_login_attempts' => $attempts]);
    }

    public function resetFailedLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_login' => now(),
        ]);
    }

    public function addToPasswordHistory(string $passwordHash): void
    {
        $history = $this->password_history ?? [];
        $history[] = $passwordHash;
        $maxHistorySize = 5;
        if (count($history) > $maxHistorySize) {
            array_shift($history);
        }
        $this->update(['password_history' => $history]);
    }

    public function passwordInHistory(string $plainPassword): bool
    {
        $history = $this->password_history ?? [];
        foreach ($history as $hash) {
            if (\Hash::check($plainPassword, $hash)) {
                return true;
            }
        }
        return false;
    }

    public function hasIpWhitelist(): bool
    {
        return !empty($this->ip_whitelist) && is_array($this->ip_whitelist);
    }

    public function isIpWhitelisted(string $ip): bool
    {
        if (!$this->hasIpWhitelist()) {
            return true;
        }
        return in_array($ip, $this->ip_whitelist);
    }

    public function addIpToWhitelist(string $ip): void
    {
        $whitelist = $this->ip_whitelist ?? [];
        if (!in_array($ip, $whitelist)) {
            $whitelist[] = $ip;
            $this->update(['ip_whitelist' => $whitelist]);
        }
    }

    public function removeIpFromWhitelist(string $ip): void
    {
        $whitelist = $this->ip_whitelist ?? [];
        $whitelist = array_diff($whitelist, [$ip]);
        $this->update(['ip_whitelist' => array_values($whitelist)]);
    }

    public function clearIpWhitelist(): void
    {
        $this->update(['ip_whitelist' => null]);
    }
}

