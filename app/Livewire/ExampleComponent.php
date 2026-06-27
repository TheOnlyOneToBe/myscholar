<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\WithLivewirePermissions;

class ExampleComponent extends Component
{
    use WithLivewirePermissions;

    public function render()
    {
        // Exemple d'utilisation des permissions dans Livewire
        $canViewStudents = $this->userCan('students.view');
        $canCreateStudents = $this->userCan('students.create');
        $isDirecteur = $this->userHasRole('directeur');

        return view('livewire.example-component', [
            'canViewStudents' => $canViewStudents,
            'canCreateStudents' => $canCreateStudents,
            'isDirecteur' => $isDirecteur,
            'currentUser' => $this->getCurrentUser(),
            'userRoles' => $this->getCurrentUserRoles(),
        ]);
    }

    public function performAction()
    {
        // Vérifier la permission avant d'exécuter
        $this->authorize('students.create');

        // L'action est maintenant autorisée
        $this->dispatch('action-performed');
    }
}
