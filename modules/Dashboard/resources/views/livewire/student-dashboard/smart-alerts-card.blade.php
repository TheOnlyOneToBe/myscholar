<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">🔔 Alertes Intelligentes</h3>
        @if ($alerts['total_alerts'] > 0)
        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full font-bold text-sm">
            {{ $alerts['critical_count'] }} critique{{ $alerts['critical_count'] > 1 ? 's' : '' }}
        </span>
        @endif
    </div>

    @if ($loading)
        <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 rounded"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
    @else
        @if ($alerts['total_alerts'] == 0)
        <div class="text-center py-8">
            <p class="text-5xl mb-3">✅</p>
            <p class="text-gray-700 font-semibold">Aucune alerte!</p>
            <p class="text-gray-500 text-sm">Tu es en bon chemin. Reste vigilant!</p>
        </div>
        @else
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @foreach ($alerts['alerts'] as $alert)
            <div class="alert-card p-4 rounded-lg border-l-4 transition-all" :class="{
                'bg-red-50 border-red-500': {{ $alert['priority'] }} >= 4,
                'bg-orange-50 border-orange-500': {{ $alert['priority'] }} == 3,
                'bg-yellow-50 border-yellow-500': {{ $alert['priority'] }} < 3
            }">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-start gap-3 flex-1">
                        <span class="text-2xl">{{ $alert['emoji'] }}</span>
                        <div>
                            <h4 class="font-bold {{ $alert['priority'] >= 4 ? 'text-red-800' : ($alert['priority'] == 3 ? 'text-orange-800' : 'text-yellow-800') }}">
                                {{ $alert['title'] }}
                            </h4>
                            <p class="text-sm text-gray-700 mt-1">{{ $alert['message'] }}</p>
                        </div>
                    </div>
                    <button
                        wire:click="dismissAlert('{{ $alert['id'] }}')"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        title="Ignorer"
                    >
                        ✕
                    </button>
                </div>
                <div class="ml-11">
                    <a href="{{ $alert['action_url'] }}" class="inline-block text-sm font-semibold {{ $alert['priority'] >= 4 ? 'text-red-600 hover:text-red-800' : ($alert['priority'] == 3 ? 'text-orange-600 hover:text-orange-800' : 'text-yellow-600 hover:text-yellow-800') }}">
                        {{ $alert['action_label'] }} →
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Légende des Priorités -->
        <div class="mt-4 pt-4 border-t grid grid-cols-3 gap-2 text-xs">
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 bg-red-500 rounded"></span>
                <span class="text-gray-700">Critique</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 bg-orange-500 rounded"></span>
                <span class="text-gray-700">Important</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 bg-yellow-500 rounded"></span>
                <span class="text-gray-700">Info</span>
            </div>
        </div>
        @endif
    @endif
</div>
