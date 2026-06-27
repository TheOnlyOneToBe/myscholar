<?php

namespace Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentContact extends Model
{
    protected $fillable = [
        'student_id',
        'contact_type',
        'full_name',
        'relationship',
        'profession',
        'phone',
        'email',
        'address',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public static function contactTypes(): array
    {
        return ['father', 'mother', 'guardian'];
    }
}
