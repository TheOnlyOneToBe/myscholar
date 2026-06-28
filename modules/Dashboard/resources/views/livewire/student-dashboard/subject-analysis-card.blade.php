<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-book mr-2"></i> Analyse par Matière</h3>
        <span class="text-sm font-semibold text-purple-600">{{ $analysis['total_subjects'] ?? 0 }} matières</span>
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 rounded"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
    @else
        <!-- Meilleures et Pires Matières -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @if ($analysis['best_subject'])
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-green-800"><i class="fas fa-trophy mr-1"></i> Meilleure Matière</h4>
                    <span class="text-2xl font-bold text-green-600">{{ $analysis['best_subject']['average'] }}/20</span>
                </div>
                <p class="text-gray-700"><strong>{{ $analysis['best_subject']['name'] }}</strong></p>
                <p class="text-sm text-gray-600">{{ $analysis['best_subject']['grade_count'] }} notes • Grade: {{ $analysis['best_subject']['grade'] }}</p>
            </div>
            @endif

            @if ($analysis['worst_subject'])
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-red-800"><i class="fas fa-exclamation-triangle mr-1"></i> À Améliorer</h4>
                    <span class="text-2xl font-bold text-red-600">{{ $analysis['worst_subject']['average'] }}/20</span>
                </div>
                <p class="text-gray-700"><strong>{{ $analysis['worst_subject']['name'] }}</strong></p>
                <p class="text-sm text-gray-600">{{ $analysis['worst_subject']['grade_count'] }} notes • Grade: {{ $analysis['worst_subject']['grade'] }}</p>
            </div>
            @endif
        </div>

        <!-- Matières à Améliorer -->
        @if (count($analysis['improvement_needed']) > 0)
        <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 rounded">
            <h4 class="font-semibold text-orange-800 mb-3"><i class="fas fa-thumbtack mr-1"></i> Priorités d'Amélioration</h4>
            <div class="space-y-2">
                @foreach ($analysis['improvement_needed'] as $subject)
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">{{ $subject['name'] }}</span>
                    <span class="text-sm font-bold text-orange-600">{{ $subject['average'] }}/20</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tableau de Toutes les Matières -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-700">Matière</th>
                        <th class="px-4 py-2 text-center text-gray-700">Moyenne</th>
                        <th class="px-4 py-2 text-center text-gray-700">Grade</th>
                        <th class="px-4 py-2 text-center text-gray-700">Notes</th>
                        <th class="px-4 py-2 text-center text-gray-700">Progression</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($analysis['subjects'] as $subject)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800 font-semibold">{{ $subject['name'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-lg font-bold {{ $subject['average'] >= 16 ? 'text-green-600' : ($subject['average'] >= 12 ? 'text-orange-600' : 'text-red-600') }}">
                                {{ $subject['average'] }}/20
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">{{ $subject['grade'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $subject['grade_count'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $subject['progress_percentage'] }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $subject['progress_percentage'] }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
