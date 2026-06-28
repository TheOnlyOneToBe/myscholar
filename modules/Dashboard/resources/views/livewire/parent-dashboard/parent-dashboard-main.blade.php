<div class="space-y-6 ml-64">
    <!-- Global Stats Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6"><i class="fas fa-chart-pie mr-2 text-blue-600"></i>Résumé Global</h2>

        @if(!empty($children))
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total Children -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600 mb-2">Total d'Enfants</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $globalStats['total_children'] ?? 0 }}</p>
                </div>

                <!-- Average Performance -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm text-gray-600 mb-2">Performance Moyenne</p>
                    <p class="text-3xl font-bold text-green-600">{{ $globalStats['average_performance'] ?? 0 }}/20</p>
                </div>

                <!-- Outstanding Balance -->
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="text-sm text-gray-600 mb-2">Solde Impayé</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($globalStats['total_outstanding_balance'] ?? 0, 0) }} FCFA</p>
                </div>

                <!-- Total Absences -->
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="text-sm text-gray-600 mb-2">Total Absences</p>
                    <p class="text-3xl font-bold text-red-600">{{ $globalStats['total_absences'] ?? 0 }}</p>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Aucun enfant associé à votre compte parent.
                </p>
            </div>
        @endif
    </div>

    <!-- Alerts Section -->
    @if(!empty($alerts))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6"><i class="fas fa-bell mr-2 text-yellow-600"></i>Alertes Actives</h2>

        <div class="space-y-3">
            @foreach($alerts as $alert)
                <div class="flex items-start space-x-4 p-4 border-l-4 @if($alert['severity'] === 'danger') border-red-500 bg-red-50 @elseif($alert['severity'] === 'warning') border-yellow-500 bg-yellow-50 @else border-blue-500 bg-blue-50 @endif rounded">
                    <div class="flex-shrink-0">
                        <i class="fas {{ $alert['icon'] }} w-6 h-6 @if($alert['severity'] === 'danger') text-red-600 @elseif($alert['severity'] === 'warning') text-yellow-600 @else text-blue-600 @endif"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold @if($alert['severity'] === 'danger') text-red-800 @elseif($alert['severity'] === 'warning') text-yellow-800 @else text-blue-800 @endif">
                            {{ $alert['student'] }}
                        </p>
                        <p class="text-sm @if($alert['severity'] === 'danger') text-red-700 @elseif($alert['severity'] === 'warning') text-yellow-700 @else text-blue-700 @endif">
                            {{ $alert['message'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Children Overview Section -->
    @if(!empty($children))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6"><i class="fas fa-users mr-2 text-purple-600"></i>Mes Enfants</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($children as $child)
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-5 cursor-pointer hover:shadow-lg transition" wire:click="selectChild({{ $child['id'] }})">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $child['full_name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $child['student_id'] }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($child['first_name'], 0, 1) }}
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p class="text-gray-700">
                            <span class="font-semibold">Classe:</span> {{ $child['current_class'] ?? 'N/A' }}
                        </p>
                        <p class="text-gray-700">
                            <span class="font-semibold">Statut:</span>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold @if($child['enrollment_status'] === 'active') bg-green-100 text-green-800 @elseif($child['enrollment_status'] === 'inactive') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($child['enrollment_status']) }}
                            </span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
