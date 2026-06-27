<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Configuration du Lycée</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Gérez les informations et paramètres de votre établissement</p>
        </div>
        @can('config.school_info.edit')
            <button wire:click="toggleEditMode"
                    class="px-4 py-2 {{ $editMode ? 'bg-gray-500' : 'bg-blue-600' }} text-white rounded-lg hover:{{ $editMode ? 'bg-gray-600' : 'bg-blue-700' }} transition">
                {{ $editMode ? 'Annuler' : 'Modifier' }}
            </button>
        @endcan
    </div>

    <!-- School Logo and Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Logo Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Logo de l'établissement</h2>
            @if ($schoolInfo?->hasLogo())
                <img src="{{ asset($schoolInfo->logo_path) }}"
                     alt="{{ $schoolInfo->name }}"
                     class="w-full h-32 object-contain mb-4 bg-gray-100 dark:bg-gray-700 rounded p-2">
            @else
                <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center mb-4">
                    <span class="text-gray-400">Aucun logo</span>
                </div>
            @endif
            @can('config.school_info.logo')
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Changer le logo
                </button>
            @endcan
        </div>

        <!-- School Name and Acronym -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Informations Générales</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom de l'établissement *
                        </label>
                        <input type="text" wire:model="formData.name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sigle/Acronyme
                        </label>
                        <input type="text" wire:model="formData.acronym"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nom</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $schoolInfo?->name ?? 'Non configuré' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Acronyme</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->acronym ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Type and Motto -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Type et Devise</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type d'établissement *
                        </label>
                        <select wire:model="formData.school_type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="public">Public</option>
                            <option value="prive">Privé</option>
                            <option value="confessionnel">Confessionnel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Devise/Motto
                        </label>
                        <input type="text" wire:model="formData.motto"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Type</p>
                        <p class="text-gray-900 dark:text-white capitalize">
                            {{ $schoolInfo?->school_type ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Devise</p>
                        <p class="text-gray-900 dark:text-white italic">{{ $schoolInfo?->motto ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Address and Contact -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Address -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-map-marker-alt"></i> Localisation</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                        <input type="text" wire:model="formData.address"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville</label>
                            <input type="text" wire:model="formData.city"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Région</label>
                            <input type="text" wire:model="formData.region"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Boîte Postale</label>
                        <input type="text" wire:model="formData.po_box"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->getFullAddress() ?? '—' }}</p>
                    @if ($schoolInfo?->po_box)
                        <p class="text-gray-600 dark:text-gray-400 text-sm">B.P. {{ $schoolInfo->po_box }}</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Contact Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-phone"></i> Contact</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                        <input type="tel" wire:model="formData.phone"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" wire:model="formData.email"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Web</label>
                        <input type="url" wire:model="formData.website"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Téléphone</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Site Web</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->website ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Administrative Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-clipboard"></i> Informations Administratives</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° d'Agrément</label>
                        <input type="text" wire:model="formData.approval_number"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arrêté de Création</label>
                        <input type="text" wire:model="formData.creation_decree"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">N° d'Agrément</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->approval_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Arrêté de Création</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->creation_decree ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Historical Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-history"></i> Historique</h2>
            @if ($editMode)
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fondateur</label>
                        <input type="text" wire:model="formData.founder_name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Année de Fondation</label>
                            <input type="number" wire:model="formData.foundation_year" min="1900" max="2100"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Directeur</label>
                            <input type="text" wire:model="formData.director_name"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Fondateur</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->founder_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Année de Fondation</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->foundation_year ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Directeur Actuel</p>
                        <p class="text-gray-900 dark:text-white">{{ $schoolInfo?->director_name ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- School Year Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-calendar-alt"></i> Année Scolaire Active</h2>
        @if ($currentSchoolYear)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900 rounded p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Année</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $currentSchoolYear['year'] }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900 rounded p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Label</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $currentSchoolYear['label'] }}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900 rounded p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Début</p>
                    <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($currentSchoolYear['start_date'])->format('d/m/Y') }}</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900 rounded p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Fin</p>
                    <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($currentSchoolYear['end_date'])->format('d/m/Y') }}</p>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>Aucune année scolaire active</p>
            </div>
        @endif
    </div>

    <!-- Save Button -->
    @if ($editMode)
        <div class="flex gap-4">
            <button wire:click="updateSchoolInfo"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                Enregistrer les modifications
            </button>
            <button wire:click="toggleEditMode"
                    class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition">
                Annuler
            </button>
        </div>
    @endif
</div>
