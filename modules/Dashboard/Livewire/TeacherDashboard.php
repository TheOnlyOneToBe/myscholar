<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Teachers\Models\Teacher;
use Modules\Classes\Models\SchoolClass;
use Modules\Grades\Models\Grade;

class TeacherDashboard extends Component
{
    public ?Teacher $teacher = null;
    public int $classesCount = 0;
    public int $studentsCount = 0;
    public float $averageClassGrade = 0;
    public array $upcomingClasses = [];

    public function mount()
    {
        $user = auth()->user();

        $this->teacher = Teacher::where('user_id', $user->id)->first();

        if ($this->teacher) {
            $this->classesCount = $this->teacher->classes()->count();
            $this->studentsCount = $this->teacher->classes()
                ->withCount('students')
                ->get()
                ->sum('students_count');

            $this->calculateAverageGrade();
            $this->loadUpcomingClasses();
        }
    }

    private function calculateAverageGrade(): void
    {
        if (!$this->teacher) {
            return;
        }

        $classIds = $this->teacher->classes()->pluck('classes.id');

        $average = Grade::whereIn('class_id', $classIds)
            ->where('status', 'final')
            ->avg('score');

        $this->averageClassGrade = round($average ?? 0, 2);
    }

    private function loadUpcomingClasses(): void
    {
        if (!$this->teacher) {
            return;
        }

        $this->upcomingClasses = $this->teacher->classes()
            ->where('status', 'active')
            ->take(5)
            ->get()
            ->map(fn($class) => [
                'id' => $class->id,
                'name' => $class->name,
                'level' => $class->level,
                'students_count' => $class->students_count ?? 0,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('dashboard::livewire.teacher-dashboard', [
            'teacher' => $this->teacher,
            'classesCount' => $this->classesCount,
            'studentsCount' => $this->studentsCount,
            'averageClassGrade' => $this->averageClassGrade,
            'upcomingClasses' => $this->upcomingClasses,
        ]);
    }
}
