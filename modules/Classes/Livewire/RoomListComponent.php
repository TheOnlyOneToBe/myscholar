<?php

namespace Modules\Classes\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Classes\Models\Room;

class RoomListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $building = '';
    public $type = '';
    public $per_page = 25;

    protected $paginationTheme = 'tailwind';

    public $showForm = false;
    public $editingId = null;
    public $form = [
        'name' => '',
        'building' => '',
        'capacity' => 45,
        'type' => 'classroom',
        'description' => '',
    ];

    public $deleteConfirm = null;

    public function render()
    {
        $query = Room::query();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->building) {
            $query->where('building', $this->building);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $rooms = $query->paginate($this->per_page);

        return view('classes::livewire.room-list', [
            'rooms' => $rooms,
            'types' => ['classroom', 'lab', 'auditorium', 'library'],
            'buildings' => Room::distinct('building')->whereNotNull('building')->pluck('building'),
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedBuilding()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        if ($id) {
            $room = Room::findOrFail($id);
            $this->editingId = $id;
            $this->form = [
                'name' => $room->name,
                'building' => $room->building,
                'capacity' => $room->capacity,
                'type' => $room->type,
                'description' => $room->description,
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
            'name' => '',
            'building' => '',
            'capacity' => 45,
            'type' => 'classroom',
            'description' => '',
        ];
    }

    public function saveRoom()
    {
        $validated = $this->validate([
            'form.name' => $this->editingId
                ? 'required|string|max:100|unique:rooms,name,' . $this->editingId
                : 'required|string|max:100|unique:rooms,name',
            'form.building' => 'nullable|string|max:100',
            'form.capacity' => 'required|integer|min:1',
            'form.type' => 'required|string|in:classroom,lab,auditorium,library',
            'form.description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            Room::findOrFail($this->editingId)->update($this->form);
            $this->dispatch('notify', message: 'Salle mise à jour', type: 'success');
        } else {
            Room::create($this->form);
            $this->dispatch('notify', message: 'Salle créée', type: 'success');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirm = $id;
    }

    public function deleteRoom()
    {
        if ($this->deleteConfirm) {
            Room::findOrFail($this->deleteConfirm)->delete();
            $this->dispatch('notify', message: 'Salle supprimée', type: 'success');
            $this->deleteConfirm = null;
            $this->resetPage();
        }
    }

    public function cancelDelete()
    {
        $this->deleteConfirm = null;
    }
}
