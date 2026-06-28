<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;

class StudentDashboard extends Component
{
    public $activeTab = 'overview';
    public $studentInfo = [];
    public $quickStats = [];
    public $recentGrades = [];
    public $attendanceSummary = [];
    public $upcomingPayments = [];
    public $classInfo = [];
    public $isChefClasse = false;
    public $chefClasseData = [];

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    public function switchTab($tab): void
    {
        $this->activeTab = $tab;
    }

    private function loadDashboardData(): void
    {
        $service = app(StudentDashboardService::class);

        $this->studentInfo = $service->getStudentInfo();
        $this->quickStats = $service->getQuickStats();
        $this->recentGrades = $service->getRecentGrades(5);
        $this->attendanceSummary = $service->getAttendanceSummary();
        $this->upcomingPayments = $service->getUpcomingPaymentsDue(3);
        $this->classInfo = $service->getClassInformation();
        $this->isChefClasse = $service->isChefClasse();

        if ($this->isChefClasse) {
            $this->chefClasseData = $service->getChefClasseData();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard');
    }
}
