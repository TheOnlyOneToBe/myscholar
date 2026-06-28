<div class="bg-white rounded-lg shadow p-6 ml-64">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-file-pdf mr-2 text-red-600"></i>Bulletins de {{ $childName }}</h2>

    @if(!empty($bulletins))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($bulletins as $bulletin)
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition bg-gradient-to-br from-gray-50 to-white">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $bulletin['name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $bulletin['type'] }}</p>
                        </div>
                        <i class="fas fa-file-pdf text-3xl text-red-600"></i>
                    </div>

                    <div class="space-y-2 text-sm mb-4 border-t border-gray-200 pt-4">
                        <p class="text-gray-700">
                            <span class="font-semibold">Période:</span> {{ $bulletin['start_date'] }} - {{ $bulletin['end_date'] }}
                        </p>
                        <p class="text-gray-700">
                            <span class="font-semibold">Statut:</span>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold @if($bulletin['status'] === 'completed') bg-green-100 text-green-800 @elseif($bulletin['status'] === 'current') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                                @if($bulletin['status'] === 'completed')
                                    <i class="fas fa-check-circle mr-1"></i>Terminé
                                @elseif($bulletin['status'] === 'current')
                                    <i class="fas fa-hourglass-half mr-1"></i>En cours
                                @else
                                    <i class="fas fa-clock mr-1"></i>À venir
                                @endif
                            </span>
                        </p>
                    </div>

                    @if($bulletin['status'] === 'completed')
                        <button
                            wire:click="downloadBulletin({{ $bulletin['id'] }})"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold"
                        >
                            <i class="fas fa-download mr-2"></i>Télécharger
                        </button>
                    @else
                        <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed font-semibold">
                            <i class="fas fa-lock mr-2"></i>Non disponible
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-file-pdf text-4xl text-yellow-600 mb-4"></i>
            <p class="text-lg font-semibold text-yellow-800">Aucun bulletin disponible</p>
            <p class="text-sm text-yellow-700 mt-2">Les bulletins n'ont pas encore été générés pour cette période académique.</p>
        </div>
    @endif
</div>
