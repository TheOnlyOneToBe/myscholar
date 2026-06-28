<div class="bg-white rounded-lg shadow p-6 ml-64">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-folder-open mr-2 text-purple-600"></i>Documents de {{ $childName }}</h2>

    <!-- Year Filter -->
    @if(!empty($academicYears))
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-600 mb-3">Filtrer par année académique</h3>
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="selectYear(null)"
                class="px-4 py-2 rounded-lg font-semibold transition @if(is_null($selectedYear)) bg-blue-600 text-white @else bg-gray-200 text-gray-800 hover:bg-gray-300 @endif"
            >
                Toutes les années
            </button>
            @foreach($academicYears as $year)
                <button
                    wire:click="selectYear({{ $year }})"
                    class="px-4 py-2 rounded-lg font-semibold transition @if($selectedYear === $year) bg-blue-600 text-white @else bg-gray-200 text-gray-800 hover:bg-gray-300 @endif"
                >
                    {{ $year }}-{{ $year + 1 }}
                </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Documents -->
    @if(!empty($availableDocuments))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($availableDocuments as $document)
            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition @if(!$document['available']) opacity-50 @endif bg-gradient-to-br from-gray-50 to-white">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $document['name'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $document['description'] }}</p>
                    </div>
                    <i class="fas {{ $document['icon'] }} text-3xl @if($document['available']) text-blue-600 @else text-gray-400 @endif"></i>
                </div>

                @if($document['available'])
                    <button
                        @if(str_starts_with($document['id'], 'invoice_'))
                            wire:click="downloadDocument('invoice', {{ $document['invoice_id'] ?? null }})"
                        @else
                            wire:click="downloadDocument('{{ $document['id'] }}', {{ $selectedYear ?? null }})"
                        @endif
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
            <i class="fas fa-folder-open text-4xl text-yellow-600 mb-4"></i>
            <p class="text-lg font-semibold text-yellow-800">Aucun document disponible</p>
            <p class="text-sm text-yellow-700 mt-2">Aucun document n'a été généré pour cette période.</p>
        </div>
    @endif
</div>
