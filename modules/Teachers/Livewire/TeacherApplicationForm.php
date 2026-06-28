<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Grades\Models\Subject;
use Modules\Teachers\Models\TeacherApplication;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class TeacherApplicationForm extends Component
{
    public $specialization = '';
    public $qualification_level = '';
    public $hire_date = '';
    public $filiere = '';
    public $office_location = '';
    public $years_of_experience = 0;
    public $bio = '';
    public $phone_office = '';
    public $email_office = '';
    public $selectedSubjects = [];
    public $subjectProficiency = [];
    public $subjectSinceYear = [];
    public $subjectIsPrimary = [];
    public $message = '';
    public $messageType = '';

    public function mount()
    {
        // Check if user already has a pending application
        $existing = TeacherApplication::where('user_id', auth()->id())->first();
        if ($existing && $existing->isPending()) {
            $this->message = __('teachers::messages.info.application_pending');
            $this->messageType = 'info';
        }
    }

    public function getSubjectsProperty(): Collection
    {
        return Subject::orderBy('name')->get();
    }

    public function addSubject($subjectId)
    {
        if (!in_array($subjectId, $this->selectedSubjects)) {
            $this->selectedSubjects[] = $subjectId;
            $this->subjectProficiency[$subjectId] = 3;
            $this->subjectSinceYear[$subjectId] = now()->year;
            $this->subjectIsPrimary[$subjectId] = false;
        }
    }

    public function removeSubject($subjectId)
    {
        $this->selectedSubjects = array_filter(
            $this->selectedSubjects,
            fn($id) => $id != $subjectId
        );
        unset($this->subjectProficiency[$subjectId]);
        unset($this->subjectSinceYear[$subjectId]);
        unset($this->subjectIsPrimary[$subjectId]);
    }

    public function setPrimary($subjectId)
    {
        foreach ($this->subjectIsPrimary as $key => $value) {
            $this->subjectIsPrimary[$key] = false;
        }
        $this->subjectIsPrimary[$subjectId] = true;
    }

    public function submit()
    {
        $this->validate([
            'specialization' => 'required|string|max:255',
            'qualification_level' => 'required|string|max:255',
            'hire_date' => 'nullable|date',
            'filiere' => 'nullable|in:generale,technique',
            'office_location' => 'nullable|string|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'nullable|string|max:1000',
            'phone_office' => 'nullable|string|max:20',
            'email_office' => 'nullable|email|max:255',
            'selectedSubjects' => 'required|array|min:1',
        ], [
            'selectedSubjects.required' => __('teachers::validation.selectedSubjects.required'),
            'selectedSubjects.min' => __('teachers::validation.selectedSubjects.min'),
        ]);

        // Prepare subjects data
        $subjectsData = [];
        foreach ($this->selectedSubjects as $subjectId) {
            $subjectsData[] = [
                'subject_id' => $subjectId,
                'proficiency_level' => $this->subjectProficiency[$subjectId] ?? 3,
                'since_year' => $this->subjectSinceYear[$subjectId] ?? now()->year,
                'is_primary' => $this->subjectIsPrimary[$subjectId] ?? false,
            ];
        }

        // Create application
        TeacherApplication::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'specialization' => $this->specialization,
                'qualification_level' => $this->qualification_level,
                'hire_date' => $this->hire_date ? \Carbon\Carbon::parse($this->hire_date) : null,
                'filiere' => $this->filiere,
                'office_location' => $this->office_location,
                'years_of_experience' => $this->years_of_experience,
                'bio' => $this->bio,
                'phone_office' => $this->phone_office,
                'email_office' => $this->email_office,
                'subjects_data' => $subjectsData,
                'status' => 'pending',
            ]
        );

        $this->message = __('teachers::messages.success.application_submitted');
        $this->messageType = 'success';
        $this->reset();
    }

    public function render()
    {
        return view('teachers::livewire.teacher-application-form', [
            'subjects' => $this->subjects,
        ]);
    }
}
