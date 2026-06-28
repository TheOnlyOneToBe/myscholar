<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Students\Models\Student;

class StudentAttendanceComponent extends Component
{
    use WithPagination;

    public $studentId;
    public $student;
    public $perPage = 25;
    public $attendanceRate = 0;
    public $showMarkModal = false;

    protected $attendanceService;
    protected $recordRepository;

    public function mount($studentId)
    {
        $this->studentId = $studentId;
        $this->student = Student::find($studentId);
        $this->attendanceService = app(AttendanceService::class);
        $this->recordRepository = app(AttendanceRecordRepository::class);

        if ($this->student) {
            $this->attendanceRate = $this->attendanceService->calculateStudentAttendanceRate($this->studentId);
        }
    }

    public function render()
    {
        $records = $this->recordRepository->findByStudent($this->studentId, $this->perPage);

        return view('attendance::livewire.student-attendance', [
            'records' => $records,
            'attendanceRate' => $this->attendanceRate,
            'isPassingRate' => $this->attendanceRate >= 80,
        ]);
    }

    public function refreshAttendanceRate()
    {
        $this->attendanceRate = $this->attendanceService->calculateStudentAttendanceRate($this->studentId);
        $this->dispatch('attendance-updated');
    }

    public function openMarkModal()
    {
        $this->showMarkModal = true;
    }

    public function closeModal()
    {
        $this->showMarkModal = false;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
