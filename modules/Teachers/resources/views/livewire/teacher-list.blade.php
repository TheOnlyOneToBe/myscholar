<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Gestion des Enseignants</h1>
            <p class="text-gray-600">Consultez et gérez les profils de tous les enseignants</p>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="space-y-4">
                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Rechercher</label>
                    <input type="text" wire:model.live="search" placeholder="Nom, email, matricule, spécialisation..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filtres -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Filière -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filière</label>
                        <select wire:model.live="filiere" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Toutes les filières</option>
                            <option value="generale">Générale</option>
                            <option value="technique">Technique</option>
                        </select>
                    </div>

                    <!-- Spécialisation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Spécialisation</label>
                        <select wire:model.live="specialization" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Toutes les spécialisations</option>
                            <option value="Mathématiques">Mathématiques</option>
                            <option value="Français">Français</option>
                            <option value="Anglais">Anglais</option>
                            <option value="Physique">Physique</option>
                            <option value="Chimie">Chimie</option>
                            <option value="SVT">SVT</option>
                            <option value="Histoire">Histoire</option>
                            <option value="Géographie">Géographie</option>
                        </select>
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select wire:model.live="isActive" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actifs</option>
                            <option value="0">Inactifs</option>
                        </select>
                    </div>

                    <!-- Affichage par page -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Par page</label>
                        <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Bouton Réinitialiser -->
                <div class="flex justify-end">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium transition">
                        🔄 Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Tableau des enseignants -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            @if($teachers->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="setSortBy('created_at')" class="font-semibold text-gray-900 hover:text-blue-600 flex items-center gap-2">
                                        Nom
                                        @if($sortBy === 'created_at')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                @if($sortDirection === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Matricule</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Spécialisation</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Filière</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Expérience</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Matières</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Statut</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($teachers as $teacher)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $teacher->user->first_name }} {{ $teacher->user->last_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $teacher->user->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-mono text-sm">{{ $teacher->teacher_code }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ $teacher->specialization }}</td>
                                    <td class="px-6 py-4">
                                        <span @class(['inline-block px-3 py-1 rounded-full text-sm font-medium',
                                            'bg-blue-100 text-blue-800' => $teacher->filiere === 'generale',
                                            'bg-purple-100 text-purple-800' => $teacher->filiere === 'technique'
                                        ])>
                                            {{ ucfirst($teacher->filiere) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">{{ $teacher->years_of_experience }} ans</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($teacher->subjects()->limit(3)->get() as $subject)
                                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                    {{ $subject->code ?? $subject->name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-500 text-sm">Aucune</span>
                                            @endforelse
                                            @if($teacher->subjects()->count() > 3)
                                                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                                    +{{ $teacher->subjects()->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($teacher->is_active)
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                                ✓ Actif
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                                ✕ Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="/teacher/{{ $teacher->id }}/subjects" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                Matières
                                            </a>
                                            <button class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                                Détails
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    {{ $teachers->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21l-4.35-4.35m0 0A7.5 7.5 0 103.305 3.305a7.5 7.5 0 0010.345 10.345z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun enseignant trouvé</h3>
                    <p class="mt-1 text-gray-600">Essayez de modifier vos critères de recherche ou de filtrage</p>
                </div>
            @endif
        </div>

        <!-- Statistiques -->
        @if($teachers->count())
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $teachers->total() }}</p>
                        <p class="text-sm text-gray-600">Enseignants trouvés</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $teachers->currentPage() }}</p>
                        <p class="text-sm text-gray-600">Page actuelle</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ $teachers->perPage() }}</p>
                        <p class="text-sm text-gray-600">Par page</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600">{{ $teachers->lastPage() }}</p>
                        <p class="text-sm text-gray-600">Total des pages</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
