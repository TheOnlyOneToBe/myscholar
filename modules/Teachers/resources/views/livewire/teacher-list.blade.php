<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('teachers::views.titles.teacher_list') }}</h1>
            <p class="text-gray-600">{{ __('teachers::views.descriptions.manage_teachers') }}</p>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="space-y-4">
                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-search mr-2"></i>{{ __('teachers::views.buttons.search') }}</label>
                    <input type="text" wire:model.live="search" placeholder="{{ __('teachers::views.placeholders.search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filtres -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Filière -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.filters.filiere') }}</label>
                        <select wire:model.live="filiere" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('teachers::views.filters.all_fieres') }}</option>
                            <option value="generale">{{ __('teachers::views.fieres.generale') }}</option>
                            <option value="technique">{{ __('teachers::views.fieres.technique') }}</option>
                        </select>
                    </div>

                    <!-- Spécialisation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.filters.specialization') }}</label>
                        <select wire:model.live="specialization" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('teachers::views.filters.all_specializations') }}</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.filters.status') }}</label>
                        <select wire:model.live="isActive" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('teachers::views.filters.all_statuses') }}</option>
                            <option value="1">{{ __('teachers::views.filters.active') }}</option>
                            <option value="0">{{ __('teachers::views.filters.inactive') }}</option>
                        </select>
                    </div>

                    <!-- Affichage par page -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.filters.per_page') }}</label>
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
                        <i class="fas fa-redo mr-2"></i>{{ __('teachers::views.buttons.reset_filters') }}
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
                                        {{ __('teachers::views.labels.name') }}
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
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.teacher_code') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.specialization') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.filiere') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.years_of_experience') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.subjects') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::views.labels.status') }}</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-900">{{ __('teachers::messages.actions.view') }}</th>
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
                                                <span class="text-gray-500 text-sm">{{ __('teachers::views.messages.no_subjects') }}</span>
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
                                                <i class="fas fa-check-circle mr-1"></i>{{ __('teachers::views.statuses.active') }}
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                                <i class="fas fa-times-circle mr-1"></i>{{ __('teachers::views.statuses.inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="/teacher/{{ $teacher->id }}/subjects" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                <i class="fas fa-book mr-1"></i>{{ __('teachers::views.buttons.view_subjects') }}
                                            </a>
                                            <button class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                                <i class="fas fa-info-circle mr-1"></i>{{ __('teachers::views.buttons.view_details') }}
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
                    <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ __('teachers::views.messages.no_teachers_found') }}</h3>
                    <p class="mt-1 text-gray-600">{{ __('teachers::views.messages.try_modifying_search') }}</p>
                </div>
            @endif
        </div>

        <!-- Statistiques -->
        @if($teachers->count())
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('teachers::views.summary.summary') }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $teachers->total() }}</p>
                        <p class="text-sm text-gray-600">{{ __('teachers::views.summary.found_teachers') }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $teachers->currentPage() }}</p>
                        <p class="text-sm text-gray-600">{{ __('teachers::views.summary.current_page') }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ $teachers->perPage() }}</p>
                        <p class="text-sm text-gray-600">{{ __('teachers::views.summary.per_page_label') }}</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600">{{ $teachers->lastPage() }}</p>
                        <p class="text-sm text-gray-600">{{ __('teachers::views.summary.total_pages') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
