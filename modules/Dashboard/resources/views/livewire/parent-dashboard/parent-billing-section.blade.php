<div class="bg-white rounded-lg shadow p-6 ml-64">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold"><i class="fas fa-money-bill-wave mr-2 text-orange-600"></i>Facturation de {{ $childName }}</h2>
        <div class="text-right bg-orange-50 px-4 py-2 rounded-lg border border-orange-200">
            <p class="text-sm text-gray-600">Solde Impayé</p>
            <p class="text-2xl font-bold text-orange-600">{{ number_format($outstandingBalance, 0) }} FCFA</p>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Outstanding Invoices -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Factures Impayées</h3>
            @if(!empty($outstandingInvoices))
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Numéro</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Montant</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Échéance</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Statut</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outstandingInvoices as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $invoice['invoice_number'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right font-semibold">{{ number_format($invoice['amount'], 0) }} FCFA</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $invoice['due_date'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        @if($invoice['is_overdue'])
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>En retard
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($invoice['status']) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <button class="text-blue-600 hover:text-blue-800 font-semibold">
                                            <i class="fas fa-file-pdf mr-1"></i>Voir
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Aucune facture impayée. Excellent travail !
                    </p>
                </div>
            @endif
        </div>

        <!-- Recent Payments -->
        @if(!empty($recentPayments))
        <div>
            <h3 class="text-lg font-semibold mb-4">Paiements Récents</h3>
            <div class="space-y-3">
                @foreach($recentPayments as $payment)
                    <div class="bg-green-50 border border-green-200 rounded p-4 flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-gray-800">{{ number_format($payment['amount'], 0) }} FCFA</p>
                            <p class="text-sm text-gray-600">{{ $payment['method'] }} - {{ $payment['reference'] }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $payment['date'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
