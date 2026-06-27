<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Traits\HasAlerts;

class SimpleForm extends Component
{
    use HasAlerts;

    public string $name = '';
    public string $email = '';
    public string $message = '';

    public function mount(): void
    {
        $this->initializeAlerts();
    }

    public function submit(): void
    {
        // Validation
        $validated = $this->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        // Process form
        try {
            // Simulate form processing
            $this->success('Message sent successfully!', 'MESSAGE_SENT');
            $this->warning('Please check your email for confirmation', 'EMAIL_CONFIRM');

            // Reset form
            $this->reset(['name', 'email', 'message']);
        } catch (\Exception $e) {
            $this->error('Error sending message: ' . $e->getMessage(), 'SEND_ERROR');
        }
    }

    public function render()
    {
        return view('livewire.forms.simple-form');
    }
}
