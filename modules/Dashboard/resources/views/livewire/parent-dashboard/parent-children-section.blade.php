<div class="bg-white rounded-lg shadow p-6 ml-64">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-users mr-2 text-purple-600"></i>Mes Enfants</h2>

    @if(!empty($children))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($children as $child)
                <div
                    wire:click="selectChild({{ $child['id'] }})"
                    class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 @if($selectedChildId === $child['id']) border-purple-600 bg-purple-50 @else border-purple-200 @endif rounded-lg p-5 cursor-pointer hover:shadow-lg transition"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $child['full_name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $child['student_id'] }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            {{ substr($child['first_name'], 0, 1) }}
                        </div>
                    </div>

                    <div class="space-y-2 text-sm border-t border-purple-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600"><i class="fas fa-classroom mr-2"></i>Classe</span>
                            <span class="font-semibold text-gray-800">{{ $child['current_class'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600"><i class="fas fa-check-circle mr-2"></i>Statut</span>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold @if($child['enrollment_status'] === 'active') bg-green-100 text-green-800 @elseif($child['enrollment_status'] === 'inactive') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($child['enrollment_status']) }}
                            </span>
                        </div>
                    </div>

                    @if($selectedChildId === $child['id'])
                        <div class="mt-4 pt-4 border-t border-purple-200">
                            <p class="text-xs text-purple-600 font-semibold">
                                <i class="fas fa-arrow-right mr-1"></i>Enfant sélectionné
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
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
