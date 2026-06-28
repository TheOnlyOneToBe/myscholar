<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Scolarité</title>
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
            max-width: 800px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            background-color: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        .school-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 10px 0;
        }
        .school-motto {
            font-style: italic;
            color: #666;
            font-size: 14px;
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a;
        }
        .content {
            margin: 40px 0;
            line-height: 1.8;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .info-label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            min-width: 150px;
        }
        .info-value {
            color: #555;
        }
        .certificate-number {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .date-issued {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
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
            <div class="school-logo"><i class="fas fa-school"></i></div>
            <div class="school-name">{{ $school->name ?? 'Établissement Scolaire' }}</div>
            @if($school->motto)
                <div class="school-motto">{{ $school->motto }}</div>
            @endif
        </div>

        <!-- Title -->
        <div class="title">Certificat de Scolarité</div>

        <!-- Content -->
        <div class="content">
            <p style="text-align: justify; font-size: 16px; line-height: 1.6;">
                Je soussigné(e), directeur(trice) de {{ $school->name ?? 'l\'établissement' }},
                certifie par la présente que :
            </p>

            <!-- Student Information -->
            <div class="info-section">
                <div><span class="info-label">Nom et Prénom :</span> <span class="info-value">{{ $data['student_name'] }}</span></div>
                <div><span class="info-label">Matricule :</span> <span class="info-value">{{ $data['student_id'] }}</span></div>
                <div><span class="info-label">Date de Naissance :</span> <span class="info-value">{{ $data['date_of_birth'] }}</span></div>
                <div><span class="info-label">Classe :</span> <span class="info-value">{{ $data['class'] }}</span></div>
                <div><span class="info-label">Année Académique :</span> <span class="info-value">{{ $data['academic_year'] }}</span></div>
            </div>

            <!-- Certificate Body -->
            <p style="text-align: justify; margin-top: 30px;">
                est régulièrement inscrit(e) dans notre établissement pour l'année scolaire {{ $data['academic_year'] }}.
                L'étudiant(e) suit régulièrement les cours et a le statut d'<strong>{{ $data['enrollment_status'] }}</strong>.
            </p>

            <p style="text-align: justify;">
                Ce certificat est délivré sur demande de l'intéressé(e) pour servir et valoir ce que de droit.
            </p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div>
                <div class="signature-line">Directeur(trice)</div>
                <div style="margin-top: 20px; font-size: 12px; color: #666;">
                    {{ $school->director_name ?? 'Signature' }}
                </div>
            </div>
            <div>
                <div class="signature-line">Cachet de l'établissement</div>
                <div style="margin-top: 20px; font-size: 12px; color: #666;">
                    [Cachet Officiel]
                </div>
            </div>
        </div>

        <!-- Certificate Number -->
        <div class="certificate-number">
            Certificat #{{ $data['certificate_number'] }}
        </div>

        <!-- Date Issued -->
        <div class="date-issued">
            Délivré le : {{ $data['generated_date'] }}
        </div>
    </div>
</body>
</html>
