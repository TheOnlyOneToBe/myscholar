<div class="grades-section bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold"><i class="fas fa-book mr-2"></i>Mes Notes</h2>

        @if(!empty($academicPeriods))
        <div class="flex items-center gap-3">
            <label for="period-select" class="text-sm font-semibold text-gray-700">Trimestre/Semestre:</label>
            <select
                id="period-select"
                wire:model.live="selectedPeriodId"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($academicPeriods as $period)
                    <option value="{{ $period['id'] }}">
                        {{ $period['name'] }} ({{ $period['start_date'] }} - {{ $period['end_date'] }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Module non disponible:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        <div class="space-y-6">
            <!-- Recent Grades -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Notes Récentes</h3>
                @if(!empty($recentGrades))
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Matière</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Score</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Grade</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentGrades as $grade)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $grade['subject'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ $grade['score'] }}/20</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                                @if($grade['grade'] === 'A') bg-green-100 text-green-800
                                                @elseif($grade['grade'] === 'B') bg-blue-100 text-blue-800
                                                @elseif($grade['grade'] === 'C') bg-yellow-100 text-yellow-800
                                                @elseif($grade['grade'] === 'D') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ $grade['grade'] }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $grade['date'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">Aucune note enregistrée pour le moment.</p>
                @endif
            </div>

            <!-- Subject Performance -->
            @if(!empty($subjectPerformance))
            <div>
                <h3 class="text-lg font-semibold mb-4">Performance par Matière</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($subjectPerformance as $subject)
                        <div class="border border-gray-200 rounded p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">{{ $subject['subject'] }}</h4>
                                <span class="text-lg font-bold text-blue-600">{{ $subject['average'] }}/20</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($subject['average'] / 20) * 100 }}%"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">{{ $subject['grades_count'] }} note(s)</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pending Appeals -->
            @if(!empty($pendingAppeals))
            <div>
                <h3 class="text-lg font-semibold mb-4">Appels en Attente</h3>
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 space-y-3">
                    @foreach($pendingAppeals as $appeal)
                        <div class="border-b pb-3">
                            <p class="font-semibold">{{ $appeal['subject'] }}</p>
                            <p class="text-sm text-gray-600">Note originale: {{ $appeal['original_score'] }}/20</p>
                            <p class="text-sm text-gray-600">Motif: {{ $appeal['reason'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
