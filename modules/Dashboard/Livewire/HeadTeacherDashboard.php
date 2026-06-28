<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Teachers\Models\Teacher;
use Modules\Classes\Models\SchoolClass;
use Modules\Students\Models\Student;
use Modules\Attendance\Models\AttendanceRecord;

class HeadTeacherDashboard extends Component
{
    public ?Teacher $teacher = null;
    public ?SchoolClass $mainClass = null;
    public int $totalStudents = 0;
    public float $averageAttendance = 0;
    public int $pendingJustifications = 0;
    public array $attendanceStats = [];
    public array $classGrades = [];

    public function mount()
    {
        $user = auth()->user();

        $this->teacher = Teacher::where('user_id', $user->id)->first();

        if ($this->teacher) {
            $this->loadMainClass();
            $this->calculateStats();
        }
    }

    private function loadMainClass(): void
    {
        if (!$this->teacher) {
            return;
        }

        $this->mainClass = SchoolClass::whereHas('headTeacher', fn($q) =>
            $q->where('teacher_id', $this->teacher->id)
        )->first();

        if ($this->mainClass) {
            $this->totalStudents = $this->mainClass->students()->count();
        }
    }

    private function calculateStats(): void
    {
        if (!$this->mainClass) {
            return;
        }

        $this->calculateAverageAttendance();
        $this->countPendingJustifications();
        $this->loadClassGrades();
    }

    private function calculateAverageAttendance(): void
    {
        $studentIds = $this->mainClass->students()->pluck('students.id');

        $records = AttendanceRecord::whereIn('student_id', $studentIds)
            ->where('status', 'present')
            ->count();

        $total = AttendanceRecord::whereIn('student_id', $studentIds)->count();

        $this->averageAttendance = $total > 0 ? round(($records / $total) * 100, 2) : 0;
    }

    private function countPendingJustifications(): void
    {
        $studentIds = $this->mainClass->students()->pluck('students.id');

        $this->pendingJustifications = AttendanceRecord::whereIn('student_id', $studentIds)
            ->where('status', 'absent')
            ->whereNull('justification_id')
            ->count();
    }

    private function loadClassGrades(): void
    {
        $this->classGrades = [
            'average' => 0,
            'highest' => 0,
            'lowest' => 0,
        ];
    }

    public function render()
    {
        return view('dashboard::livewire.head-teacher-dashboard', [
            'teacher' => $this->teacher,
            'mainClass' => $this->mainClass,
            'totalStudents' => $this->totalStudents,
            'averageAttendance' => $this->averageAttendance,
            'pendingJustifications' => $this->pendingJustifications,
            'classGrades' => $this->classGrades,
        ]);
    }
}
