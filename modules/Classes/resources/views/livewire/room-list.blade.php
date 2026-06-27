<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestion des Salles</h1>
        <button wire:click="openForm()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nouvelle Salle
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <input type="text" wire:model.debounce="search" placeholder="Rechercher..." 
            class="border border-gray-300 rounded px-3 py-2">
        
        <select wire:model="building" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Tous les bâtiments</option>
            @foreach($buildings as $b)
                <option value="{{ $b }}">{{ $b }}</option>
            @endforeach
        </select>

        <select wire:model="type" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Tous les types</option>
            @foreach($types as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($rooms as $room)
            <div class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold">{{ $room->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $room->building ?? 'Principal' }}</p>
                    </div>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $room->type }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">Capacité: <strong>{{ $room->capacity }}</strong></p>
                <div class="flex gap-2">
                    <button wire:click="openForm({{ $room->id }})" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-sm py-2 rounded">
                        Éditer
                    </button>
                    <button wire:click="confirmDelete({{ $room->id }})" class="flex-1 bg-red-500 hover:bg-red-700 text-white text-sm py-2 rounded">
                        Supprimer
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                Aucune salle trouvée
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Éditer Salle' : 'Nouvelle Salle' }}
                </h3>

                <form wire:submit="saveRoom">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" wire:model="form.name" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bâtiment</label>
                            <input type="text" wire:model="form.building" class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacité</label>
                            <input type="number" wire:model="form.capacity" class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select wire:model="form.type" class="w-full border border-gray-300 rounded px-3 py-2">
                                @foreach($types as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Enregistrer
                            </button>
                            <button type="button" wire:click="closeForm" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($deleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-80 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
                <p class="text-gray-600 mb-4">Êtes-vous sûr?</p>
                <div class="flex gap-2">
                    <button wire:click="deleteRoom" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Supprimer
                    </button>
                    <button wire:click="cancelDelete" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
