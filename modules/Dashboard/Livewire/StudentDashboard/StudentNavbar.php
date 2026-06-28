<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;

class StudentNavbar extends Component
{
    public $studentName = '';
    public $studentMatricule = '';
    public $currentClass = '';
    public $notificationCount = 0;
    public $isChefClasse = false;
    public $showProfileDropdown = false;
    public $showNotifications = false;

    public function mount(): void
    {
        $this->loadStudentInfo();
    }

    private function loadStudentInfo(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('student')) {
            return;
        }

        try {
            $service = app(StudentDashboardService::class);
            $studentInfo = $service->getStudentInfo();

            $this->studentName = $studentInfo['full_name'] ?? 'Étudiant';
            $this->studentMatricule = $studentInfo['matricule'] ?? '';
            $this->currentClass = $studentInfo['current_class'] ?? '';
            $this->isChefClasse = $service->isChefClasse();

            // Count unread notifications (placeholder - integrate with Notifications module)
            $this->notificationCount = 0;

        } catch (\Exception $e) {
            \Log::error('Error loading student navbar: ' . $e->getMessage());
        }
    }

    public function toggleProfileDropdown(): void
    {
        $this->showProfileDropdown = !$this->showProfileDropdown;
        $this->showNotifications = false;
    }

    public function toggleNotifications(): void
    {
        $this->showNotifications = !$this->showNotifications;
        $this->showProfileDropdown = false;
    }

    public function logout(): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        redirect()->route('login');
    }

    public function navigateTo(string $route): void
    {
        redirect()->route($route);
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-navbar');
    }
}
