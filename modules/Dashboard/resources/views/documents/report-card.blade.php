<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #f5f5f5;
        }
        .document {
            background-color: white;
            padding: 40px;
            border: 2px solid #333;
            margin: 0 auto;
            max-width: 900px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 10px 0;
        }
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 20px 0;
            color: #1a1a1a;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #28a745;
        }
        .info-block {
            font-size: 13px;
        }
        .info-label {
            font-weight: bold;
            color: #333;
            font-size: 11px;
            text-transform: uppercase;
        }
        .info-value {
            color: #555;
            font-size: 14px;
            margin-top: 3px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 13px;
        }
        .grades-table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }
        .grades-table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .grade-score {
            font-weight: bold;
            text-align: center;
            font-size: 14px;
        }
        .grade-letter {
            background-color: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .summary-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f7ff;
            border-left: 4px solid #007bff;
        }
        .summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 10px 0;
        }
        .summary-item {
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-top: 5px;
        }
        .remarks {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        .date-issued {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.05;
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $school->acronym ?? 'SCHOOL' }}</div>

    <div class="document">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $school->name ?? 'Établissement Scolaire' }}</div>
            <div style="color: #666; font-size: 12px;">{{ $school->address ?? '' }}</div>
        </div>

        <!-- Title -->
        <div class="title">📋 BULLETIN SCOLAIRE</div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-block">
                <div class="info-label">Nom et Prénom</div>
                <div class="info-value">{{ $data['student_name'] }}</div>
            </div>
            <div class="info-block">
                <div class="info-label">Matricule</div>
                <div class="info-value">{{ $data['student_id'] }}</div>
            </div>
            <div class="info-block">
                <div class="info-label">Classe</div>
                <div class="info-value">{{ $data['class'] }}</div>
            </div>
            <div class="info-block">
                <div class="info-label">Année Académique</div>
                <div class="info-value">{{ $data['academic_year'] }}</div>
            </div>
        </div>

        <!-- Grades Table -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Matière</th>
                    <th style="width: 20%;">Score</th>
                    <th style="width: 20%;">Notation</th>
                    <th style="width: 20%;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['subjects'] as $subject)
                    <tr>
                        <td>{{ $subject['subject'] }}</td>
                        <td class="grade-score">{{ $subject['average'] }}/20</td>
                        <td style="text-align: center;">
                            @if($subject['average'] >= 18)
                                <span style="color: green; font-weight: bold;">Excellent</span>
                            @elseif($subject['average'] >= 16)
                                <span style="color: green;">Très Bien</span>
                            @elseif($subject['average'] >= 14)
                                <span style="color: blue;">Bien</span>
                            @elseif($subject['average'] >= 12)
                                <span style="color: blue;">Satisfaisant</span>
                            @else
                                <span style="color: red;">À Améliorer</span>
                            @endif
                        </td>
                        <td>
                            <div class="grade-letter
                                @if($subject['grade'] === 'A') bg-green-200
                                @elseif($subject['grade'] === 'B') bg-blue-200
                                @elseif($subject['grade'] === 'C') bg-yellow-200
                                @elseif($subject['grade'] === 'D') bg-orange-200
                                @else bg-red-200
                                @endif
                            " style="background-color: @if($subject['grade'] === 'A') #d4edda @elseif($subject['grade'] === 'B') #d1ecf1 @elseif($subject['grade'] === 'C') #fff3cd @elseif($subject['grade'] === 'D') #ffe5cc @else #f8d7da @endif;">
                                {{ $subject['grade'] }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Aucune note enregistrée</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <h3 style="margin-top: 0; color: #007bff;">Résumé Académique</h3>
            <div class="summary-row">
                <div class="summary-item">
                    <div class="summary-label">Moyenne Générale</div>
                    <div class="summary-value">{{ $data['overall_average'] }}/20</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Appréciation</div>
                    <div class="summary-value" style="font-size: 24px;">
                        @if($data['overall_grade'] === 'A') 🌟
                        @elseif($data['overall_grade'] === 'B') ⭐
                        @elseif($data['overall_grade'] === 'C') ✓
                        @elseif($data['overall_grade'] === 'D') △
                        @else ⚠️
                        @endif
                    </div>
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-item">
                    <div class="summary-label">Nombre de Matières</div>
                    <div class="summary-value">{{ $data['total_subjects'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Classement</div>
                    <div class="summary-value">À Déterminer</div>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        <div class="remarks">
            <strong>Remarques :</strong>
            @if($data['overall_average'] >= 18)
                Excellent résultat. Continuez vos efforts !
            @elseif($data['overall_average'] >= 16)
                Très bonne performance. Bravo !
            @elseif($data['overall_average'] >= 14)
                Bon travail. Un peu d'effort pour les prochains contrôles.
            @elseif($data['overall_average'] >= 12)
                Satisfaisant mais des améliorations sont nécessaires.
            @else
                Travail insuffisant. Consultation avec les parents recommandée.
            @endif
        </div>

        <!-- Date Issued -->
        <div class="date-issued">
            <strong>Bulletin #:</strong> {{ $data['bulletin_number'] }}<br>
            <strong>Émis le :</strong> {{ $data['generated_date'] }}
        </div>
    </div>
</body>
</html>
