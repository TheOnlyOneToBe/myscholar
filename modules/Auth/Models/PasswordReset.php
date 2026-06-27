<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Scopes
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    public function scopeValid($query)
    {
        return $query->where('created_at', '>=', now()->subHour());
    }

    public function scopeExpired($query)
    {
        return $query->where('created_at', '<', now()->subHour());
    }

    // Methods
    public function isValid(): bool
    {
        return $this->created_at->greaterThan(now()->subHour());
    }

    public function isExpired(): bool
    {
        return !$this->isValid();
    }

    public function minutesUntilExpiration(): int
    {
        return $this->created_at->diffInMinutes(now()->addHour(), false);
    }

    public static function createToken(string $email): string
    {
        // Supprimer les anciens tokens
        static::byEmail($email)->delete();

        // Créer un nouveau token
        $token = hash('sha256', $email . config('app.key') . time() . random_bytes(16));

        static::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        return $token;
    }

    public static function findValidToken(string $email, string $token): ?self
    {
        return static::byEmail($email)
            ->byToken($token)
            ->valid()
            ->first();
    }
}
