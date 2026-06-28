<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chart-line mr-2"></i> Analyse Graphique</h3>
        <div class="flex gap-2">
            <button
                wire:click="switchChart('progression')"
                class="px-3 py-1 rounded-lg font-semibold transition-all text-sm {{ $activeChart === 'progression' ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <i class="fas fa-arrow-trend-up mr-1"></i> Progression
            </button>
            <button
                wire:click="switchChart('subjects')"
                class="px-3 py-1 rounded-lg font-semibold transition-all text-sm {{ $activeChart === 'subjects' ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <i class="fas fa-bars mr-1"></i> Matières
            </button>
            <button
                wire:click="switchChart('radar')"
                class="px-3 py-1 rounded-lg font-semibold transition-all text-sm {{ $activeChart === 'radar' ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <i class="fas fa-diagram-project mr-1"></i> Comparaison
            </button>
        </div>
    </div>

    @if ($loading)
    <div class="animate-pulse space-y-4">
        <div class="h-96 bg-gray-200 rounded"></div>
    </div>
    @else
    <div class="bg-gray-50 rounded-lg p-4">
        <!-- Progression Chart -->
        @if ($activeChart === 'progression')
        <div class="h-96">
            <canvas id="progressionChart" data-chart='@json($progressionChartData)'></canvas>
        </div>
        @endif

        <!-- Subject Distribution Chart -->
        @if ($activeChart === 'subjects')
        <div class="h-96">
            <canvas id="subjectChart" data-chart='@json($subjectChartData)'></canvas>
        </div>
        @endif

        <!-- Radar Chart -->
        @if ($activeChart === 'radar')
        <div class="h-96">
            <canvas id="radarChart" data-chart='@json($radarChartData)'></canvas>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Chart = window.Chart;

            @if ($activeChart === 'progression')
            const progressionCanvas = document.getElementById('progressionChart');
            if (progressionCanvas) {
                const progressionData = {!! json_encode($progressionChartData) !!};
                new Chart(progressionCanvas, {
                    type: 'line',
                    data: progressionData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Progression des Notes (6 mois)' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 20
                            }
                        }
                    }
                });
            }
            @endif

            @if ($activeChart === 'subjects')
            const subjectCanvas = document.getElementById('subjectChart');
            if (subjectCanvas) {
                const subjectData = {!! json_encode($subjectChartData) !!};
                new Chart(subjectCanvas, {
                    type: 'bar',
                    data: subjectData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Distribution par Matière' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 20
                            }
                        }
                    }
                });
            }
            @endif

            @if ($activeChart === 'radar')
            const radarCanvas = document.getElementById('radarChart');
            if (radarCanvas) {
                const radarData = {!! json_encode($radarChartData) !!};
                new Chart(radarCanvas, {
                    type: 'radar',
                    data: radarData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Comparaison avec la Classe' }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 20
                            }
                        }
                    }
                });
            }
            @endif
        });

        // Reinitialize when Livewire updates
        Livewire.hook('message.processed', () => {
            const Chart = window.Chart;
            Chart.helpers.each(Chart.instances, instance => {
                instance.destroy();
            });

            @if ($activeChart === 'progression')
            const progressionCanvas = document.getElementById('progressionChart');
            if (progressionCanvas) {
                const progressionData = {!! json_encode($progressionChartData) !!};
                new Chart(progressionCanvas, {
                    type: 'line',
                    data: progressionData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Progression des Notes (6 mois)' }
                        },
                        scales: {
                            y: { beginAtZero: true, max: 20 }
                        }
                    }
                });
            }
            @endif
        });
    </script>
    @endif
</div>
