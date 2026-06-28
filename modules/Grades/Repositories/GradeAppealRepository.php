<?php

namespace Modules\Grades\Repositories;

use Modules\Grades\Models\GradeAppeal;

class GradeAppealRepository
{
    public function all(array $filters = [], $perPage = 25)
    {
        $query = GradeAppeal::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        return $query->with(['student', 'grade', 'subject', 'reviewer'])
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return GradeAppeal::with(['student', 'grade', 'subject', 'reviewer'])->findOrFail($id);
    }

    public function create(array $data): GradeAppeal
    {
        return GradeAppeal::create($data);
    }

    public function update($id, array $data): bool
    {
        return GradeAppeal::findOrFail($id)->update($data);
    }

    public function delete($id): bool
    {
        return GradeAppeal::findOrFail($id)->delete();
    }

    public function getStudentAppeals($studentId, $status = null)
    {
        $query = GradeAppeal::where('student_id', $studentId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->with(['grade', 'subject', 'reviewer'])->get();
    }

    public function getPendingAppeals($perPage = 25)
    {
        return GradeAppeal::pending()
            ->with(['student', 'grade', 'subject'])
            ->paginate($perPage);
    }
}
