<?php

namespace Modules\Grades\Livewire;

use Livewire\Component;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAverage;
use Modules\Grades\Services\GradeService;
use Modules\Students\Models\Student;

class StudentGradesComponent extends Component
{
    public $studentId;
    public $gradePeriodId;
    public $schoolYearId;

    public function mount($studentId, $gradePeriodId = null, $schoolYearId = null)
    {
        $this->studentId = $studentId;
        $this->gradePeriodId = $gradePeriodId;
        $this->schoolYearId = $schoolYearId;
    }

    public function render()
    {
        $gradeService = app(GradeService::class);
        
        $student = Student::findOrFail($this->studentId);
        
        $grades = Grade::where('student_id', $this->studentId);
        
        if ($this->gradePeriodId) {
            $grades->where('grade_period_id', $this->gradePeriodId);
        }
        
        if ($this->schoolYearId) {
            $grades->where('school_year_id', $this->schoolYearId);
        }
        
        $grades = $grades->with(['subject', 'teacher', 'gradePeriod'])
            ->orderBy('subject_id')
            ->get();

        $averages = GradeAverage::where('student_id', $this->studentId);
        
        if ($this->gradePeriodId) {
            $averages->where('grade_period_id', $this->gradePeriodId);
        }
        
        if ($this->schoolYearId) {
            $averages->where('school_year_id', $this->schoolYearId);
        }
        
        $averages = $averages->with('subject')->get();
        
        $overallAverage = $gradeService->calculateStudentOverallAverage(
            $this->studentId,
            $this->gradePeriodId,
            $this->schoolYearId
        );

        return view('grades::livewire.student-grades', [
            'student' => $student,
            'grades' => $grades,
            'averages' => $averages,
            'overallAverage' => $overallAverage,
        ]);
    }
}
