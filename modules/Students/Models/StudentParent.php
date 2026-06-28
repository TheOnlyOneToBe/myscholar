<?php

namespace Modules\Students\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParent extends Model
{
    protected $table = 'student_parents';

    protected $fillable = [
        'student_id',
        'parent_user_id',
        'relationship_type',
        'is_primary_contact',
        'can_access_records',
        'can_receive_alerts',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'can_access_records' => 'boolean',
        'can_receive_alerts' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public static function isParentOfStudent(int $parentUserId, int $studentId): bool
    {
        return self::where('parent_user_id', $parentUserId)
            ->where('student_id', $studentId)
            ->exists();
    }

    public static function getChildrenFor(int $parentUserId)
    {
        return Student::whereIn('id', function ($query) use ($parentUserId) {
            $query->select('student_id')
                ->from('student_parents')
                ->where('parent_user_id', $parentUserId);
        })->get();
    }
}
