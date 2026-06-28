<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Gestion de Mes Matières</h1>

            <!-- Matières actuelles -->
            @if($subjects->count())
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Mes Matières ({{ $subjects->count() }})</h2>
                    <div class="space-y-4">
                        @foreach($subjects as $subject)
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $subject->name }}</h3>
                                        @if($subject->pivot->is_primary)
                                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                Spécialité Principale
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Niveau de Maîtrise</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $subject->pivot->proficiency_level }}/5
                                                @if($subject->pivot->proficiency_level == 5)
                                                    (Maître)
                                                @elseif($subject->pivot->proficiency_level == 4)
                                                    (Expert)
                                                @elseif($subject->pivot->proficiency_level == 3)
                                                    (Compétent)
                                                @elseif($subject->pivot->proficiency_level == 2)
                                                    (Intermédiaire)
                                                @else
                                                    (Débutant)
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Enseigne Depuis</p>
                                            <p class="font-medium text-gray-900">{{ $subject->pivot->since_year }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Années d'Enseignement</p>
                                            <p class="font-medium text-gray-900">{{ now()->year - $subject->pivot->since_year }} ans</p>
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="removeSubject({{ $subject->id }})" class="ml-4 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800">Vous n'avez pas encore déclaré de matières. Veuillez en ajouter.</p>
                </div>
            @endif

            <!-- Ajouter une matière -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ajouter une Nouvelle Matière</h2>

                <form wire:submit="addSubject" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                            <select wire:model="newSubjectId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner une matière...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('newSubjectId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Maîtrise</label>
                            <select wire:model.number="newProficiency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="1">1 - Débutant</option>
                                <option value="2">2 - Intermédiaire</option>
                                <option value="3">3 - Compétent</option>
                                <option value="4">4 - Expert</option>
                                <option value="5">5 - Maître</option>
                            </select>
                            @error('newProficiency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Année de Début</label>
                            <input type="number" wire:model.number="newSinceYear" min="1900" max="{{ now()->year }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('newSinceYear') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center cursor-pointer gap-2">
                                <input type="checkbox" wire:model.boolean="newIsPrimary" class="rounded">
                                <span class="text-gray-700 font-medium">Spécialité principale</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                        + Ajouter cette Matière
                    </button>
                </form>
            </div>

            <!-- Informations utiles -->
            <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-blue-900 mb-3">💡 Conseil</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-2 text-sm">
                    <li>Déclarez toutes les matières que vous pouvez enseigner</li>
                    <li>Indiquez votre niveau de maîtrise honnêtement (1-5)</li>
                    <li>Marquez votre spécialité principale (celle dans laquelle vous êtes expert)</li>
                    <li>Une fois validée par l'admin, votre profil apparaîtra aux coordinateurs pour les attributions de classes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
