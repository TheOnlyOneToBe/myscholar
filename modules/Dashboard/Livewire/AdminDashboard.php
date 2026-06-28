<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Dashboard\Services\DashboardService;

class AdminDashboard extends Component
{
    protected DashboardService $dashboardService;

    public array $quickStats = [];
    public array $recentActivity = [];
    public array $systemHealth = [];
    public array $topAbsentStudents = [];
    public array $highestPerformers = [];
    public array $lowPerformers = [];
    public array $subjectAverages = [];
    public float $attendanceRate = 0;
    public float $averageGrade = 0;
    public int $pendingAppeals = 0;

    public function mount()
    {
        $this->dashboardService = app(DashboardService::class);
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->quickStats = $this->dashboardService->getQuickStats();
        $this->recentActivity = $this->dashboardService->getRecentActivity(8);
        $this->systemHealth = $this->dashboardService->getSystemHealth();
        $this->topAbsentStudents = $this->dashboardService->getTopAbsentStudents(5);
        $this->highestPerformers = $this->dashboardService->getHighestPerformers(5);
        $this->lowPerformers = $this->dashboardService->getLowPerformers(5);
        $this->subjectAverages = $this->dashboardService->getSubjectAverages();
        $this->attendanceRate = $this->dashboardService->getAttendanceRate();
        $this->averageGrade = $this->dashboardService->getAverageGrade();
        $this->pendingAppeals = $this->dashboardService->getPendingAppealsCount();
    }

    public function refresh()
    {
        $this->loadDashboardData();
    }

    public function render()
    {
        return view('dashboard::livewire.admin-dashboard');
    }
}
