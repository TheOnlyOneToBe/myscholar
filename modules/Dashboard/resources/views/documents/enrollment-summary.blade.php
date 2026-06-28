<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résumé d'Inscription</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .document { background: white; padding: 40px; border: 2px solid #333; max-width: 900px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .title { font-size: 22px; font-weight: bold; margin: 20px 0; text-align: center; }
        .info-section { padding: 15px; background-color: #f0f7ff; border-left: 4px solid #007bff; margin: 20px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { margin: 10px 0; }
        .label { font-weight: bold; color: #333; font-size: 12px; }
        .value { color: #555; margin-top: 3px; }
        .financial-section { padding: 20px; background-color: #fff8e1; border-left: 4px solid #ffc107; margin: 20px 0; }
        .financial-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 15px; }
        .financial-item { background: white; padding: 15px; border-radius: 4px; text-align: center; }
        .financial-label { font-size: 12px; color: #666; }
        .financial-value { font-size: 18px; font-weight: bold; color: #ffc107; margin-top: 5px; }
        .years-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px; }
        .year-badge { background: #007bff; color: white; padding: 10px; text-align: center; border-radius: 4px; font-weight: bold; }
        .date-issued { text-align: right; font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div style="font-size: 24px; font-weight: bold;">{{ $school->name ?? 'École' }}</div>
        </div>

        <div class="title">📑 RÉSUMÉ D'INSCRIPTION</div>

        <div class="info-section">
            <h3 style="margin-top: 0;">Informations Personnelles</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Nom et Prénom</div>
                    <div class="value">{{ $data['student_name'] }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Matricule</div>
                    <div class="value">{{ $data['student_id'] }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Email</div>
                    <div class="value">{{ $data['email'] }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Téléphone</div>
                    <div class="value">{{ $data['phone'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Genre</div>
                    <div class="value">{{ ucfirst($data['gender']) }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Classe Actuelle</div>
                    <div class="value">{{ $data['current_class'] }}</div>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <div class="label">Adresse</div>
                <div class="value">{{ $data['address'] ?? 'Non spécifiée' }}</div>
            </div>
        </div>

        <div class="financial-section">
            <h3 style="margin-top: 0;">Résumé Financier</h3>
            <div class="financial-grid">
                <div class="financial-item">
                    <div class="financial-label">Montant Facturé</div>
                    <div class="financial-value">{{ number_format($data['total_invoiced'], 0) }}<br><span style="font-size: 12px;">FCFA</span></div>
                </div>
                <div class="financial-item">
                    <div class="financial-label">Montant Payé</div>
                    <div class="financial-value" style="color: #28a745;">{{ number_format($data['total_paid'], 0) }}<br><span style="font-size: 12px;">FCFA</span></div>
                </div>
                <div class="financial-item">
                    <div class="financial-label">Solde en Attente</div>
                    <div class="financial-value" style="color: @if($data['outstanding_balance'] > 0) #dc3545 @else #28a745 @endif;">
                        {{ number_format($data['outstanding_balance'], 0) }}<br><span style="font-size: 12px;">FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 15px; background-color: #f9f9f9; border-left: 4px solid #17a2b8; margin: 20px 0;">
            <h3 style="margin-top: 0;">Années d'Inscription</h3>
            <div class="years-list">
                @foreach($data['enrollment_years'] as $year)
                    <div class="year-badge">{{ $year }}</div>
                @endforeach
            </div>
            <div style="margin-top: 15px; font-size: 12px; color: #666;">
                Total d'inscriptions : <strong>{{ $data['total_enrollments'] }}</strong>
            </div>
        </div>

        <div style="padding: 15px; background-color: #f0f0f0; border-radius: 4px; margin: 20px 0;">
            <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                Ce résumé confirme que {{ $data['student_name'] }} a été inscrit(e) à l'établissement {{ $school->name ?? 'l\'école' }}
                pour {{ $data['total_enrollments'] }} année(s) académique(s). Le document fournit un aperçu complet de la scolarité
                et de la situation financière de l'étudiant(e) auprès de notre établissement.
            </p>
        </div>

        <div class="date-issued">
            <strong>Résumé #:</strong> {{ $data['summary_number'] }}<br>
            <strong>Généré le :</strong> {{ $data['generated_date'] }}
        </div>
    </div>
</body>
</html>
