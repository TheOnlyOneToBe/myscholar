<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'coefficient',
        'is_mandatory',
        'filiere',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'coefficient' => 'float',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    public static function byFiliere(string $filiere)
    {
        return static::where('filiere', $filiere)->orWhereNull('filiere');
    }
}
