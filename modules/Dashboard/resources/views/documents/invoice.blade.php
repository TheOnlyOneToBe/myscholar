<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .document { background: white; padding: 40px; border: 2px solid #333; max-width: 900px; margin: 0 auto; }
        .header { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 30px; }
        .school-info { }
        .school-name { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
        .school-detail { font-size: 12px; color: #666; margin: 3px 0; }
        .invoice-info { text-align: right; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #007bff; margin-bottom: 10px; }
        .invoice-detail { font-size: 13px; color: #666; margin: 5px 0; }
        .addresses { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0; }
        .address-block { }
        .address-label { font-weight: bold; color: #333; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; }
        .address-content { font-size: 13px; color: #555; line-height: 1.6; }
        .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
        .items-table th { background-color: #007bff; color: white; padding: 12px; text-align: left; font-weight: bold; }
        .items-table td { padding: 10px 12px; border-bottom: 1px solid #ddd; }
        .items-table tr:nth-child(even) { background-color: #f9f9f9; }
        .items-table tr:last-child td { border-bottom: 2px solid #333; }
        .quantity { text-align: center; }
        .price { text-align: right; }
        .summary { margin: 30px 0; }
        .summary-row { display: grid; grid-template-columns: 1fr auto; gap: 20px; margin: 8px 0; padding: 8px 0; font-size: 14px; }
        .summary-label { }
        .summary-value { text-align: right; width: 150px; font-weight: bold; }
        .total-row { border-top: 2px solid #333; border-bottom: 2px solid #333; padding: 12px 0; margin: 15px 0; }
        .total-label { font-size: 18px; font-weight: bold; }
        .total-value { font-size: 24px; font-weight: bold; color: #dc3545; }
        .status-section { padding: 15px; background-color: #f0f7ff; border-left: 4px solid #007bff; margin: 20px 0; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .status-pending { background-color: #ffc107; color: white; }
        .status-paid { background-color: #28a745; color: white; }
        .terms { padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; margin: 20px 0; font-size: 12px; line-height: 1.6; }
        .footer { text-align: center; font-size: 11px; color: #999; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="document">
        <!-- Header -->
        <div class="header">
            <div class="school-info">
                <div class="school-name">{{ $school->name ?? 'Établissement Scolaire' }}</div>
                @if($school->address)
                    <div class="school-detail">📍 {{ $school->address }}</div>
                @endif
                @if($school->phone)
                    <div class="school-detail">📞 {{ $school->phone }}</div>
                @endif
                @if($school->email)
                    <div class="school-detail">📧 {{ $school->email }}</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-detail"><strong>N° :</strong> {{ $data['invoice_number'] }}</div>
                <div class="invoice-detail"><strong>Date :</strong> {{ $data['issued_date'] }}</div>
                <div class="invoice-detail"><strong>Échéance :</strong> {{ $data['due_date'] }}</div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="addresses">
            <div>
                <div class="address-label">Facturé À</div>
                <div class="address-content">
                    <strong>{{ $data['student_name'] }}</strong><br>
                    Matricule: {{ $data['student_id'] }}<br>
                    Email: {{ $data['student_email'] }}
                </div>
            </div>
            <div>
                <div class="address-label">Établissement</div>
                <div class="address-content">
                    <strong>{{ $school->name ?? 'École' }}</strong><br>
                    @if($school->address)
                        {{ $school->address }}<br>
                    @endif
                    @if($school->po_box)
                        BP: {{ $school->po_box }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 15%;" class="quantity">Quantité</th>
                    <th style="width: 20%;" class="price">Prix Unitaire</th>
                    <th style="width: 15%;" class="price">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['items'] as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td class="quantity">{{ $item['quantity'] }}</td>
                        <td class="price">{{ number_format($item['unit_price'], 0) }} FCFA</td>
                        <td class="price">{{ number_format($item['total'], 0) }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Aucun article</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Sous-total</div>
                <div class="summary-value">{{ number_format($data['subtotal'], 0) }} FCFA</div>
            </div>
            @if($data['tax'] > 0)
                <div class="summary-row">
                    <div class="summary-label">Taxes</div>
                    <div class="summary-value">{{ number_format($data['tax'], 0) }} FCFA</div>
                </div>
            @endif
            <div class="summary-row total-row">
                <div class="total-label">MONTANT TOTAL</div>
                <div class="summary-value total-value">{{ number_format($data['total_amount'], 0) }} FCFA</div>
            </div>
        </div>

        <!-- Status -->
        <div class="status-section">
            <strong>Statut :</strong>
            <span class="status-badge @if($data['status'] === 'paid') status-paid @else status-pending @endif">
                @if($data['status'] === 'paid') ✓ PAYÉE @else EN ATTENTE @endif
            </span>
        </div>

        <!-- Payment Terms -->
        @if($data['payment_terms'])
            <div class="terms">
                <strong>Conditions de Paiement :</strong><br>
                {{ $data['payment_terms'] }}
            </div>
        @endif

        <!-- Notes -->
        @if($data['notes'])
            <div style="padding: 15px; background-color: #f9f9f9; border-left: 4px solid #6c757d; margin: 20px 0; font-size: 12px;">
                <strong>Notes :</strong><br>
                {{ $data['notes'] }}
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Merci pour votre paiement. Cette facture a été générée automatiquement le {{ $data['generated_date'] }}.<br>
            En cas de question, veuillez contacter l'établissement.
        </div>
    </div>
</body>
</html>
