<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Teachers\Models\Teacher;
use Modules\Grades\Models\Subject;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class TeacherSubjectManagement extends Component
{
    public Teacher $teacher;
    public $selectedSubjects = [];
    public $newSubjectId = '';
    public $newProficiency = 3;
    public $newSinceYear;
    public $newIsPrimary = false;

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;
        $this->newSinceYear = now()->year;

        // Load selected subjects
        $this->selectedSubjects = $teacher->subjects()
            ->get()
            ->keyBy('id')
            ->toArray();
    }

    public function getAvailableSubjectsProperty(): Collection
    {
        $assignedIds = $this->teacher->subjects()->pluck('subject_id')->toArray();
        return Subject::whereNotIn('id', $assignedIds)->orderBy('name')->get();
    }

    public function addSubject()
    {
        $this->validate([
            'newSubjectId' => 'required|exists:subjects,id',
            'newProficiency' => 'required|integer|between:1,5',
            'newSinceYear' => 'required|integer|min:1900|max:' . now()->year,
        ]);

        // Check if already assigned
        if ($this->teacher->subjects()->where('subject_id', $this->newSubjectId)->exists()) {
            $this->dispatch('error', __('teachers::messages.error.subject_already_assigned'));
            return;
        }

        // If marking as primary, remove primary from others
        if ($this->newIsPrimary) {
            $this->teacher->subjects()->update(['is_primary' => false]);
        }

        $this->teacher->subjects()->attach($this->newSubjectId, [
            'proficiency_level' => $this->newProficiency,
            'since_year' => $this->newSinceYear,
            'is_primary' => $this->newIsPrimary,
        ]);

        $this->reset(['newSubjectId', 'newProficiency', 'newIsPrimary']);
        $this->newProficiency = 3;
        $this->newSinceYear = now()->year;
        $this->dispatch('success', __('teachers::messages.success.subject_added'));
    }

    public function updateSubject($subjectId, $proficiency, $sinceYear, $isPrimary)
    {
        $this->validate([
            'newProficiency' => 'required|integer|between:1,5',
            'newSinceYear' => 'required|integer|min:1900|max:' . now()->year,
        ]);

        // If marking as primary, remove primary from others
        if ($isPrimary && !$this->teacher->subjects()->where('subject_id', $subjectId)->wherePivot('is_primary', true)->exists()) {
            $this->teacher->subjects()->update(['is_primary' => false]);
        }

        $this->teacher->subjects()->updateExistingPivot($subjectId, [
            'proficiency_level' => $proficiency,
            'since_year' => $sinceYear,
            'is_primary' => $isPrimary,
        ]);

        $this->dispatch('success', __('teachers::messages.success.subject_added'));
    }

    public function removeSubject($subjectId)
    {
        $this->teacher->subjects()->detach($subjectId);
        $this->dispatch('success', __('teachers::messages.success.subject_removed'));
    }

    public function render()
    {
        return view('teachers::livewire.teacher-subject-management', [
            'subjects' => $this->teacher->subjects()->get(),
            'availableSubjects' => $this->availableSubjects,
        ]);
    }
}
