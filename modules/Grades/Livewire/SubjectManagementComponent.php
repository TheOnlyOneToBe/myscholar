<?php

namespace Modules\Grades\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Grades\Models\Subject;
use Modules\Grades\Services\SubjectService;

class SubjectManagementComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 25;
    public $showModal = false;
    public $editingId = null;
    public $formData = [
        'code' => '',
        'name' => '',
        'description' => '',
        'credits' => 3,
        'coefficient' => 1.0,
        'is_active' => true,
    ];

    protected $paginationTheme = 'tailwind';
    protected $rules = [
        'formData.code' => 'required|string|max:50|unique:subjects',
        'formData.name' => 'required|string|max:255',
        'formData.description' => 'nullable|string',
        'formData.credits' => 'nullable|integer|min:1|max:10',
        'formData.coefficient' => 'nullable|numeric|min:0.1|max:5',
        'formData.is_active' => 'nullable|boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->editingId = null;
        $this->resetForm();
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        $this->editingId = $id;
        $this->formData = $subject->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        $service = app(SubjectService::class);
        
        if ($this->editingId) {
            $rules = $this->rules;
            $rules['formData.code'] = "required|string|max:50|unique:subjects,code,{$this->editingId}";
            $this->validate($rules);
            
            $service->updateSubject($this->editingId, $this->formData);
            $this->dispatch('notify', ['message' => 'Subject updated successfully']);
        } else {
            $this->validate();
            $service->createSubject($this->formData);
            $this->dispatch('notify', ['message' => 'Subject created successfully']);
        }
        
        $this->closeModal();
    }

    public function delete($id)
    {
        $service = app(SubjectService::class);
        $service->deleteSubject($id);
        $this->dispatch('notify', ['message' => 'Subject deleted successfully']);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->formData = [
            'code' => '',
            'name' => '',
            'description' => '',
            'credits' => 3,
            'coefficient' => 1.0,
            'is_active' => true,
        ];
    }

    public function render()
    {
        $query = Subject::query();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
        }

        $subjects = $query->paginate($this->perPage);

        return view('grades::livewire.subject-management', ['subjects' => $subjects]);
    }
}
