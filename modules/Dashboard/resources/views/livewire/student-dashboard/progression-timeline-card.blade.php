<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">📈 Timeline de Progression (6 mois)</h3>
        <span class="text-sm font-semibold" :class="{
            'text-green-600': '{{ $timeline['trend']['direction'] }}' === 'up',
            'text-red-600': '{{ $timeline['trend']['direction'] }}' === 'down',
            'text-gray-600': '{{ $timeline['trend']['direction'] }}' === 'stable'
        }">
            @if ($timeline['trend']['direction'] === 'up')
                📈 +{{ $timeline['trend']['percentage'] }}%
            @elseif ($timeline['trend']['direction'] === 'down')
                📉 {{ $timeline['trend']['percentage'] }}%
            @else
                ➡️ Stable
            @endif
        </span>
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-32 bg-gray-200 rounded"></div>
        </div>
    @else
        <!-- Status Badge -->
        <div class="mb-4 p-3 rounded-lg" :class="{
            'bg-green-50 border-l-4 border-green-500': '{{ $timeline['progress_status'] }}' === 'excellent',
            'bg-blue-50 border-l-4 border-blue-500': '{{ $timeline['progress_status'] }}' === 'stable',
            'bg-orange-50 border-l-4 border-orange-500': '{{ $timeline['progress_status'] }}' === 'concerning'
        }">
            <p class="text-sm font-semibold" :class="{
                'text-green-800': '{{ $timeline['progress_status'] }}' === 'excellent',
                'text-blue-800': '{{ $timeline['progress_status'] }}' === 'stable',
                'text-orange-800': '{{ $timeline['progress_status'] }}' === 'concerning'
            }">
                @if ($timeline['progress_status'] === 'excellent')
                    ✅ Progression Excellente - Continue comme ça!
                @elseif ($timeline['progress_status'] === 'stable')
                    ➡️ Progression Stable - Reste vigilant
                @else
                    ⚠️ Progression en Baisse - Intensifie tes efforts
                @endif
            </p>
        </div>

        <!-- Monthly Chart (Text-based) -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="space-y-2">
                @foreach ($timeline['monthly_averages'] as $month)
                <div class="flex items-center gap-4">
                    <div class="w-20 text-sm font-semibold text-gray-700">{{ $month['month'] }}</div>
                    <div class="flex-1 bg-gray-200 rounded-full h-6 flex items-center relative">
                        @if ($month['average'])
                        <div class="bg-blue-500 h-6 rounded-full flex items-center justify-center" style="width: {{ ($month['average'] / 20) * 100 }}%">
                            <span class="text-xs font-bold text-white px-2">{{ $month['average'] }}</span>
                        </div>
                        @else
                        <span class="text-xs text-gray-500 px-2">-</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Timeline Events -->
        <div class="mb-4">
            <h4 class="font-semibold text-gray-800 mb-3">📌 Dernières Notes</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse ($timeline['timeline_events'] as $event)
                <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded">
                    <span class="text-xl">{{ $event['emoji'] }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $event['subject'] }}</p>
                        <p class="text-xs text-gray-600">{{ $event['date'] }}</p>
                    </div>
                    <span class="font-bold text-lg" :class="{
                        'text-green-600': {{ $event['score'] }} >= 16,
                        'text-orange-600': {{ $event['score'] }} >= 12,
                        'text-red-600': {{ $event['score'] }} < 12
                    }">{{ $event['score'] }}/20</span>
                </div>
                @empty
                <p class="text-sm text-gray-600">Aucune note enregistrée</p>
                @endforelse
            </div>
        </div>

        <!-- Current Average -->
        <div class="p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
            <div class="flex items-center justify-between">
                <span class="text-gray-700 font-semibold">Moyenne Actuelle</span>
                <span class="text-2xl font-bold text-blue-600">{{ $timeline['current_average'] }}/20</span>
            </div>
        </div>
    @endif
</div>
