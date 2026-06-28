<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Complet {{ $data['year'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .school-info {
            font-size: 12px;
            color: #666;
        }

        .bulletin-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 10px;
        }

        .student-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }

        .info-label {
            font-weight: bold;
            width: 200px;
        }

        .info-value {
            flex: 1;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1e40af;
            page-break-inside: avoid;
        }

        .subsection-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }

        .term-box {
            margin: 15px 0;
            padding: 15px;
            background-color: #eff6ff;
            border-left: 4px solid #1e40af;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .term-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 10px 0;
        }

        .term-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }

        .term-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .term-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .table thead {
            background-color: #1e40af;
            color: white;
        }

        .table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .grade-excellent {
            color: #059669;
            font-weight: bold;
        }

        .grade-good {
            color: #2563eb;
            font-weight: bold;
        }

        .grade-average {
            color: #d97706;
            font-weight: bold;
        }

        .grade-poor {
            color: #dc2626;
            font-weight: bold;
        }

        .annual-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
            page-break-inside: avoid;
        }

        .summary-item {
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: center;
        }

        .summary-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }

        .payments-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
            page-break-inside: avoid;
        }

        .payment-item {
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: center;
        }

        .payment-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .payment-value {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $data['student']->school->name ?? 'LYCÉE' }}</div>
            <div class="school-info">{{ date('d/m/Y') }}</div>
            <div class="bulletin-title">BULLETIN ACADÉMIQUE COMPLET</div>
            <div class="school-info">Année Académique: {{ $data['year'] }}</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <span class="info-label">Nom et Prénoms:</span>
                <span class="info-value">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Classe:</span>
                <span class="info-value">{{ $data['student']->getCurrentClass()?->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Matricule:</span>
                <span class="info-value">{{ $data['student']->registration_number ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Annual Summary -->
        <div class="section-title"><i class="fas fa-graduation-cap"></i> Résumé de l'Année</div>
        <div class="annual-summary">
            <div class="summary-item">
                <div class="summary-label">Moyenne Annuelle</div>
                <div class="summary-value {{ $data['annual_summary']['average'] >= 18 ? 'grade-excellent' : ($data['annual_summary']['average'] >= 16 ? 'grade-good' : ($data['annual_summary']['average'] >= 12 ? 'grade-average' : 'grade-poor')) }}">
                    {{ $data['annual_summary']['average'] }}/20
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Grade Annuel</div>
                <div class="summary-value">{{ $data['annual_summary']['grade'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Statut</div>
                <div class="summary-value" style="font-size: 14px;">
                    @if ($data['annual_summary']['average'] >= 16)
                    <span style="color: #059669;">ADMIS</span>
                    @elseif ($data['annual_summary']['average'] >= 12)
                    <span style="color: #d97706;">EN RATTRAPAGE</span>
                    @else
                    <span style="color: #dc2626;">NON ADMIS</span>
                    @endif
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Année</div>
                <div class="summary-value">{{ $data['year'] }}</div>
            </div>
        </div>

        <!-- Trimestres -->
        <div class="section-title"><i class="fas fa-calendar"></i> Performance par Trimestre</div>

        @foreach (['term_1', 'term_2', 'term_3'] as $term)
        <div class="term-box page-break-avoid">
            <div class="subsection-title">{{ $data[$term]['academic']['name'] ?? 'Trimestre' }}</div>
            <div class="school-info">Période: {{ $data[$term]['academic']['period'] ?? '' }}</div>

            <div class="term-summary">
                <div class="term-item">
                    <div class="term-label">Moyenne</div>
                    <div class="term-value {{ $data[$term]['summary']['average'] >= 18 ? 'grade-excellent' : ($data[$term]['summary']['average'] >= 16 ? 'grade-good' : ($data[$term]['summary']['average'] >= 12 ? 'grade-average' : 'grade-poor')) }}">
                        {{ $data[$term]['summary']['average'] }}/20
                    </div>
                </div>
                <div class="term-item">
                    <div class="term-label">Grade</div>
                    <div class="term-value">{{ $data[$term]['summary']['grade'] ?? 'N/A' }}</div>
                </div>
                <div class="term-item">
                    <div class="term-label">Plus Haute</div>
                    <div class="term-value" style="color: #059669;">{{ $data[$term]['summary']['highest'] ?? 0 }}</div>
                </div>
                <div class="term-item">
                    <div class="term-label">Plus Basse</div>
                    <div class="term-value" style="color: #dc2626;">{{ $data[$term]['summary']['lowest'] ?? 0 }}</div>
                </div>
            </div>

            <div style="font-size: 11px; color: #666; margin-top: 8px;">
                Nombre de notes: {{ $data[$term]['summary']['grade_count'] ?? 0 }} |
                Classement: {{ $data[$term]['summary']['ranking'] ?? 'N/A' }}/{{ $data[$term]['summary']['total_students'] ?? 0 }}
            </div>
        </div>
        @endforeach

        <!-- Grades by Subject (Combined) -->
        <div class="section-title"><i class="fas fa-book"></i> Notes par Matière</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th style="text-align: center;">T1</th>
                    <th style="text-align: center;">T2</th>
                    <th style="text-align: center;">T3</th>
                    <th style="text-align: center;">Moyenne</th>
                    <th style="text-align: center;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $allSubjects = collect()
                        ->merge($data['term_1']['grades'])
                        ->merge($data['term_2']['grades'])
                        ->merge($data['term_3']['grades'])
                        ->unique('subject')
                        ->sortBy('subject');
                @endphp

                @foreach ($allSubjects as $subject)
                @php
                    $t1Grade = collect($data['term_1']['grades'])->where('subject', $subject['subject'])->first();
                    $t2Grade = collect($data['term_2']['grades'])->where('subject', $subject['subject'])->first();
                    $t3Grade = collect($data['term_3']['grades'])->where('subject', $subject['subject'])->first();

                    $avg = (($t1Grade['average'] ?? 0) + ($t2Grade['average'] ?? 0) + ($t3Grade['average'] ?? 0)) / 3;
                @endphp
                <tr>
                    <td>{{ $subject['subject'] }}</td>
                    <td style="text-align: center;">{{ $t1Grade['average'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $t2Grade['average'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $t3Grade['average'] ?? '-' }}</td>
                    <td style="text-align: center;" class="{{ $avg >= 18 ? 'grade-excellent' : ($avg >= 16 ? 'grade-good' : ($avg >= 12 ? 'grade-average' : 'grade-poor')) }}">
                        {{ round($avg, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $subject['grade'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Payments Summary -->
        <div class="section-title"><i class="fas fa-money-bill-alt"></i> Situation Financière</div>
        <div class="payments-box">
            <div class="payment-item">
                <div class="payment-label">Total Facturé</div>
                <div class="payment-value">{{ number_format($data['payments']['total'], 0, ',', ' ') }} XAF</div>
            </div>
            <div class="payment-item">
                <div class="payment-label">Montant Payé</div>
                <div class="payment-value" style="color: #059669;">{{ number_format($data['payments']['paid'], 0, ',', ' ') }} XAF</div>
            </div>
            <div class="payment-item">
                <div class="payment-label">Solde Dû</div>
                <div class="payment-value" style="color: #dc2626;">{{ number_format($data['payments']['pending'], 0, ',', ' ') }} XAF</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Document officiel généré le {{ date('d/m/Y à H:i') }}</p>
            <p style="margin-top: 5px;">Plateforme de Gestion Académique - MyScholar</p>
            <p style="margin-top: 5px; font-size: 10px;">Ce document est valide avec le sceau officiel de l'établissement</p>
        </div>
    </div>
</body>
</html>
