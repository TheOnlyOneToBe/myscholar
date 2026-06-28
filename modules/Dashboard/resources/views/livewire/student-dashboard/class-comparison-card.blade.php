<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chart-bar mr-2"></i> Comparaison avec la Classe</h3>
        <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Mise à jour: {{ date('d/m/Y H:i') }}</span>
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Toi -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                <div class="text-sm text-gray-600 mb-2"><i class="fas fa-star mr-1"></i> Toi</div>
                <div class="text-3xl font-bold text-blue-600">{{ $comparisonData['student']['average'] ?? 0 }}/20</div>
                <div class="text-sm text-gray-700 mt-1">Grade: <strong>{{ $comparisonData['student']['grade'] ?? 'N/A' }}</strong></div>
                <div class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-trophy mr-1"></i> Classement: <strong>#{{ $comparisonData['student']['ranking'] ?? 'N/A' }}</strong>
                </div>
            </div>

            <!-- Classe -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-lg">
                <div class="text-sm text-gray-600 mb-2"><i class="fas fa-users mr-1"></i> Classe ({{ $comparisonData['class']['student_count'] ?? 0 }} élèves)</div>
                <div class="text-3xl font-bold text-gray-700">{{ $comparisonData['class']['average'] ?? 0 }}/20</div>
                <div class="text-sm text-gray-700 mt-1">
                    Plage: {{ $comparisonData['class']['lowest_average'] ?? 0 }} - {{ $comparisonData['class']['highest_average'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 mt-2">
                    Écart: <strong>{{ abs($comparisonData['comparison']['difference'] ?? 0) }}</strong>
                </div>
            </div>

            <!-- Analyse -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                <div class="text-sm text-gray-600 mb-2"><i class="fas fa-chart-line mr-1"></i> Analyse</div>
                <div class="text-2xl font-bold">
                    @if ($comparisonData['comparison']['status'] === 'above')
                        <span class="text-green-600"><i class="fas fa-chart-line mr-1"></i> Au-dessus</span>
                    @else
                        <span class="text-orange-600"><i class="fas fa-arrow-down mr-1"></i> Sous la moyenne</span>
                    @endif
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Percentile: <strong>{{ $comparisonData['comparison']['percentile'] ?? 0 }}%</strong>
                </div>
                <div class="mt-3 w-full bg-gray-300 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $comparisonData['comparison']['percentile'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Message motivant -->
        <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-sm text-blue-800">
                @if ($comparisonData['comparison']['status'] === 'above')
                    <i class="fas fa-check-circle mr-1"></i> Excellent! Tu es au-dessus de la moyenne. Continues tes efforts pour maintenir ce niveau!
                @else
                    <i class="fas fa-heart mr-1"></i> Tu es sous la moyenne. Mais tu peux l'améliorer! Cible les matières faibles et demande de l'aide.
                @endif
            </p>
        </div>
    @endif
</div>
