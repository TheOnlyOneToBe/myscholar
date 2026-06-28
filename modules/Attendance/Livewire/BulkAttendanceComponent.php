<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Classes\Models\Classes;
use Modules\Students\Models\Student;
use Illuminate\Support\Facades\Http;

class BulkAttendanceComponent extends Component
{
    public $sessionId = null;
    public $session = null;
    public $class = null;
    public $students = [];
    public $attendance = [];
    public $defaultStatus = 'present';
    public $showConfirmation = false;
    public $bulkResult = null;
    public $isLoading = false;
    public $selectedCount = 0;

    public function mount($sessionId = null)
    {
        if ($sessionId) {
            $this->loadSession($sessionId);
        }
    }

    public function loadSession($sessionId)
    {
        $this->session = AttendanceSession::find($sessionId);
        if (!$this->session) {
            session()->flash('error', 'Session not found');
            return;
        }

        $this->sessionId = $sessionId;
        $this->class = $this->session->class;

        // Load all students from the class
        $this->students = Student::where('class_id', $this->class->id)
            ->orderBy('last_name')
            ->get()
            ->toArray();

        // Initialize attendance array
        $this->initializeAttendance();
    }

    protected function initializeAttendance()
    {
        $this->attendance = [];
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = [
                'status' => $this->defaultStatus,
                'notes' => '',
            ];
        }
    }

    public function render()
    {
        return view('attendance::livewire.bulk-attendance', [
            'session' => $this->session,
            'class' => $this->class,
            'students' => $this->students,
            'attendance' => $this->attendance,
            'selectedCount' => $this->selectedCount,
        ]);
    }

    public function updatedDefaultStatus()
    {
        // Apply default status to all students
        foreach ($this->attendance as $studentId => &$record) {
            $record['status'] = $this->defaultStatus;
        }
    }

    public function setStatus($studentId, $status)
    {
        if (isset($this->attendance[$studentId])) {
            $this->attendance[$studentId]['status'] = $status;
        }
    }

    public function setNotes($studentId, $notes)
    {
        if (isset($this->attendance[$studentId])) {
            $this->attendance[$studentId]['notes'] = $notes;
        }
    }

    public function markAllPresent()
    {
        foreach ($this->attendance as &$record) {
            $record['status'] = 'present';
        }
    }

    public function markAllAbsent()
    {
        foreach ($this->attendance as &$record) {
            $record['status'] = 'absent';
        }
    }

    public function toggleStatus($studentId)
    {
        if (isset($this->attendance[$studentId])) {
            $current = $this->attendance[$studentId]['status'];
            $this->attendance[$studentId]['status'] = $current === 'present' ? 'absent' : 'present';
        }
    }

    public function openConfirmation()
    {
        // Validate that at least one record exists
        if (empty($this->attendance)) {
            session()->flash('error', 'No students to mark');
            return;
        }

        $this->selectedCount = count($this->attendance);
        $this->showConfirmation = true;
    }

    public function closeConfirmation()
    {
        $this->showConfirmation = false;
    }

    public function submitBulkAttendance()
    {
        if (!$this->sessionId) {
            session()->flash('error', 'No session selected');
            return;
        }

        $this->isLoading = true;

        try {
            // Prepare records
            $records = [];
            foreach ($this->attendance as $studentId => $data) {
                $records[] = [
                    'student_id' => (int) $studentId,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?: null,
                ];
            }

            // Call API
            $response = Http::withToken(request()->bearerToken())
                ->post('/api/attendance/bulk/mark', [
                    'session_id' => $this->sessionId,
                    'records' => $records,
                ]);

            if ($response->successful()) {
                $this->bulkResult = $response->json('result');
                session()->flash('message', "Attendance marked for {$this->bulkResult['success']} students");
                $this->showConfirmation = false;

                // Reset form
                $this->initializeAttendance();
            } else {
                $errors = $response->json('errors');
                session()->flash('error', 'Failed to mark attendance: ' . json_encode($errors));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function exportAsCSV()
    {
        if (!$this->session) {
            return;
        }

        $csv = "student_id,student_name,status,notes\n";

        foreach ($this->students as $student) {
            $studentId = $student['id'];
            $status = $this->attendance[$studentId]['status'] ?? 'present';
            $notes = $this->attendance[$studentId]['notes'] ?? '';

            // Escape quotes in notes
            $notes = str_replace('"', '""', $notes);

            $csv .= "{$studentId},\"{$student['first_name']} {$student['last_name']}\",{$status},\"{$notes}\"\n";
        }

        return response()->streamDownload(
            fn () => print($csv),
            "attendance_{$this->session->id}_" . now()->format('Y-m-d') . ".csv"
        );
    }

    public function importCSV()
    {
        $file = request()->file('csv_file');

        if (!$file) {
            session()->flash('error', 'No file selected');
            return;
        }

        try {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // Skip header

            while ($row = fgetcsv($handle)) {
                if (count($row) < 2) {
                    continue;
                }

                $studentId = (int) $row[0];
                $status = $row[2] ?? 'present';
                $notes = $row[3] ?? '';

                if (isset($this->attendance[$studentId])) {
                    $this->attendance[$studentId] = [
                        'status' => $status,
                        'notes' => $notes,
                    ];
                }
            }

            fclose($handle);
            session()->flash('message', 'CSV imported successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
