<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Exemple de Composant Livewire</h1>
        <p class="text-gray-600 mt-2">Démonstration de l'intégration des permissions avec Livewire</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informations utilisateur -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations Utilisateur</h2>

            @if($currentUser)
                <div class="space-y-2">
                    <p><strong>Nom:</strong> {{ $currentUser->full_name }}</p>
                    <p><strong>Email:</strong> {{ $currentUser->email }}</p>
                    <p><strong>Rôles:</strong> {{ implode(', ', $userRoles) ?: 'Aucun' }}</p>
                </div>
            @else
                <p class="text-gray-500">Aucun utilisateur connecté</p>
            @endif
        </div>

        <!-- Permissions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h2>

            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-sm font-semibold
                        {{ $canViewStudents ? 'bg-green-500' : 'bg-gray-300' }}">
                        {{ $canViewStudents ? '✓' : '✗' }}
                    </span>
                    <span class="ml-3">Voir les étudiants</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-sm font-semibold
                        {{ $canCreateStudents ? 'bg-green-500' : 'bg-gray-300' }}">
                        {{ $canCreateStudents ? '✓' : '✗' }}
                    </span>
                    <span class="ml-3">Créer des étudiants</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-sm font-semibold
                        {{ $isDirecteur ? 'bg-green-500' : 'bg-gray-300' }}">
                        {{ $isDirecteur ? '✓' : '✗' }}
                    </span>
                    <span class="ml-3">Rôle: Directeur</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

        @if($canCreateStudents)
            <button
                wire:click="performAction"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                Exécuter une action
            </button>
        @else
            <p class="text-gray-600 text-sm">
                ⚠️ Vous n'avez pas la permission d'exécuter des actions.
            </p>
        @endif
    </div>

    <!-- Notes -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-2">Comment utiliser les permissions dans Livewire:</h3>
        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
            <li>Utilise <code class="bg-white px-2 py-1 rounded">$this->userCan('permission')</code> pour vérifier une permission</li>
            <li>Utilise <code class="bg-white px-2 py-1 rounded">$this->userHasRole('role')</code> pour vérifier un rôle</li>
            <li>Utilise <code class="bg-white px-2 py-1 rounded">$this->authorize('permission')</code> pour autoriser une action</li>
            <li>Dans les templates Blade, utilise <code class="bg-white px-2 py-1 rounded">@can('permission')</code></li>
        </ul>
    </div>
</div>
