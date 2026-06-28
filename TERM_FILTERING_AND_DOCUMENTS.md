# Filtrage par Trimestre et Génération de Documents

## 📋 Vue d'ensemble

Ce document décrit les nouvelles fonctionnalités pour filtrer les données des élèves par trimestre et générer les documents imprimables (bulletins, relevés, résumés de classe).

## 🎯 Objectifs

- ✅ Filtrer les notes des élèves par trimestre (term_1, term_2, term_3)
- ✅ Générer des bulletins trimestrels filtrés
- ✅ Exporter les données d'une classe par trimestre
- ✅ Récupérer les classements par trimestre
- ✅ Télécharger les documents imprimables

## 🏗️ Architecture

### Services

#### 1. **TermGradeService** (modules/Grades/Services)
Service pour gérer les notes filtrées par trimestre.

```php
// Récupérer les notes d'un élève pour un trimestre
$grades = $termGradeService->getStudentGradesByTerm($studentId, $academicPeriodId);

// Récupérer la moyenne
$average = $termGradeService->getStudentTermAverage($studentId, $academicPeriodId);

// Récupérer les notes de la classe
$classGrades = $termGradeService->getClassGradesByTerm($classId, $academicPeriodId);

// Récupérer le classement
$ranking = $termGradeService->getClassRankingByTerm($classId, $academicPeriodId);

// Récupérer les trimestres disponibles
$terms = $termGradeService->getAvailableTerms($academicYear);

// Récupérer le résumé
$summary = $termGradeService->getTermSummary($studentId, $academicPeriodId);
```

#### 2. **TermDocumentService** (modules/Dashboard/Services)
Service pour générer les données des documents filtrés par trimestre.

```php
// Données du bulletin trimestrel
$bulletinData = $documentService->getTermBulletinData($studentId, $academicPeriodId);

// Résumé de classe pour un trimestre
$classSummary = $documentService->getTermClassSummary($classId, $academicPeriodId);

// Relevé par trimestre
$transcript = $documentService->getTermTranscript($studentId, $academicPeriodId);
```

### Contrôleurs

#### 1. **TermGradeController** (modules/Grades/Controllers)
Endpoints pour accéder aux données des notes par trimestre.

**Routes disponibles:**
- `GET /api/term-grades/terms` - Lister les trimestres disponibles
- `GET /api/term-grades/current-term` - Récupérer le trimestre actuel
- `GET /api/term-grades/student/{student}` - Notes d'un élève pour un trimestre
- `GET /api/term-grades/student/{student}/summary` - Résumé des notes
- `GET /api/term-grades/student/{student}/average` - Moyenne de l'élève
- `GET /api/term-grades/class/{classId}/ranking` - Classement de la classe
- `GET /api/term-grades/class/{classId}/export` - Exporter les notes de la classe en CSV

**Exemple de requête:**
```bash
# Récupérer les notes d'un élève pour le trimestre 1
curl -X GET "http://localhost:8000/api/term-grades/student/1?academic_period_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Récupérer le classement
curl -X GET "http://localhost:8000/api/term-grades/class/1/ranking?academic_period_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 2. **TermDocumentController** (modules/Dashboard/Controllers)
Endpoints pour télécharger et prévisualiser les documents par trimestre.

**Routes disponibles:**
- `GET /api/dashboard/term-documents/terms` - Lister les trimestres
- `GET /api/dashboard/term-documents/student/{studentId}/bulletin/{academicPeriodId}` - Données du bulletin
- `GET /api/dashboard/term-documents/student/{studentId}/bulletin/{academicPeriodId}/download` - Télécharger le bulletin PDF
- `GET /api/dashboard/term-documents/student/{studentId}/bulletin/{academicPeriodId}/preview` - Prévisualiser le bulletin
- `GET /api/dashboard/term-documents/student/{studentId}/transcript` - Données du relevé
- `GET /api/dashboard/term-documents/student/{studentId}/transcript/download` - Télécharger le relevé PDF
- `GET /api/dashboard/term-documents/class/{classId}/summary/{academicPeriodId}` - Résumé de classe
- `GET /api/dashboard/term-documents/class/{classId}/summary/{academicPeriodId}/download` - Télécharger le résumé PDF

**Exemple de requête:**
```bash
# Télécharger le bulletin du trimestre 1 pour un élève
curl -X GET "http://localhost:8000/api/dashboard/term-documents/student/1/bulletin/1/download" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o bulletin.pdf

# Récupérer les données du bulletin sans télécharger
curl -X GET "http://localhost:8000/api/dashboard/term-documents/student/1/bulletin/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📊 Structure des Données

### Données du Bulletin Trimestrel
```json
{
  "school": {
    "name": "Lycée MyScholar",
    "logo_path": "...",
    "address": "...",
    "phone": "...",
    "email": "..."
  },
  "student": {
    "id": 1,
    "first_name": "Jean",
    "last_name": "Dupont",
    "full_name": "Jean Dupont",
    "class": "Terminale S",
    "registration_number": "STU001"
  },
  "academic": {
    "year": 2024,
    "term": "Trimestre 1",
    "term_type": "trimestre",
    "period": "01/09/2024 - 30/11/2024"
  },
  "grades": [
    {
      "subject": "Mathématiques",
      "subject_id": 1,
      "score": 18,
      "grade_type": "exam",
      "weight": 2.5,
      "comments": "Excellent travail"
    }
  ],
  "summary": {
    "average": 16.5,
    "grade": "B",
    "total_subjects": 8,
    "passed": 8,
    "failed": 0,
    "ranking": 5,
    "total_students": 45
  },
  "attendance": {
    "present": 40,
    "absent": 2,
    "justified": 0,
    "total": 42,
    "percentage": 95.24
  }
}
```

### Données du Résumé de Classe
```json
{
  "class_id": 1,
  "term": "Trimestre 1",
  "academic_year": 2024,
  "period": "01/09/2024 - 30/11/2024",
  "statistics": {
    "total_students": 45,
    "class_average": 14.2,
    "best_average": 19.5,
    "worst_average": 8.3,
    "best_student": "Marie Martin",
    "worst_student": "Pierre Blanc"
  },
  "students": [
    {
      "rank": 1,
      "student_id": 5,
      "student_name": "Marie Martin",
      "average": 19.5,
      "grade_count": 24
    }
  ]
}
```

## 🔄 Flux de Travail

### 1. Filtrer les notes d'un élève par trimestre

```php
// Dans un contrôleur
$termGradeService = app(\Modules\Grades\Services\TermGradeService::class);

// Récupérer les trimestres disponibles
$terms = $termGradeService->getAvailableTerms(2024);

// Récupérer les notes du premier trimestre
$academicPeriodId = $terms[0]['id'];
$grades = $termGradeService->getStudentGradesByTerm($studentId, $academicPeriodId);

// Obtenir le résumé
$summary = $termGradeService->getTermSummary($studentId, $academicPeriodId);
```

### 2. Générer et télécharger un bulletin par trimestre

```php
// Dans un contrôleur
$documentService = app(\Modules\Dashboard\Services\TermDocumentService::class);

// Récupérer les données
$bulletinData = $documentService->getTermBulletinData($studentId, $academicPeriodId);

// Générer le PDF
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
    'bulletins.term-bulletin',
    ['data' => $bulletinData]
)->download('bulletin.pdf');
```

### 3. Exporter les notes d'une classe par trimestre

```bash
# Via l'API
curl -X GET "http://localhost:8000/api/term-grades/class/1/export?academic_period_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  | jq -r '.csv' | base64 -d > grades.csv
```

### 4. Obtenir le classement d'une classe

```php
$termGradeService = app(\Modules\Grades\Services\TermGradeService::class);
$ranking = $termGradeService->getClassRankingByTerm($classId, $academicPeriodId);

// Le tableau est déjà trié par moyenne descendante avec le rang assigné
foreach ($ranking as $student) {
    echo "{$student['rank']}. {$student['student_name']} - Moyenne: {$student['average']}";
}
```

## 🗄️ Schéma de Base de Données

### Table: enrollment_academic_periods
Liaison entre les inscriptions et les périodes académiques.

| Colonne | Type | Description |
|---------|------|-------------|
| id | BIGINT | ID primaire |
| enrollment_id | FK | ID de l'inscription (student_enrollments) |
| academic_period_id | FK | ID de la période académique |
| created_at | TIMESTAMP | Horodatage de création |
| updated_at | TIMESTAMP | Horodatage de mise à jour |

**Contraintes:**
- Clé unique sur (enrollment_id, academic_period_id)

## 📱 Intégration avec les Modèles

### StudentEnrollment
```php
// Relation vers les périodes académiques
public function academicPeriods()
{
    return $this->belongsToMany(
        AcademicPeriod::class,
        'enrollment_academic_periods',
        'enrollment_id',
        'academic_period_id'
    )->withTimestamps();
}
```

## 🔐 Autorisations

Les endpoints utilisent l'autorisation standard Laravel:
- Les élèves ne peuvent voir que leurs propres données
- Les enseignants peuvent voir les données de leurs classes
- Les administrateurs et directeurs ont accès complet
- Les permissions basées sur les rôles sont appliquées

## 📈 Cas d'Usage

### 1. Dashboard Élève
Afficher le bulletin du trimestre actuel avec les notes, la moyenne et le classement.

### 2. Bulletin d'Évaluation
Générer et télécharger un bulletin PDF pour chaque trimestre.

### 3. Rapport de Classe
Générer un résumé des performances de la classe pour un trimestre.

### 4. Relevé de Notes
Afficher l'historique complet des notes par trimestre.

### 5. Export pour Secrétariat
Exporter les données d'une classe par trimestre en CSV pour traitement administratif.

## 🚀 Déploiement

### Étapes d'installation

1. **Exécuter les migrations:**
   ```bash
   php artisan migrate
   ```

2. **Enregistrer les services:** (déjà fait dans les Service Providers)
   - TermGradeService dans GradesServiceProvider
   - TermDocumentService dans DashboardServiceProvider

3. **Vérifier les routes:**
   ```bash
   php artisan route:list | grep term
   ```

4. **Tester les endpoints:**
   ```bash
   # Récupérer les trimestres disponibles
   curl -X GET "http://localhost:8000/api/term-grades/terms" \
     -H "Authorization: Bearer YOUR_TOKEN"
   ```

## 🐛 Dépannage

### Les trimestres n'apparaissent pas
- Vérifier que la table `academic_periods` contient des données
- Vérifier que le type est 'trimestre' ou 'term'
- Vérifier l'année académique

### Pas de notes pour un trimestre
- Vérifier que les notes ont `grade_period_id` correctement défini
- Vérifier que la période académique est active

### PDF ne se génère pas
- Vérifier que DomPDF est installé: `composer require barryvdh/laravel-dompdf`
- Vérifier que la vue du bulletin existe

## 📚 Ressources Additionnelles

- [Guide Complet des Grades](modules/Grades/README.md)
- [Guide Complet du Dashboard](modules/Dashboard/README.md)
- [Guide Complet des Étudiants](modules/Students/README.md)
