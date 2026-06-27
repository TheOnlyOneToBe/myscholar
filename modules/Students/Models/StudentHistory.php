<?php

namespace Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHistory extends Model
{
    protected $table = 'student_history';

    protected $fillable = [
        'student_id',
        'school_year',
        'class',
        'filiere',
        'average_grade',
        'ranking',
        'mention',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'average_grade' => 'float',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public static function mentions(): array
    {
        return ['Excellent', 'Très bien', 'Bien', 'Assez bien', 'Passable', 'Faible'];
    }
}
