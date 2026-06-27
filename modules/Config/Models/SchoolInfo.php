<?php

namespace Modules\Config\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolInfo extends Model
{
    protected $table = 'school_info';

    protected $fillable = [
        'name',
        'acronym',
        'motto',
        'logo_path',
        'school_type',
        'address',
        'city',
        'region',
        'phone',
        'email',
        'website',
        'po_box',
        'approval_number',
        'creation_decree',
        'founder_name',
        'director_name',
        'foundation_year',
    ];

    protected function casts(): array
    {
        return [
            'foundation_year' => 'integer',
        ];
    }

    public static function current(): ?self
    {
        return static::first();
    }
}
