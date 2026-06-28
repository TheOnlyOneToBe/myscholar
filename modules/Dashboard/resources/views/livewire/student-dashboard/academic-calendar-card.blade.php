<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-calendar-alt mr-2"></i> Calendrier Académique</h3>
        <span class="text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">Mois: {{ date('m/Y') }}</span>
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 rounded"></div>
        </div>
    @else
        <!-- Événements Prochains -->
        <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-thumbtack mr-1"></i> Événements à Venir</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse ($calendar['upcoming_events'] as $event)
                <div class="flex items-start gap-3 p-3 bg-gradient-to-r {{
                    $event['type'] === 'exam' ? 'from-red-50 to-red-100' :
                    ($event['type'] === 'control' ? 'from-orange-50 to-orange-100' :
                    ($event['type'] === 'project' ? 'from-blue-50 to-blue-100' : 'from-green-50 to-green-100'))
                }} rounded-lg border-l-4 {{
                    $event['type'] === 'exam' ? 'border-red-500' :
                    ($event['type'] === 'control' ? 'border-orange-500' :
                    ($event['type'] === 'project' ? 'border-blue-500' : 'border-green-500'))
                }}">
                    <i class="fas {{ $event['icon'] }} text-xl"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $event['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $event['date'] }}</p>
                        @if ($event['description'])
                        <p class="text-sm text-gray-700 mt-1">{{ $event['description'] }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if ($event['days_until'] == 0)
                        <span class="inline-block bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Aujourd'hui</span>
                        @elseif ($event['days_until'] <= 3)
                        <span class="inline-block bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">Dans {{ $event['days_until'] }}j</span>
                        @else
                        <span class="inline-block bg-gray-400 text-white text-xs font-bold px-2 py-1 rounded">{{ $event['days_until'] }} jours</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-600 text-center py-4">Aucun événement prévu</p>
                @endforelse
            </div>
        </div>

        <!-- Examens Prévus -->
        @if (count($calendar['exams_schedule']) > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-file-alt mr-1"></i> Horaire des Examens</h4>
            <div class="space-y-2">
                @foreach ($calendar['exams_schedule'] as $exam)
                <div class="flex items-center justify-between p-3 bg-red-50 border-l-4 border-red-500 rounded {{ $exam['is_soon'] ? 'ring-2 ring-red-300' : '' }}">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $exam['subject'] }}</p>
                        <p class="text-sm text-gray-600">{{ $exam['date'] }} • {{ $exam['time'] }} • Salle {{ $exam['room'] }}</p>
                    </div>
                    @if ($exam['is_soon'])
                    <span class="inline-block bg-red-500 text-white text-xs font-bold px-2 py-1 rounded"><i class="fas fa-clock mr-1"></i> Bientôt!</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Dates Importantes -->
        @if (count($calendar['important_dates']) > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-bullseye mr-1"></i> Dates Importantes</h4>
            <div class="space-y-2">
                @foreach ($calendar['important_dates'] as $date)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded border-l-4 {{
                    $date['status'] === 'in_progress' ? 'border-blue-500 bg-blue-50' :
                    ($date['status'] === 'completed' ? 'border-green-500 bg-green-50' : 'border-gray-500')
                }}">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $date['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $date['start_date'] }} au {{ $date['end_date'] }}</p>
                    </div>
                    <i class="fas {{ $date['icon_class'] }} text-xl"></i>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Vacances -->
        @if (count($calendar['holidays']) > 0)
        <div>
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-party-horn mr-1"></i> Vacances Scolaires</h4>
            <div class="space-y-2">
                @foreach ($calendar['holidays'] as $holiday)
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded border-l-4 border-yellow-500">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $holiday['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $holiday['start_date'] }} au {{ $holiday['end_date'] }} ({{ $holiday['duration'] }} jours)</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif
</div>
