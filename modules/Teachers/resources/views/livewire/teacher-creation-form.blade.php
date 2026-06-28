<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Ajouter un Enseignant</h1>
            <p class="text-gray-600 mb-8">Créer un nouvel enseignant en utilisant un utilisateur existant ou nouveau</p>

            @if($message)
                <div class="mb-6 p-4 rounded-lg {{ $messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
                    {{ $message }}
                </div>
            @endif

            <!-- Sélection du mode -->
            <div class="flex gap-4 mb-8 border-b">
                <button
                    wire:click="$set('mode', 'new')"
                    @class(['px-6 py-3 font-medium border-b-2 transition',
                        'border-blue-600 text-blue-600' => $mode === 'new',
                        'border-transparent text-gray-600 hover:text-gray-900' => $mode !== 'new'
                    ])>
                    <i class="fas fa-user-plus mr-2"></i>Créer un Nouvel Utilisateur
                </button>
                <button
                    wire:click="$set('mode', 'existing')"
                    @class(['px-6 py-3 font-medium border-b-2 transition',
                        'border-blue-600 text-blue-600' => $mode === 'existing',
                        'border-transparent text-gray-600 hover:text-gray-900' => $mode !== 'existing'
                    ])>
                    <i class="fas fa-user-check mr-2"></i>Utiliser un Utilisateur Existant
                </button>
            </div>

            <form wire:submit="submit" class="space-y-8">
                <!-- Section: Utilisateur -->
                <div class="border-b pb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">
                        {{ $mode === 'new' ? 'Informations Utilisateur' : 'Sélectionner un Utilisateur' }}
                    </h2>

                    @if($mode === 'new')
                        <!-- Création d'utilisateur -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                                <input type="text" wire:model="firstName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('firstName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                                <input type="text" wire:model="lastName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('lastName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'Utilisateur *</label>
                                <input type="text" wire:model="username" placeholder="ex: john.doe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mot de Passe *</label>
                                <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le Mot de Passe *</label>
                                <input type="password" wire:model="passwordConfirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('passwordConfirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <input type="tel" wire:model="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @else
                        <!-- Sélection d'utilisateur existant -->
                        <div class="space-y-4">
                            @if($userId)
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $firstName }} {{ $lastName }}</p>
                                            <p class="text-sm text-gray-600">{{ $email }}</p>
                                            <p class="text-sm text-gray-600">@{{ $username }}</p>
                                        </div>
                                        <button type="button" wire:click="clearUserSelection" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times mr-1"></i>Changer
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un Utilisateur *</label>
                                    <input
                                        type="text"
                                        wire:model.live="searchUser"
                                        placeholder="Rechercher par nom, email, ou username..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('userId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if($searchUser)
                                    <div class="mt-3 space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg divide-y">
                                        @forelse($availableUsers as $user)
                                            <button
                                                type="button"
                                                wire:click="selectUser({{ $user->id }})"
                                                class="w-full text-left p-4 hover:bg-gray-50 transition"
                                            >
                                                <p class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                                <p class="text-xs text-gray-500">@{{ $user->username }}</p>
                                            </button>
                                        @empty
                                            <div class="p-4 text-center text-gray-500">
                                                Aucun utilisateur trouvé
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Section: Infos Enseignant -->
                <div class="border-b pb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Informations Enseignant</h2>
                        <button type="button" wire:click="generateTeacherCode" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-sync-alt mr-1"></i>Générer un Matricule
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Matricule (Teacher Code) *</label>
                            <input type="text" wire:model="teacherCode" placeholder="PROF-2026-0001" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
                            @error('teacherCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spécialisation *</label>
                            <input type="text" wire:model="specialization" placeholder="ex: Mathématiques" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Qualification *</label>
                            <select wire:model="qualificationLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner...</option>
                                <option value="Bac+2">Bac+2</option>
                                <option value="Bac+3">Bac+3 (Licence)</option>
                                <option value="Bac+5">Bac+5 (Master)</option>
                                <option value="Doctorat">Doctorat</option>
                            </select>
                            @error('qualificationLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date d'Embauche</label>
                            <input type="date" wire:model="hireDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('hireDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Années d'Expérience *</label>
                            <input type="number" wire:model.number="yearsOfExperience" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('yearsOfExperience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bureau / Salle</label>
                            <input type="text" wire:model="officeLocation" placeholder="ex: A-201" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('officeLocation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biographie</label>
                            <textarea wire:model="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone Bureau</label>
                            <input type="tel" wire:model="phoneOffice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('phoneOffice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Bureau</label>
                            <input type="email" wire:model="emailOffice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('emailOffice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-check mr-2"></i>Créer l'Enseignant
                    </button>
                    <a href="/teachers" class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
