<?php

namespace Modules\Grades\Services;

use Modules\Grades\Repositories\GradeRepository;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAverage;
use Modules\Grades\Models\ClassAverage;
use Modules\Students\Models\Student;

class GradeService
{
    public function __construct(protected GradeRepository $repository)
    {
    }

    public function createGrade(array $data): Grade
    {
        $grade = $this->repository->create($data);
        $grade->graded_at = now();
        $grade->save();
        
        return $grade;
    }

    public function updateGrade($id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteGrade($id): bool
    {
        return $this->repository->delete($id);
    }

    public function getStudentAverages($studentId, $gradePeriodId, $schoolYearId)
    {
        return GradeAverage::where('student_id', $studentId)
            ->where('grade_period_id', $gradePeriodId)
            ->where('school_year_id', $schoolYearId)
            ->with(['subject'])
            ->get();
    }

    public function calculateStudentOverallAverage($studentId, $gradePeriodId, $schoolYearId)
    {
        $averages = $this->getStudentAverages($studentId, $gradePeriodId, $schoolYearId);
        
        if ($averages->isEmpty()) {
            return 0;
        }

        $totalCoefficient = 0;
        $weightedSum = 0;

        foreach ($averages as $average) {
            $coefficient = $average->subject->coefficient ?? 1;
            $weightedSum += $average->average * $coefficient;
            $totalCoefficient += $coefficient;
        }

        return $totalCoefficient > 0 ? round($weightedSum / $totalCoefficient, 2) : 0;
    }

    public function calculateClassAverage($classId, $subjectId, $gradePeriodId, $schoolYearId)
    {
        $grades = Grade::whereHas('student', function ($query) use ($classId) {
            $query->where('current_class_id', $classId);
        })
            ->where('subject_id', $subjectId)
            ->where('grade_period_id', $gradePeriodId)
            ->where('school_year_id', $schoolYearId)
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedSum = 0;
        $passed = 0;
        $highest = null;
        $lowest = null;

        foreach ($grades as $grade) {
            $weightedSum += $grade->score * $grade->weight;
            $totalWeight += $grade->weight;

            if ($grade->score >= 10) {
                $passed++;
            }

            if ($highest === null || $grade->score > $highest) {
                $highest = $grade->score;
            }

            if ($lowest === null || $grade->score < $lowest) {
                $lowest = $grade->score;
            }
        }

        $average = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        $passRate = round(($passed / count($grades)) * 100, 2);

        return ClassAverage::updateOrCreate(
            [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'grade_period_id' => $gradePeriodId,
                'school_year_id' => $schoolYearId,
            ],
            [
                'average' => $average,
                'highest_score' => $highest,
                'lowest_score' => $lowest,
                'pass_rate' => $passRate,
            ]
        );
    }

    public function updateStudentRanking($subjectId, $gradePeriodId, $schoolYearId)
    {
        $averages = GradeAverage::where('subject_id', $subjectId)
            ->where('grade_period_id', $gradePeriodId)
            ->where('school_year_id', $schoolYearId)
            ->orderByDesc('average')
            ->get();

        $rank = 1;
        foreach ($averages as $average) {
            $average->update(['rank' => $rank]);
            $rank++;
        }
    }
}
