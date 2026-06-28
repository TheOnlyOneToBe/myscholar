<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-graduation-cap mr-2"></i> Comparaison Multi-Trimestres</h3>
        <button
            wire:click="downloadBulletin('complete')"
            class="px-3 py-1 bg-purple-500 text-white rounded-lg font-semibold text-sm hover:bg-purple-600 transition-all"
        >
            <i class="fas fa-download mr-1"></i> Bulletin Complet
        </button>
    </div>

    @if ($loading)
    <div class="animate-pulse space-y-4">
        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
    </div>
    @else
    <div class="space-y-6">
        <!-- Tab Navigation -->
        <div class="flex gap-2 border-b border-gray-200 pb-4">
            <button
                wire:click="switchTab('overview')"
                class="px-4 py-2 font-semibold transition-all {{ $activeTab === 'overview' ? 'border-b-2 border-purple-500 text-purple-600' : 'text-gray-600 hover:text-gray-800' }}"
            >
                <i class="fas fa-eye mr-1"></i> Résumé
            </button>
            <button
                wire:click="switchTab('term1')"
                class="px-4 py-2 font-semibold transition-all {{ $activeTab === 'term1' ? 'border-b-2 border-purple-500 text-purple-600' : 'text-gray-600 hover:text-gray-800' }}"
            >
                <i class="fas fa-1 mr-1"></i> T1
            </button>
            <button
                wire:click="switchTab('term2')"
                class="px-4 py-2 font-semibold transition-all {{ $activeTab === 'term2' ? 'border-b-2 border-purple-500 text-purple-600' : 'text-gray-600 hover:text-gray-800' }}"
            >
                <i class="fas fa-2 mr-1"></i> T2
            </button>
            <button
                wire:click="switchTab('term3')"
                class="px-4 py-2 font-semibold transition-all {{ $activeTab === 'term3' ? 'border-b-2 border-purple-500 text-purple-600' : 'text-gray-600 hover:text-gray-800' }}"
            >
                <i class="fas fa-3 mr-1"></i> T3
            </button>
        </div>

        <!-- Overview Tab -->
        @if ($activeTab === 'overview')
        <div class="space-y-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Résumé de l'Année Académique</h4>

            <!-- Annual Summary Card -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Moyenne Annuelle</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $comparisonData['year_summary']['average'] ?? 0 }}/20</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1">Grade: {{ $comparisonData['year_summary']['grade'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-center border-l border-purple-300">
                        <p class="text-sm text-gray-600 mb-2">Notes Totales</p>
                        <p class="text-3xl font-bold text-gray-700">{{ $comparisonData['year_summary']['grade_count'] ?? 0 }}</p>
                    </div>
                    <div class="text-center border-l border-purple-300">
                        <p class="text-sm text-gray-600 mb-2">Plus Haute</p>
                        <p class="text-3xl font-bold text-green-600">{{ $comparisonData['year_summary']['highest'] ?? 0 }}</p>
                    </div>
                    <div class="text-center border-l border-purple-300">
                        <p class="text-sm text-gray-600 mb-2">Plus Basse</p>
                        <p class="text-3xl font-bold text-red-600">{{ $comparisonData['year_summary']['lowest'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Terms Comparison -->
            <h5 class="text-md font-semibold text-gray-800 mt-6 mb-4">Performance par Trimestre</h5>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach (['term_1' => 'T1', 'term_2' => 'T2', 'term_3' => 'T3'] as $key => $label)
                <div class="bg-gradient-to-br {{ $comparisonData['terms'][$key]['average'] >= 16 ? 'from-green-50 to-green-100' : ($comparisonData['terms'][$key]['average'] >= 12 ? 'from-orange-50 to-orange-100' : 'from-red-50 to-red-100') }} p-4 rounded-lg border {{ $comparisonData['terms'][$key]['average'] >= 16 ? 'border-green-200' : ($comparisonData['terms'][$key]['average'] >= 12 ? 'border-orange-200' : 'border-red-200') }}">
                    <h6 class="font-semibold text-gray-800 mb-2">{{ $label }}</h6>
                    <p class="text-2xl font-bold {{ $comparisonData['terms'][$key]['average'] >= 16 ? 'text-green-600' : ($comparisonData['terms'][$key]['average'] >= 12 ? 'text-orange-600' : 'text-red-600') }}">
                        {{ $comparisonData['terms'][$key]['average'] ?? 0 }}/20
                    </p>
                    <p class="text-sm text-gray-700 mt-1">Grade: <strong>{{ $comparisonData['terms'][$key]['grade'] ?? 'N/A' }}</strong></p>
                    <p class="text-xs text-gray-600 mt-1">{{ $comparisonData['terms'][$key]['grade_count'] ?? 0 }} notes</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Term Details -->
        @foreach (['term_1' => 'T1', 'term_2' => 'T2', 'term_3' => 'T3'] as $key => $label)
        @if ($activeTab === $key)
        <div class="space-y-4">
            <div class="bg-gradient-to-r from-purple-100 to-purple-50 p-4 rounded-lg border border-purple-200">
                <h5 class="text-lg font-semibold text-gray-800 mb-2">{{ $comparisonData['terms'][$key]['name'] ?? 'Trimestre' }}</h5>
                <p class="text-sm text-gray-600">{{ $comparisonData['terms'][$key]['period'] ?? '' }}</p>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div>
                        <span class="text-gray-600">Moyenne:</span>
                        <p class="font-bold text-lg">{{ $comparisonData['terms'][$key]['average'] ?? 0 }}/20</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Grade:</span>
                        <p class="font-bold text-lg">{{ $comparisonData['terms'][$key]['grade'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Plus haute:</span>
                        <p class="font-bold text-lg text-green-600">{{ $comparisonData['terms'][$key]['highest'] ?? 0 }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Plus basse:</span>
                        <p class="font-bold text-lg text-red-600">{{ $comparisonData['terms'][$key]['lowest'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Subjects Performance -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 font-semibold">Matière</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-semibold">Moyenne</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($comparisonData['terms'][$key]['subject_performance'] ?? [] as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800 font-semibold">{{ $subject->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-lg font-bold {{ $subject->average >= 16 ? 'text-green-600' : ($subject->average >= 12 ? 'text-orange-600' : 'text-red-600') }}">
                                    {{ round($subject->average, 2) }}/20
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $subject->grade_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button
                wire:click="downloadBulletin('{{ $key }}')"
                class="w-full mt-4 px-4 py-2 bg-purple-500 text-white rounded-lg font-semibold hover:bg-purple-600 transition-all"
            >
                <i class="fas fa-download mr-1"></i> Télécharger le Bulletin T{{ substr($label, -1) }}
            </button>
        </div>
        @endif
        @endforeach
    </div>
    @endif
</div>
