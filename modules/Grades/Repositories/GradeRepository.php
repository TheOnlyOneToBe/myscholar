<?php

namespace Modules\Grades\Repositories;

use Modules\Grades\Models\Grade;
use Illuminate\Pagination\Paginator;

class GradeRepository
{
    public function all(array $filters = [], $perPage = 25)
    {
        $query = Grade::query();

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['grade_period_id'])) {
            $query->where('grade_period_id', $filters['grade_period_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['grade_type'])) {
            $query->where('grade_type', $filters['grade_type']);
        }

        if (!empty($filters['school_year_id'])) {
            $query->where('school_year_id', $filters['school_year_id']);
        }

        return $query->with(['student', 'subject', 'teacher', 'gradePeriod'])
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return Grade::with(['student', 'subject', 'teacher', 'gradePeriod'])->findOrFail($id);
    }

    public function create(array $data): Grade
    {
        return Grade::create($data);
    }

    public function update($id, array $data): bool
    {
        return Grade::findOrFail($id)->update($data);
    }

    public function delete($id): bool
    {
        return Grade::findOrFail($id)->delete();
    }

    public function getStudentGrades($studentId, $gradePeriodId = null, $schoolYearId = null)
    {
        $query = Grade::where('student_id', $studentId);

        if ($gradePeriodId) {
            $query->where('grade_period_id', $gradePeriodId);
        }

        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }

        return $query->with(['subject', 'teacher', 'gradePeriod'])->get();
    }

    public function getBySubjectAndPeriod($subjectId, $gradePeriodId, $schoolYearId)
    {
        return Grade::where('subject_id', $subjectId)
            ->where('grade_period_id', $gradePeriodId)
            ->where('school_year_id', $schoolYearId)
            ->with(['student', 'teacher'])
            ->get();
    }
}
