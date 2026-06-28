<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Attendance\Repositories\AttendanceSessionRepository;
use Modules\Attendance\Models\AttendanceSession;

class AttendanceSessionListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $classId = null;
    public $perPage = 25;
    public $showCreateModal = false;
    public $editingSession = null;

    protected $sessionRepository;

    public function mount()
    {
        $this->sessionRepository = app(AttendanceSessionRepository::class);
    }

    public function render()
    {
        $query = AttendanceSession::query();

        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }

        $sessions = $query->orderBy('date', 'desc')
            ->paginate($this->perPage);

        return view('attendance::livewire.attendance-session-list', [
            'sessions' => $sessions,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->editingSession = null;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->editingSession = null;
    }

    public function deleteSession(AttendanceSession $session)
    {
        try {
            $this->sessionRepository->delete($session);
            session()->flash('message', 'Session deleted successfully');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete session');
        }
    }
}
