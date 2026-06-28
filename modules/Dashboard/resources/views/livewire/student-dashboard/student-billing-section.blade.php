<div class="billing-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-money-bill-wave mr-2"></i>Ma Facturation</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Module non disponible:</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        <!-- Financial Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-600">Solde Impayé</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($outstandingBalance, 0) }} FCFA</p>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                <p class="text-sm text-gray-600">Factures Impayées</p>
                <p class="text-2xl font-bold text-orange-600">{{ $overdueCount }}</p>
            </div>
        </div>

        <!-- Upcoming Payments -->
        @if(!empty($upcomingPayments))
            <div>
                <h3 class="text-lg font-semibold mb-4">Paiements à Venir</h3>
                <div class="space-y-3">
                    @foreach($upcomingPayments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4 @if($payment['is_overdue']) bg-red-50 @else bg-gray-50 @endif">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">Facture #{{ $payment['number'] }}</p>
                                    <p class="text-sm text-gray-600">Échéance: {{ $payment['due_date'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600">{{ number_format($payment['amount'], 0) }} FCFA</p>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($payment['is_overdue']) bg-red-100 text-red-800
                                        @elseif($payment['status'] === 'paid') bg-green-100 text-green-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        @if($payment['is_overdue']) EN RETARD @else {{ strtoupper($payment['status']) }} @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <p class="text-green-800"><i class="fas fa-check-circle"></i> Aucune facture impayée. Excellent!</p>
            </div>
        @endif
    @endif
</div>
