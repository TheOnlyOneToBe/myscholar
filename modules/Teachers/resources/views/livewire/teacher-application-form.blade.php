<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Formulaire d'Inscription Enseignant</h1>

            @if($message)
                <div class="mb-6 p-4 rounded-lg {{ $messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                    {{ $message }}
                </div>
            @endif

            <form wire:submit="submit" class="space-y-6">
                <!-- Section: Informations de base -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations de Base</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spécialisation</label>
                            <input type="text" wire:model="specialization" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="ex: Mathématiques">
                            @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Qualification</label>
                            <select wire:model="qualification_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner...</option>
                                <option value="Bac+2">Bac+2</option>
                                <option value="Bac+3">Bac+3 (Licence)</option>
                                <option value="Bac+5">Bac+5 (Master)</option>
                                <option value="Doctorat">Doctorat</option>
                            </select>
                            @error('qualification_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date d'Embauche</label>
                            <input type="date" wire:model="hire_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('hire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filière</label>
                            <select wire:model="filiere" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner...</option>
                                <option value="generale">Générale</option>
                                <option value="technique">Technique</option>
                            </select>
                            @error('filiere') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Années d'Expérience</label>
                            <input type="number" wire:model.number="years_of_experience" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('years_of_experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bureau / Salle</label>
                            <input type="text" wire:model="office_location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="ex: A-201">
                            @error('office_location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Biographie</label>
                        <textarea wire:model="bio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="4" placeholder="Décrivez votre expérience, vos réalisations..."></textarea>
                        @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Section: Contacts -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Coordonnées Professionnelles</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone du Bureau</label>
                            <input type="tel" wire:model="phone_office" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="+237 xxx xxx xxx">
                            @error('phone_office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email du Bureau</label>
                            <input type="email" wire:model="email_office" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="nom@lycee.cm">
                            @error('email_office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Matières -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Matières Enseignées</h2>

                    @error('selectedSubjects') <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-lg">{{ $message }}</div> @enderror

                    <!-- Matières sélectionnées -->
                    @if($selectedSubjects)
                        <div class="mb-6 space-y-3">
                            @foreach($selectedSubjects as $subjectId)
                                @php
                                    $subject = $subjects->firstWhere('id', $subjectId);
                                @endphp
                                @if($subject)
                                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $subject->name }}</p>
                                            <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
                                                <div>
                                                    <label class="text-gray-600">Niveau:</label>
                                                    <select wire:model="subjectProficiency.{{ $subjectId }}" class="mt-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                                        <option value="1">Débutant (1)</option>
                                                        <option value="2">Intermédiaire (2)</option>
                                                        <option value="3">Compétent (3)</option>
                                                        <option value="4">Expert (4)</option>
                                                        <option value="5">Maître (5)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-gray-600">Depuis:</label>
                                                    <input type="number" wire:model.number="subjectSinceYear.{{ $subjectId }}" min="1900" max="{{ now()->year }}" class="mt-1 px-2 py-1 border border-gray-300 rounded text-sm w-full">
                                                </div>
                                                <div class="flex items-end">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" wire:change="setPrimary({{ $subjectId }})" @checked($subjectIsPrimary[$subjectId] ?? false) class="rounded">
                                                        <span class="ml-2 text-gray-700">Spécialité principale</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="removeSubject({{ $subjectId }})" class="ml-4 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Ajouter une matière -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Ajouter une Matière</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                                <select wire:model="newSubjectId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Sélectionner une matière...</option>
                                    @foreach($subjects as $subject)
                                        @if(!in_array($subject->id, $selectedSubjects))
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Maîtrise</label>
                                <select wire:model.number="newProficiency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="1">Débutant (1)</option>
                                    <option value="2">Intermédiaire (2)</option>
                                    <option value="3">Compétent (3)</option>
                                    <option value="4">Expert (4)</option>
                                    <option value="5">Maître (5)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Depuis l'année</label>
                                <input type="number" wire:model.number="newSinceYear" min="1900" max="{{ now()->year }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.boolean="newIsPrimary" class="rounded">
                                <span class="ml-2 text-gray-700">Ceci est ma spécialité principale</span>
                            </label>
                        </div>
                        <button type="button" wire:click="addSubject" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-plus mr-2"></i>Ajouter cette Matière
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i>Soumettre ma Candidature
                    </button>
                    <a href="/" class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
