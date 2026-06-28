<div class="attendance-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">✅ Mes Présences</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Module non disponible:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        @if(!empty($attendanceSummary))
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Présences</p>
                    <p class="text-2xl font-bold text-green-600">{{ $attendanceSummary['total_present'] }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Absences</p>
                    <p class="text-2xl font-bold text-red-600">{{ $attendanceSummary['total_absent'] }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Retards</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $attendanceSummary['total_late'] }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Taux Présence</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $attendanceSummary['attendance_rate'] }}%</p>
                </div>
            </div>

            <!-- Attendance Progress Bar -->
            <div class="mt-6">
                <p class="text-sm font-semibold mb-2">Progression</p>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-green-600 h-3 rounded-full" style="width: {{ $attendanceSummary['attendance_rate'] }}%"></div>
                </div>
            </div>

            <!-- Summary -->
            <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">
                    Vous avez été présents à <strong>{{ $attendanceSummary['total_present'] }}</strong> sessions sur
                    <strong>{{ $attendanceSummary['total_present'] + $attendanceSummary['total_absent'] + $attendanceSummary['total_late'] }}</strong> total.
                </p>
            </div>
        @else
            <p class="text-gray-600">Aucun enregistrement de présence pour le moment.</p>
        @endif
    @endif
</div>
