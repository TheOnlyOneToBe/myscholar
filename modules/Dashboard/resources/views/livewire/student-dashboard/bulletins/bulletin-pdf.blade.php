<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin {{ $data['academic']['term'] }} {{ $data['academic']['year'] }}</title>
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
            margin-bottom: 5px;
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

        .summary-box {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            padding: 15px;
            background-color: #eff6ff;
            border-left: 4px solid #1e40af;
            border-radius: 4px;
        }

        .summary-item {
            flex: 1;
            text-align: center;
        }

        .summary-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table thead {
            background-color: #1e40af;
            color: white;
        }

        .table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 13px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table tbody tr:hover {
            background-color: #f3f4f6;
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

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e40af;
        }

        .attendance-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .attendance-item {
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: center;
        }

        .attendance-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .attendance-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .signature-box {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-line {
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

        .signature-label {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $data['school']['name'] ?? 'LYCÉE' }}</div>
            @if ($data['school']['address'])
            <div class="school-info">{{ $data['school']['address'] }}</div>
            @endif
            @if ($data['school']['phone'])
            <div class="school-info">Tél: {{ $data['school']['phone'] }}</div>
            @endif
            <div class="bulletin-title">BULLETIN {{ strtoupper($data['academic']['term']) }}</div>
            <div class="school-info">Année Académique: {{ $data['academic']['year'] }}</div>
            <div class="school-info">Période: {{ $data['academic']['period'] }}</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <span class="info-label">Nom et Prénoms:</span>
                <span class="info-value">{{ $data['student']['full_name'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Classe:</span>
                <span class="info-value">{{ $data['student']['class'] ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Matricule:</span>
                <span class="info-value">{{ $data['student']['registration_number'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date d'établissement:</span>
                <span class="info-value">{{ date('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-item">
                <div class="summary-label">Moyenne Générale</div>
                <div class="summary-value {{ $data['summary']['average'] >= 18 ? 'grade-excellent' : ($data['summary']['average'] >= 16 ? 'grade-good' : ($data['summary']['average'] >= 12 ? 'grade-average' : 'grade-poor')) }}">
                    {{ $data['summary']['average'] }}/20
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Grade</div>
                <div class="summary-value">{{ $data['summary']['grade'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Classement</div>
                <div class="summary-value">{{ $data['summary']['ranking'] }}/{{ $data['summary']['total_students'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Matières</div>
                <div class="summary-value">{{ $data['summary']['total_subjects'] }}</div>
            </div>
        </div>

        <!-- Grades Table -->
        <div class="section-title"><i class="fas fa-book"></i> Notes par Matière</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th style="width: 80px; text-align: center;">Moyenne</th>
                    <th style="width: 60px; text-align: center;">Grade</th>
                    <th style="width: 60px; text-align: center;">Notes</th>
                    <th style="width: 70px; text-align: center;">Plus Haute</th>
                    <th style="width: 70px; text-align: center;">Plus Basse</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['grades'] as $grade)
                <tr>
                    <td>{{ $grade['subject'] }}</td>
                    <td style="text-align: center;" class="{{ $grade['average'] >= 18 ? 'grade-excellent' : ($grade['average'] >= 16 ? 'grade-good' : ($grade['average'] >= 12 ? 'grade-average' : 'grade-poor')) }}">
                        {{ $grade['average'] }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $grade['grade'] }}</td>
                    <td style="text-align: center;">{{ $grade['count'] }}</td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">{{ $grade['highest'] }}</td>
                    <td style="text-align: center; color: #dc2626; font-weight: bold;">{{ $grade['lowest'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Attendance -->
        <div class="section-title"><i class="fas fa-check"></i> Assiduité</div>
        <div class="attendance-box">
            <div class="attendance-item">
                <div class="attendance-label">Présentes</div>
                <div class="attendance-value" style="color: #059669;">{{ $data['attendance']['present'] }}</div>
            </div>
            <div class="attendance-item">
                <div class="attendance-label">Absentes</div>
                <div class="attendance-value" style="color: #dc2626;">{{ $data['attendance']['absent'] }}</div>
            </div>
            <div class="attendance-item">
                <div class="attendance-label">Justifiées</div>
                <div class="attendance-value" style="color: #d97706;">{{ $data['attendance']['justified'] }}</div>
            </div>
            <div class="attendance-item">
                <div class="attendance-label">Taux (%)</div>
                <div class="attendance-value">{{ $data['attendance']['percentage'] }}%</div>
            </div>
        </div>

        <!-- Signature Box -->
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-space"></div>
                <div class="signature-label">Le Directeur</div>
            </div>
            <div class="signature-line">
                <div class="signature-space"></div>
                <div class="signature-label">Le Chef de Classe</div>
            </div>
            <div class="signature-line">
                <div class="signature-space"></div>
                <div class="signature-label">Parent/Tuteur</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bulletin généré le {{ date('d/m/Y à H:i') }}</p>
            <p style="margin-top: 10px;">Document officiel - MyScholar</p>
        </div>
    </div>
</body>
</html>
