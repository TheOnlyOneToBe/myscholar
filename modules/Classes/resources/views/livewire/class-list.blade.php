<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestion des Classes</h1>
        <button wire:click="openForm()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nouvelle Classe
        </button>
    </div>

    {{-- Filtres --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <input type="text" wire:model.debounce="search" placeholder="Rechercher..." 
            class="border border-gray-300 rounded px-3 py-2">
        
        <select wire:model="level" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Tous les niveaux</option>
            @foreach($levels as $l)
                <option value="{{ $l }}">{{ $l }}</option>
            @endforeach
        </select>

        <select wire:model="filiere" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Toutes les filières</option>
            @foreach($filieres as $f)
                <option value="{{ $f }}">{{ $f }}</option>
            @endforeach
        </select>

        <select wire:model="school_year_id" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Sélectionner année</option>
            @foreach($schoolYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                        wire:click="toggleSort('name')">
                        Nom @if($sort_by === 'name') {{ $sort_order === 'asc' ? '↑' : '↓' }} @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Code
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Niveau
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Filière
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Capacité
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($classes as $class)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $class->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $class->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $class->level }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $class->filiere ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $class->current_students }}/{{ $class->capacity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="openForm({{ $class->id }})" class="text-blue-600 hover:text-blue-900 mr-4">
                                Éditer
                            </button>
                            <button wire:click="confirmDelete({{ $class->id }})" class="text-red-600 hover:text-red-900">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Aucune classe trouvée
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $classes->links() }}
    </div>

    {{-- Modal Formulaire --}}
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Éditer Classe' : 'Nouvelle Classe' }}
                </h3>

                <form wire:submit="saveClass">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" wire:model="form.name" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Code</label>
                            <input type="text" wire:model="form.code" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Niveau</label>
                            <select wire:model="form.level" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option>Sélectionner</option>
                                @foreach($levels as $l)
                                    <option value="{{ $l }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Filière</label>
                            <select wire:model="form.filiere" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option>Sélectionner</option>
                                @foreach($filieres as $f)
                                    <option value="{{ $f }}">{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacité</label>
                            <input type="number" wire:model="form.capacity" class="w-full border border-gray-300 rounded px-3 py-2">
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

    {{-- Confirmation suppression --}}
    @if($deleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
                <p class="text-gray-600 mb-4">Êtes-vous sûr de vouloir supprimer cette classe?</p>
                <div class="flex gap-2">
                    <button wire:click="deleteClass" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
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
