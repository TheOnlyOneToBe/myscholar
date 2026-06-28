<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Tableau de bord - Classes</h1>
    </div>

    {{-- Sélecteur d'année --}}
    <div class="mb-6 flex items-center gap-4">
        <label class="text-sm font-medium text-gray-700">Année scolaire:</label>
        <select wire:model="schoolYearId" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Toutes les années</option>
            @foreach($schoolYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Cartes de statistiques principales --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Nombre total de classes --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total de Classes</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="text-4xl text-blue-200"><i class="fas fa-book"></i></div>
            </div>
        </div>

        {{-- Capacité totale --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Capacité Totale</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['total_capacity'] }}</p>
                </div>
                <div class="text-4xl text-green-200">👥</div>
            </div>
        </div>

        {{-- Étudiants inscrits --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Étudiants Inscrits</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_students'] }}</p>
                </div>
                <div class="text-4xl text-purple-200">✓</div>
            </div>
        </div>

        {{-- Taux d'occupation --}}
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Taux d'Occupation</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['occupancy_rate'] }}%</p>
                </div>
                <div class="text-4xl text-orange-200"><i class="fas fa-chart-bar"></i></div>
            </div>
        </div>
    </div>

    {{-- Grille de distribution --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Distribution par niveau --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribution par Niveau</h3>
            <div class="space-y-3">
                @forelse($stats['by_level'] as $level => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ $level ?? 'Non défini' }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full"
                                    style="width: {{ ($count / max(...array_values($stats['by_level']), 1)) * 100 }}%">
                                </div>
                            </div>
                            <span class="text-gray-900 font-semibold min-w-8">{{ $count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Aucune donnée</p>
                @endforelse
            </div>
        </div>

        {{-- Distribution par filière --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribution par Filière</h3>
            <div class="space-y-3">
                @forelse($stats['by_filiere'] as $filiere => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ $filiere ?? 'Non défini' }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full"
                                    style="width: {{ ($count / max(...array_values($stats['by_filiere']), 1)) * 100 }}%">
                                </div>
                            </div>
                            <span class="text-gray-900 font-semibold min-w-8">{{ $count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Aucune donnée</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Moyenne d'étudiants par classe --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Moyenne d'Étudiants par Classe</h3>
        <div class="flex items-center gap-4">
            <div class="text-4xl font-bold text-indigo-600">{{ $stats['avg_capacity'] }}</div>
            <p class="text-gray-600">étudiants par classe en moyenne</p>
        </div>
    </div>
</div>
