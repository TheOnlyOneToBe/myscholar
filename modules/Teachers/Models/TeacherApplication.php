<?php

namespace Modules\Teachers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;

class TeacherApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_code',
        'specialization',
        'qualification_level',
        'hire_date',
        'filiere',
        'office_location',
        'years_of_experience',
        'bio',
        'phone_office',
        'email_office',
        'subjects_data',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'approved_at' => 'datetime',
        'subjects_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        // Create Teacher record from application
        Teacher::create([
            'user_id' => $this->user_id,
            'teacher_code' => $this->teacher_code,
            'specialization' => $this->specialization,
            'qualification_level' => $this->qualification_level,
            'hire_date' => $this->hire_date,
            'filiere' => $this->filiere,
            'office_location' => $this->office_location,
            'years_of_experience' => $this->years_of_experience,
            'bio' => $this->bio,
            'phone_office' => $this->phone_office,
            'email_office' => $this->email_office,
            'is_active' => true,
        ]);

        // Attach subjects to teacher
        if ($this->subjects_data) {
            $teacher = Teacher::where('user_id', $this->user_id)->first();
            foreach ($this->subjects_data as $subject) {
                $teacher->subjects()->attach($subject['subject_id'], [
                    'proficiency_level' => $subject['proficiency_level'] ?? 3,
                    'since_year' => $subject['since_year'],
                    'is_primary' => $subject['is_primary'] ?? false,
                ]);
            }
        }
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
