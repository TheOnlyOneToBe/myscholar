<div class="class-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">🏫 Ma Classe</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Module non disponible:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        @if(!empty($classInfo))
            <!-- Class Information -->
            <div class="bg-blue-50 p-6 rounded-lg mb-6 border-l-4 border-blue-500">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Classe</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $classInfo['name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Code</p>
                        <p class="text-xl font-semibold">{{ $classInfo['code'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Responsable de Classe</p>
                        <p class="font-semibold">{{ $classInfo['form_tutor'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Effectif</p>
                        <p class="text-lg font-bold">{{ $classInfo['student_count'] }} élèves</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg hover:shadow-lg transition">
                    <p class="font-semibold">📅 Emploi du Temps</p>
                    <p class="text-sm mt-2">Consultez votre calendrier</p>
                </a>
                <a href="#" class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg hover:shadow-lg transition">
                    <p class="font-semibold">👥 Camarades</p>
                    <p class="text-sm mt-2">Voir les élèves de votre classe</p>
                </a>
                <a href="#" class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg hover:shadow-lg transition">
                    <p class="font-semibold">📢 Annonces</p>
                    <p class="text-sm mt-2">Actualités de la classe</p>
                </a>
            </div>
        @else
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-600">Aucune classe assignée pour le moment.</p>
            </div>
        @endif
    @endif
</div>
