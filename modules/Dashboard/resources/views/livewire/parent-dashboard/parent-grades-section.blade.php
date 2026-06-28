<div class="bg-white rounded-lg shadow p-6 ml-64">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold"><i class="fas fa-chart-bar mr-2 text-blue-600"></i>Notes de {{ $childName }}</h2>
        <div class="text-right">
            <p class="text-sm text-gray-600">Moyenne Générale</p>
            <p class="text-3xl font-bold text-blue-600">{{ $childAverage }}/20</p>
        </div>
    </div>

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
                    <div class="border border-gray-200 rounded p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold">{{ $subject['subject'] }}</h4>
                            <span class="text-lg font-bold text-blue-600">{{ $subject['average'] }}/20</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($subject['average'] / 20) * 100 }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $subject['grades_count'] }} note@if($subject['grades_count'] !== 1)s@endif</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
