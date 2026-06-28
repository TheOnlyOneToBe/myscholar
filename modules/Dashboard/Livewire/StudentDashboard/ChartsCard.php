<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ChartDataService;

class ChartsCard extends Component
{
    public $activeChart = 'progression';
    public $progressionChartData = [];
    public $subjectChartData = [];
    public $radarChartData = [];
    public $loading = true;

    public function mount()
    {
        $this->loadCharts();
        $this->loading = false;
    }

    public function switchChart(string $chartName)
    {
        $this->activeChart = $chartName;
    }

    private function loadCharts()
    {
        $chartService = app(ChartDataService::class);
        $this->progressionChartData = $chartService->getProgressionChartData();
        $this->subjectChartData = $chartService->getSubjectDistributionChartData();
        $this->radarChartData = $chartService->getClassComparisonRadarData();
    }

    public function render()
    {
        return view('livewire.student-dashboard.charts-card');
    }
}
