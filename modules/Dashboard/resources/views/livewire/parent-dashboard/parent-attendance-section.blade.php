<div class="bg-white rounded-lg shadow p-6 ml-64">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-calendar-check mr-2 text-green-600"></i>Présences de {{ $childName }}</h2>

    @if(!empty($attendanceSummary))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <p class="text-sm text-gray-600 mb-2">Présences</p>
                <p class="text-2xl font-bold text-green-600">{{ $attendanceSummary['total_present'] }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <p class="text-sm text-gray-600 mb-2">Absences</p>
                <p class="text-2xl font-bold text-red-600">{{ $attendanceSummary['total_absent'] }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-sm text-gray-600 mb-2">Retards</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $attendanceSummary['total_late'] }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-600 mb-2">Taux Présence</p>
                <p class="text-2xl font-bold text-blue-600">{{ $attendanceSummary['attendance_rate'] }}%</p>
            </div>
        </div>

        <!-- Attendance Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-semibold">Progression</p>
                <span class="text-sm text-gray-600">{{ $attendanceSummary['total_present'] }}/{{ $attendanceSummary['total'] }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-600 h-3 rounded-full" style="width: {{ $attendanceSummary['attendance_rate'] }}%"></div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                L'enfant a été présent à <strong>{{ $attendanceSummary['total_present'] }}</strong> sessions sur
                <strong>{{ $attendanceSummary['total'] }}</strong> total.
            </p>
        </div>

        <!-- Unjustified Absences -->
        @if(!empty($unjustifiedAbsences))
        <div>
            <h3 class="text-lg font-semibold mb-4">Absences Non Justifiées</h3>
            <div class="space-y-2">
                @foreach($unjustifiedAbsences as $absence)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded flex items-start space-x-3">
                        <i class="fas fa-times-circle text-red-600 flex-shrink-0 mt-1"></i>
                        <div>
                            <p class="font-semibold text-red-800">{{ $absence['date'] }}</p>
                            <p class="text-sm text-red-700">{{ $absence['subject'] ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @else
        <p class="text-gray-600">Aucun enregistrement de présence pour le moment.</p>
    @endif
</div>
