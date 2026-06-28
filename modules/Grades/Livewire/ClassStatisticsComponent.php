<?php

namespace Modules\Grades\Livewire;

use Livewire\Component;
use Modules\Grades\Models\ClassAverage;
use Modules\Grades\Models\GradeAverage;
use Modules\Classes\Models\ClassModel;

class ClassStatisticsComponent extends Component
{
    public $classId;
    public $gradePeriodId;
    public $schoolYearId;

    public function mount($classId, $gradePeriodId = null, $schoolYearId = null)
    {
        $this->classId = $classId;
        $this->gradePeriodId = $gradePeriodId;
        $this->schoolYearId = $schoolYearId;
    }

    public function render()
    {
        $class = ClassModel::findOrFail($this->classId);
        
        $classAverages = ClassAverage::where('class_id', $this->classId);
        
        if ($this->gradePeriodId) {
            $classAverages->where('grade_period_id', $this->gradePeriodId);
        }
        
        if ($this->schoolYearId) {
            $classAverages->where('school_year_id', $this->schoolYearId);
        }
        
        $classAverages = $classAverages->with('subject')->get();

        $studentRankings = GradeAverage::where('grade_period_id', $this->gradePeriodId)
            ->where('school_year_id', $this->schoolYearId)
            ->with(['student', 'subject'])
            ->orderBy('average', 'desc')
            ->limit(10)
            ->get();

        $overallClassAverage = $classAverages->avg('average');

        return view('grades::livewire.class-statistics', [
            'class' => $class,
            'classAverages' => $classAverages,
            'studentRankings' => $studentRankings,
            'overallClassAverage' => $overallClassAverage,
        ]);
    }
}
