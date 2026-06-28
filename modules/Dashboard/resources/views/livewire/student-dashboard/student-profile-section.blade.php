<div class="profile-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-user mr-2"></i>Mon Profil & Documents</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Module non disponible:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200 flex space-x-8">
            <button
                wire:click="switchTab('profile')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'profile') border-blue-500 text-blue-600 font-semibold @else border-transparent text-gray-600 @endif"
            >
                Informations Personnelles
            </button>
            <button
                wire:click="switchTab('enrollment')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'enrollment') border-blue-500 text-blue-600 font-semibold @else border-transparent text-gray-600 @endif"
            >
                Historique d'Inscription
            </button>
            <button
                wire:click="switchTab('documents')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'documents') border-blue-500 text-blue-600 font-semibold @else border-transparent text-gray-600 @endif"
            >
                Documents Téléchargeables
            </button>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'profile')
            <!-- Personal Information -->
            <div class="space-y-6">
                <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold mb-4">Informations Personnelles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nom Complet</p>
                            <p class="font-semibold text-lg">{{ $studentInfo['full_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Matricule</p>
                            <p class="font-semibold text-lg">{{ $studentInfo['matricule'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date de Naissance</p>
                            <p class="font-semibold">{{ $studentInfo['date_of_birth'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Genre</p>
                            <p class="font-semibold">{{ $studentInfo['gender'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold">{{ $studentInfo['email'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Statut d'Inscription</p>
                            <p class="font-semibold">
                                <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    {{ $studentInfo['enrollment_status'] ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Current Class -->
                <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold mb-4">Classe Actuelle</h3>
                    <p class="font-semibold text-xl text-green-700">{{ $studentInfo['current_class'] ?? 'Aucune classe assignée' }}</p>
                </div>
            </div>

        @elseif($activeTab === 'enrollment')
            <!-- Enrollment History -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Historique d'Inscription</h3>

                @if(!empty($enrollmentHistory))
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Année Académique</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Classe</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Date Inscription</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Moyenne</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Notes</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right">Facturé</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right">Payé</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right">Solde</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollmentHistory as $enrollment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $enrollment['academic_year'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $enrollment['class'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $enrollment['enrollment_date'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center font-semibold text-blue-600">{{ $enrollment['average_grade'] }}/20</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $enrollment['total_grades'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format($enrollment['total_invoiced'], 0) }} FCFA</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right font-semibold text-green-600">{{ number_format($enrollment['total_paid'], 0) }} FCFA</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right font-semibold @if($enrollment['outstanding'] > 0) text-red-600 @else text-green-600 @endif">
                                            {{ number_format($enrollment['outstanding'], 0) }} FCFA
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">Aucun historique d'inscription disponible.</p>
                @endif
            </div>

        @elseif($activeTab === 'documents')
            <!-- Documents Download -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Documents Téléchargeables</h3>
                <p class="text-gray-600 mb-6">Sélectionnez une année académique pour filtrer les documents (optionnel):</p>

                <!-- Year Filter -->
                @if(!empty($enrollmentHistory))
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button
                            wire:click="filterByYear(null)"
                            class="px-4 py-2 rounded-lg @if($selectedYear === null) bg-blue-500 text-white @else bg-gray-200 text-gray-800 @endif"
                        >
                            Tous les documents
                        </button>
                        @foreach($enrollmentHistory as $enrollment)
                            <button
                                wire:click="filterByYear({{ $enrollment['academic_year'] }})"
                                class="px-4 py-2 rounded-lg @if($selectedYear === $enrollment['academic_year']) bg-blue-500 text-white @else bg-gray-200 text-gray-800 @endif"
                            >
                                {{ $enrollment['academic_year'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Documents Grid -->
                @if(!empty($availableDocuments))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableDocuments as $doc)
                            @if($doc['available'])
                                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-lg transition cursor-pointer">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-2xl mb-2">{{ $doc['icon'] }}</p>
                                            <p class="font-semibold text-lg">{{ $doc['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $doc['description'] }}</p>
                                        </div>
                                        <button
                                            wire:click="downloadDocument('{{ $doc['id'] }}'{{ isset($doc['invoice_id']) ? ", '{$doc['invoice_id']}'" : '' }})"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                                        >
                                            📥 Télécharger
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="border-2 border-gray-200 rounded-lg p-4 opacity-50">
                                    <p class="text-2xl mb-2">{{ $doc['icon'] }}</p>
                                    <p class="font-semibold text-lg text-gray-500">{{ $doc['name'] }}</p>
                                    <p class="text-sm text-gray-500">Module indisponible</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <p class="text-gray-600">Aucun document disponible pour le moment.</p>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigate', () => {
        Livewire.on('download', (data) => {
            const { type, studentId, invoiceId } = data[0];

            let url = `/api/dashboard/documents/${type}`;

            if (type.includes('certificate')) {
                url = `/api/dashboard/documents/certificate/${getAcademicYearId()}`;
            } else if (type.includes('report')) {
                url = `/api/dashboard/documents/report-card/${getAcademicYearId()}`;
            } else if (type === 'invoice') {
                url = `/api/dashboard/documents/invoice/${invoiceId}`;
            }

            // Trigger download
            window.location.href = url;
        });

        Livewire.on('alert', (type, message) => {
            alert(message);
        });
    });

    function getAcademicYearId() {
        // This would be retrieved from the selected year
        return 1; // Default or from component state
    }
</script>
@endpush
