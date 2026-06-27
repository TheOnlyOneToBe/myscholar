<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\AlertService;

class AlertToast extends Component
{
    public array $alerts = [];
    public bool $showToasts = true;

    public function mount(AlertService $alertService)
    {
        $this->alerts = $alertService->all();
    }

    public function deleteAlert(string $id): void
    {
        $this->dispatch('alert:deleted', id: $id);
    }

    public function render()
    {
        return view('livewire.components.alert-toast');
    }
}
