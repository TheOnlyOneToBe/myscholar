<?php

namespace Modules\Classes\Livewire;

use Livewire\Component;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Models\Timetable;
use Modules\Classes\Models\Room;
use Modules\Auth\Models\User;

class TimetableComponent extends Component
{
    public ClassModel $class;
    public $timetables = [];
    public $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    public $timeSlots = [];

    public $showForm = false;
    public $editingId = null;
    public $form = [
        'day_of_week' => '',
        'start_time' => '',
        'end_time' => '',
        'subject_code' => '',
        'user_id' => '',
        'room_id' => '',
        'session_type' => 'regular',
        'notes' => '',
    ];

    public $deleteConfirm = null;

    public function mount(ClassModel $class)
    {
        $this->class = $class;
        $this->generateTimeSlots();
        $this->loadTimetables();
    }

    public function generateTimeSlots()
    {
        $this->timeSlots = [];
        for ($hour = 7; $hour < 17; $hour++) {
            $this->timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }
    }

    public function loadTimetables()
    {
        $this->timetables = $this->class->timetables()
            ->with('teacher', 'room', 'schoolYear')
            ->get()
            ->groupBy('day_of_week');
    }

    public function render()
    {
        return view('classes::livewire.timetable', [
            'timetables' => $this->timetables,
            'teachers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'enseignant');
            })->get(),
            'rooms' => Room::all(),
            'days' => $this->days,
            'timeSlots' => $this->timeSlots,
        ]);
    }

    public function openForm($id = null, $day = null)
    {
        if ($id) {
            $timetable = Timetable::findOrFail($id);
            $this->editingId = $id;
            $this->form = [
                'day_of_week' => $timetable->day_of_week,
                'start_time' => $timetable->start_time,
                'end_time' => $timetable->end_time,
                'subject_code' => $timetable->subject_code,
                'user_id' => $timetable->user_id,
                'room_id' => $timetable->room_id,
                'session_type' => $timetable->session_type,
                'notes' => $timetable->notes,
            ];
        } else {
            $this->resetForm();
            if ($day) {
                $this->form['day_of_week'] = $day;
            }
        }
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->form = [
            'day_of_week' => '',
            'start_time' => '',
            'end_time' => '',
            'subject_code' => '',
            'user_id' => '',
            'room_id' => '',
            'session_type' => 'regular',
            'notes' => '',
        ];
    }

    public function saveTimetable()
    {
        $validated = $this->validate([
            'form.day_of_week' => 'required|string|in:' . implode(',', $this->days),
            'form.start_time' => 'required|date_format:H:i',
            'form.end_time' => 'required|date_format:H:i|after:form.start_time',
            'form.subject_code' => 'required|string|max:20',
            'form.user_id' => 'nullable|exists:users,id',
            'form.room_id' => 'nullable|exists:rooms,id',
            'form.session_type' => 'required|in:regular,exam,makeup',
            'form.notes' => 'nullable|string',
        ]);

        if ($this->editingId) {
            Timetable::findOrFail($this->editingId)->update([
                'class_id' => $this->class->id,
                'school_year_id' => $this->class->school_year_id,
                ...$this->form,
            ]);
            $this->dispatch('notify', message: 'Session mise à jour', type: 'success');
        } else {
            Timetable::create([
                'class_id' => $this->class->id,
                'school_year_id' => $this->class->school_year_id,
                ...$this->form,
            ]);
            $this->dispatch('notify', message: 'Session créée', type: 'success');
        }

        $this->closeForm();
        $this->loadTimetables();
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirm = $id;
    }

    public function deleteTimetable()
    {
        if ($this->deleteConfirm) {
            Timetable::findOrFail($this->deleteConfirm)->delete();
            $this->dispatch('notify', message: 'Session supprimée', type: 'success');
            $this->deleteConfirm = null;
            $this->loadTimetables();
        }
    }

    public function cancelDelete()
    {
        $this->deleteConfirm = null;
    }
}
