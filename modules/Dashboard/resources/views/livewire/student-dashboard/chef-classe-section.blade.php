<div class="chef-classe-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">👨‍💼 Tableau de Bord Chef de Classe</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Modules manquants:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        @if(!empty($chefClasseData))
            <!-- Class Overview -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold mb-4">{{ $chefClasseData['class_name'] ?? 'N/A' }} - Gestion</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm opacity-90">Présences à Enregistrer</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['attendance_to_record_count'] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">Justifications en Attente</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['pending_justifications'] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">Moyenne de Classe</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['class_average'] ?? 0 }}/20</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">Taux Présence</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['attendance_rate_class'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons for Chef de Classe -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">📝 Enregistrer les Présences</p>
                    <p class="text-sm text-gray-600">Marquer les élèves présents, absents ou en retard</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-green-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">✅ Approuver Justifications</p>
                    <p class="text-sm text-gray-600">Valider les demandes d'absence justifiées</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-purple-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">📊 Voir Statistiques</p>
                    <p class="text-sm text-gray-600">Notes et performance par sujet</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-orange-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">📧 Communiquer</p>
                    <p class="text-sm text-gray-600">Envoyer des messages à la classe</p>
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-800">
                    <strong>ℹ️ En tant que chef de classe,</strong> vous pouvez accéder à des fonctionnalités supplémentaires
                    pour gérer votre classe, enregistrer les présences, approuver les justifications et consulter les statistiques académiques.
                </p>
            </div>
        @else
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-600">Impossible de charger les données de chef de classe.</p>
            </div>
        @endif
    @endif
</div>
