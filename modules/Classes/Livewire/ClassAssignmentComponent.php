<?php

namespace Modules\Classes\Livewire;

use Livewire\Component;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Models\ClassAssignment;
use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolYear;

class ClassAssignmentComponent extends Component
{
    public $classId;
    public ClassModel $class;
    public $assignments;

    public $showForm = false;
    public $editingId = null;
    public $form = [
        'user_id' => '',
        'role' => 'teacher',
        'subject' => '',
        'school_year_id' => '',
        'notes' => '',
    ];

    public $deleteConfirm = null;

    public function mount(ClassModel $class)
    {
        $this->class = $class;
        $this->classId = $class->id;
        $this->form['school_year_id'] = $class->school_year_id;
        $this->loadAssignments();
    }

    public function loadAssignments()
    {
        $this->assignments = $this->class->assignments()
            ->with('teacher', 'schoolYear')
            ->get();
    }

    public function render()
    {
        return view('classes::livewire.class-assignment', [
            'assignments' => $this->assignments,
            'teachers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'enseignant');
            })->get(),
            'roles' => ['teacher', 'class_teacher', 'coordinator'],
            'schoolYears' => SchoolYear::all(),
        ]);
    }

    public function openForm($id = null)
    {
        if ($id) {
            $assignment = ClassAssignment::findOrFail($id);
            $this->editingId = $id;
            $this->form = [
                'user_id' => $assignment->user_id,
                'role' => $assignment->role,
                'subject' => $assignment->subject,
                'school_year_id' => $assignment->school_year_id,
                'notes' => $assignment->notes,
            ];
        } else {
            $this->resetForm();
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
            'user_id' => '',
            'role' => 'teacher',
            'subject' => '',
            'school_year_id' => $this->class->school_year_id,
            'notes' => '',
        ];
    }

    public function saveAssignment()
    {
        $validated = $this->validate([
            'form.user_id' => 'required|exists:users,id',
            'form.role' => 'required|string|in:teacher,class_teacher,coordinator',
            'form.subject' => 'nullable|string|max:100',
            'form.school_year_id' => 'required|exists:school_years,id',
            'form.notes' => 'nullable|string',
        ]);

        if ($this->editingId) {
            ClassAssignment::findOrFail($this->editingId)->update($this->form);
            $this->dispatch('notify', message: 'Affectation mise à jour', type: 'success');
        } else {
            $this->class->assignments()->create($this->form);
            $this->dispatch('notify', message: 'Affectation créée', type: 'success');
        }

        $this->closeForm();
        $this->loadAssignments();
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirm = $id;
    }

    public function deleteAssignment()
    {
        if ($this->deleteConfirm) {
            ClassAssignment::findOrFail($this->deleteConfirm)->delete();
            $this->dispatch('notify', message: 'Affectation supprimée', type: 'success');
            $this->deleteConfirm = null;
            $this->loadAssignments();
        }
    }

    public function cancelDelete()
    {
        $this->deleteConfirm = null;
    }
}
