<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Affectations - {{ $class->name }}</h1>
        <button wire:click="openForm()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            + Ajouter Enseignant
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($assignments as $assignment)
            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold">{{ $assignment->teacher->name }}</h3>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $assignment->role }}</span>
                    </div>
                </div>
                
                @if($assignment->subject)
                    <p class="text-sm text-gray-600 mb-2"><strong>Matière:</strong> {{ $assignment->subject }}</p>
                @endif
                
                <p class="text-xs text-gray-500 mb-3">Année: {{ $assignment->schoolYear->name }}</p>
                
                <div class="flex gap-2">
                    <button wire:click="openForm({{ $assignment->id }})" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-sm py-2 rounded">
                        Éditer
                    </button>
                    <button wire:click="confirmDelete({{ $assignment->id }})" class="flex-1 bg-red-500 hover:bg-red-700 text-white text-sm py-2 rounded">
                        Retirer
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                Aucun enseignant affecté
            </div>
        @endforelse
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Modifier Affectation' : 'Nouvelle Affectation' }}
                </h3>

                <form wire:submit="saveAssignment">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enseignant</label>
                            <select wire:model="form.user_id" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="">Sélectionner</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('form.user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rôle</label>
                            <select wire:model="form.role" class="w-full border border-gray-300 rounded px-3 py-2">
                                @foreach($roles as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Matière</label>
                            <input type="text" wire:model="form.subject" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Ex: Mathématiques">
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer</h3>
                <p class="text-gray-600 mb-4">Retirer cet enseignant?</p>
                <div class="flex gap-2">
                    <button wire:click="deleteAssignment" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Oui
                    </button>
                    <button wire:click="cancelDelete" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Non
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
