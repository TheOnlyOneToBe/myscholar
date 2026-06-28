<div class="bg-white rounded-lg shadow p-6 ml-64">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold"><i class="fas fa-bell mr-2 text-yellow-600"></i>Alertes Système</h2>
        <button
            wire:click="refreshAlerts"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"
        >
            <i class="fas fa-sync"></i>
            <span>Actualiser</span>
        </button>
    </div>

    @if(!empty($alerts))
        <div class="space-y-4">
            @foreach($alerts as $alert)
                <div class="flex items-start space-x-4 p-4 border-l-4 @if($alert['severity'] === 'danger') border-red-500 bg-red-50 @elseif($alert['severity'] === 'warning') border-yellow-500 bg-yellow-50 @else border-blue-500 bg-blue-50 @endif rounded-lg">
                    <div class="flex-shrink-0 mt-1">
                        <i class="fas {{ $alert['icon'] }} w-6 h-6 @if($alert['severity'] === 'danger') text-red-600 @elseif($alert['severity'] === 'warning') text-yellow-600 @else text-blue-600 @endif"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold @if($alert['severity'] === 'danger') text-red-800 @elseif($alert['severity'] === 'warning') text-yellow-800 @else text-blue-800 @endif">
                            {{ $alert['student'] }}
                        </p>
                        <p class="text-sm @if($alert['severity'] === 'danger') text-red-700 @elseif($alert['severity'] === 'warning') text-yellow-700 @else text-blue-700 @endif">
                            {{ $alert['message'] }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold @if($alert['severity'] === 'danger') bg-red-200 text-red-800 @elseif($alert['severity'] === 'warning') bg-yellow-200 text-yellow-800 @else bg-blue-200 text-blue-800 @endif">
                            @if($alert['severity'] === 'danger')
                                Urgent
                            @elseif($alert['severity'] === 'warning')
                                Attention
                            @else
                                Info
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <i class="fas fa-check-circle text-4xl text-green-600 mb-4"></i>
            <p class="text-lg font-semibold text-green-800">Aucune alerte</p>
            <p class="text-sm text-green-700 mt-2">Tout va bien! Aucun problème détecté pour vos enfants.</p>
        </div>
    @endif
</div>
