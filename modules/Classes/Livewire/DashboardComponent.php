<?php

namespace Modules\Classes\Livewire;

use Livewire\Component;
use Modules\Classes\Models\ClassModel;
use Modules\Config\Models\SchoolYear;

class DashboardComponent extends Component
{
    public $schoolYearId;
    public $stats = [];

    public function mount()
    {
        $this->schoolYearId = SchoolYear::active()?->id ?? SchoolYear::latest('id')->first()?->id;
        $this->loadStats();
    }

    public function loadStats()
    {
        $query = ClassModel::query();

        if ($this->schoolYearId) {
            $query->where('school_year_id', $this->schoolYearId);
        }

        $allClasses = $query->get();

        $this->stats = [
            'total_classes' => $allClasses->count(),
            'total_capacity' => $allClasses->sum('capacity'),
            'total_students' => $allClasses->sum('current_students'),
            'by_level' => $allClasses->groupBy('level')->map->count()->toArray(),
            'by_filiere' => $allClasses->groupBy('filiere')->map->count()->toArray(),
            'occupancy_rate' => $allClasses->sum('capacity') > 0 
                ? round(($allClasses->sum('current_students') / $allClasses->sum('capacity')) * 100, 2)
                : 0,
            'avg_capacity' => $allClasses->count() > 0 
                ? round($allClasses->avg('current_students'), 2)
                : 0,
        ];
    }

    public function updatedSchoolYearId()
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('classes::livewire.dashboard', [
            'stats' => $this->stats,
            'schoolYears' => SchoolYear::all(),
        ]);
    }
}
