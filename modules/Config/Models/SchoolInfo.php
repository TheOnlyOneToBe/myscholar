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

    public function getFullName(): string
    {
        $name = $this->name ?? '';
        if ($this->acronym) {
            $name .= " ({$this->acronym})";
        }
        return $name;
    }

    public function getFullAddress(): string
    {
        $parts = [];
        if ($this->address) {
            $parts[] = $this->address;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        if ($this->region) {
            $parts[] = $this->region;
        }
        return implode(', ', $parts);
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo_path);
    }

    public function getContactInfo(): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'po_box' => $this->po_box,
        ];
    }
}
