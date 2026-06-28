<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-clock mr-2"></i> Horaire de la Semaine</h3>
        <span class="text-xs bg-teal-100 text-teal-800 px-3 py-1 rounded-full">{{ $schedule['week_start'] }} au {{ $schedule['week_end'] }}</span>
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 rounded"></div>
        </div>
    @else
        <!-- Horaire d'Aujourd'hui -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border-l-4 border-blue-500">
            <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-map-marker-alt mr-1"></i> Aujourd'hui</h4>
            @if ($schedule['today_schedule']['is_weekend'])
            <div class="text-center py-4">
                <p class="text-2xl mb-2"><i class="fas fa-party-horn"></i></p>
                <p class="text-gray-700 font-semibold">{{ $schedule['today_schedule']['message'] }}</p>
            </div>
            @else
            @if (count($schedule['today_schedule']['courses']) == 0)
            <p class="text-gray-700">Aucun cours aujourd'hui</p>
            @else
            <div class="space-y-2">
                @foreach ($schedule['today_schedule']['courses'] as $course)
                <div class="bg-white p-3 rounded border-l-4 {{
                    $course['status'] === 'in_progress' ? 'border-red-500 ring-2 ring-red-300' : 'border-gray-300'
                }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-bold text-gray-800">{{ $course['subject'] }}</p>
                            <p class="text-sm text-gray-600">{{ $course['start_time'] }} - {{ $course['end_time'] }}</p>
                            <p class="text-sm text-gray-700"><i class="fas fa-map-marker-alt mr-1"></i> Salle {{ $course['room'] }} • <i class="fas fa-chalkboard-user mr-1"></i> {{ $course['teacher'] }}</p>
                        </div>
                        <i class="fas {{ $course['icon'] }} text-2xl"></i>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @endif
        </div>

        <!-- Sélection des Jours -->
        <div class="mb-4">
            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach ($schedule['schedule'] as $dayName => $dayData)
                <button
                    wire:click="switchDay('{{ $dayName }}')"
                    class="px-4 py-2 rounded-lg whitespace-nowrap font-semibold transition-all {{
                        $activeDay === $dayName
                            ? 'bg-teal-500 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }} {{
                        $dayData['is_today'] ? 'ring-2 ring-teal-300' : ''
                    }}"
                >
                    {{ substr($dayName, 0, 3) }}
                    <span class="text-xs ml-1">{{ $dayData['day_number'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Horaire du Jour Sélectionné -->
        <div class="bg-gray-50 rounded-lg p-4">
            @php
            $selectedDay = $schedule['schedule'][$activeDay] ?? null;
            @endphp

            @if ($selectedDay)
            <h4 class="font-bold text-gray-800 mb-3">{{ $activeDay }} ({{ $selectedDay['date'] }})</h4>

            @if (count($selectedDay['courses']) == 0)
            <div class="text-center py-6">
                <p class="text-gray-600">Aucun cours ce jour</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach ($selectedDay['courses'] as $course)
                <div class="bg-white p-4 rounded-lg border-l-4 border-teal-500 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h5 class="font-bold text-gray-800 text-lg">{{ $course['subject'] }}</h5>
                            <p class="text-sm text-gray-600"><i class="fas fa-chalkboard-user"></i> {{ $course['teacher'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-teal-600 text-lg">{{ $course['start_time'] }}</p>
                            <p class="text-xs text-gray-600">à {{ $course['end_time'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-700">
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Salle {{ $course['room'] }}</span>
                        <span><i class="fas fa-stopwatch mr-1"></i> {{ $course['duration'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Résumé du Jour -->
            <div class="mt-4 p-3 bg-teal-50 rounded-lg border-l-4 border-teal-500">
                <p class="text-sm text-teal-800">
                    <strong>Résumé:</strong> {{ $selectedDay['total_hours'] }} cours | {{ $selectedDay['date'] }}
                </p>
            </div>
            @endif
        </div>

        <!-- Résumé Hebdomadaire -->
        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
            <div class="p-3 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $schedule['summary']['total_hours'] }}</p>
                <p class="text-xs text-gray-600">Cours par semaine</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg">
                <p class="text-2xl font-bold text-purple-600">{{ $schedule['summary']['unique_subjects'] }}</p>
                <p class="text-xs text-gray-600">Matières</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg">
                <p class="text-2xl font-bold text-orange-600">Jour {{ $schedule['summary']['busiest_day'] }}</p>
                <p class="text-xs text-gray-600">Plus chargé</p>
            </div>
        </div>
    @endif
</div>
