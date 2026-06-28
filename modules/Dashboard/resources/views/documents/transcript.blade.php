<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes Complet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .document { background: white; padding: 40px; border: 2px solid #333; max-width: 900px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .school-name { font-size: 24px; font-weight: bold; color: #1a1a1a; margin: 10px 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin: 20px 0; color: #1a1a1a; }
        .student-info { padding: 15px; background-color: #f9f9f9; border-left: 4px solid #17a2b8; margin: 20px 0; }
        .info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 10px 0; }
        .info-block { font-size: 13px; }
        .info-label { font-weight: bold; color: #333; font-size: 11px; text-transform: uppercase; }
        .info-value { color: #555; font-size: 14px; margin-top: 3px; }
        .year-section { margin: 30px 0; padding: 15px; background-color: #f0f7ff; border-left: 4px solid #007bff; }
        .year-title { font-size: 16px; font-weight: bold; color: #007bff; margin: 0 0 15px 0; }
        .subjects-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .subject-card { background: white; padding: 12px; border: 1px solid #ddd; border-radius: 4px; }
        .subject-name { font-weight: bold; color: #333; }
        .subject-score { color: #007bff; font-weight: bold; margin-top: 5px; }
        .summary { padding: 20px; background-color: #e7f3ff; border-left: 4px solid #007bff; margin: 30px 0; }
        .summary-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .summary-item { padding: 10px; text-align: center; }
        .summary-label { font-size: 12px; color: #666; text-transform: uppercase; }
        .summary-value { font-size: 18px; font-weight: bold; color: #007bff; margin-top: 5px; }
        .date-issued { text-align: right; font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div class="school-name">{{ $school->name ?? 'Établissement Scolaire' }}</div>
        </div>

        <div class="title">📊 RELEVÉ DE NOTES COMPLET</div>

        <div class="student-info">
            <div class="info-row">
                <div class="info-block">
                    <div class="info-label">Nom et Prénom</div>
                    <div class="info-value">{{ $data['student_name'] }}</div>
                </div>
                <div class="info-block">
                    <div class="info-label">Matricule</div>
                    <div class="info-value">{{ $data['student_id'] }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-block">
                    <div class="info-label">Date de Naissance</div>
                    <div class="info-value">{{ $data['date_of_birth'] }}</div>
                </div>
                <div class="info-block">
                    <div class="info-label">Genre</div>
                    <div class="info-value">{{ ucfirst($data['gender']) }}</div>
                </div>
            </div>
        </div>

        @foreach($data['academics'] as $academic)
        <div class="year-section">
            <div class="year-title">Année Académique {{ $academic['academic_year'] }}</div>
            <div class="subjects-grid">
                @foreach($academic['subjects'] as $subject)
                    <div class="subject-card">
                        <div class="subject-name">{{ $subject['name'] }}</div>
                        <div class="subject-score">{{ $subject['score'] }}/20 ({{ $subject['grade'] }})</div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top: 10px; text-align: right; font-size: 12px;">
                <strong>Moyenne :</strong> {{ $academic['average'] }}/20
            </div>
        </div>
        @endforeach

        <div class="summary">
            <h3 style="margin-top: 0;">Résumé Global</h3>
            <div class="summary-row">
                <div class="summary-item">
                    <div class="summary-label">Moyenne Générale</div>
                    <div class="summary-value">{{ $data['overall_average'] }}/20</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Nombre d'Années</div>
                    <div class="summary-value">{{ count($data['academics']) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total de Matières</div>
                    <div class="summary-value">
                        @php
                            $totalSubjects = array_sum(array_map(fn($a) => count($a['subjects']), $data['academics']));
                        @endphp
                        {{ $totalSubjects }}
                    </div>
                </div>
            </div>
        </div>

        <div class="date-issued">
            <strong>Relevé #:</strong> {{ $data['transcript_number'] }}<br>
            <strong>Généré le :</strong> {{ $data['generated_date'] }}
        </div>
    </div>
</body>
</html>
