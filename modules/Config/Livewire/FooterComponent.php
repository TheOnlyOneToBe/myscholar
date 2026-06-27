<?php

namespace Modules\Config\Livewire;

use Livewire\Component;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;

class FooterComponent extends Component
{
    public ?SchoolInfo $schoolInfo = null;
    public array $contactInfo = [];
    public string $currentYear = '';
    public string $appVersion = '1.0.0';

    public function mount(): void
    {
        $this->schoolInfo = SchoolInfo::current();

        if ($this->schoolInfo) {
            $this->contactInfo = $this->schoolInfo->getContactInfo();
        }

        $this->currentYear = date('Y');
    }

    public function render()
    {
        return view('config::livewire.footer');
    }
}
