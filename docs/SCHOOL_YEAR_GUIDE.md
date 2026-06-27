# Guide des Années Scolaires - MyScholar

## Vue d'ensemble

MyScholar utilise un système complet de gestion des années scolaires permettant:
- ✅ Gérer plusieurs années scolaires
- ✅ Accéder aux données de différentes années (historique complet)
- ✅ Filtrer automatiquement par année active
- ✅ Archiver les années (verrouillage des données)
- ✅ Suivre la progression de chaque année scolaire

## Modèle de Données

### Table `school_years`

```sql
CREATE TABLE school_years (
    id BIGINT PRIMARY KEY,
    name VARCHAR(50) UNIQUE,              -- "2024-2025"
    start_year YEAR,                      -- 2024
    end_year YEAR,                        -- 2025
    start_date DATE,                      -- 2024-09-01
    end_date DATE,                        -- 2025-08-31
    is_active BOOLEAN DEFAULT FALSE,      -- Année actuelle
    is_locked BOOLEAN DEFAULT FALSE,      -- Archivée
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Données liées aux années scolaires

Les modèles suivants incluent `school_year_id`:

| Module | Modèle | Description |
|--------|--------|-------------|
| Classes | SchoolClass | Une classe peut avoir plusieurs versions par année |
| Students | StudentEnrollment | L'inscription d'un étudiant à une classe par année |
| Grades | Grade, GradePeriod | Notes et périodes de notation par année |
| Billing | Invoice, FeeStructure | Factures et structures de frais par année |
| Attendance | AttendanceSession | Sessions d'appel par année |

## Gestion des Années Scolaires

### Créer une nouvelle année scolaire

#### Via interface interactive:
```bash
php artisan school-year:manage create
```

Questions posées:
- Année de début (ex: 2024)
- Année de fin (ex: 2025)
- Date de début (format: YYYY-MM-DD)
- Date de fin (format: YYYY-MM-DD)
- Description (optionnel)
- Rendre l'année active?

#### Via code:
```php
$service = app(\Modules\Config\Services\SchoolYearService::class);

$schoolYear = $service->createSchoolYear(
    startYear: 2024,
    endYear: 2025,
    startDate: Carbon::parse('2024-09-01'),
    endDate: Carbon::parse('2025-08-31'),
    description: 'Année scolaire 2024-2025',
    setActive: true  // Rendre active immédiatement
);
```

### Lister les années scolaires

```bash
php artisan school-year:manage list
```

Affiche:
- Nom de l'année
- Dates (début → fin)
- Durée totale (jours)
- Pourcentage d'avancement
- Statut (En cours/Archivée/Inactif)

### Définir l'année active

```bash
php artisan school-year:manage set-active
```

Sélectionnez l'année à activer dans la liste.

```php
$service = app(\Modules\Config\Services\SchoolYearService::class);
$schoolYear = SchoolYear::find($id);
$service->setActiveSchoolYear($schoolYear);
```

### Archiver une année (Verrouillage)

```bash
php artisan school-year:manage lock
```

Cela empêche toute modification des données de cette année.

```php
$service->lockSchoolYear($schoolYear);
```

### Déverrouiller une année

```bash
php artisan school-year:manage unlock
```

```php
$service->unlockSchoolYear($schoolYear);
```

## Utilisation dans les Modèles

### Ajouter le support des années scolaires

1. Ajouter le trait `BelongsToSchoolYear`:

```php
namespace Modules\Students\Models;

use App\Traits\BelongsToSchoolYear;

class Student extends Model
{
    use BelongsToSchoolYear;
    
    protected $fillable = [
        'school_year_id',
        // ... autres champs
    ];
}
```

2. Ajouter la colonne dans la migration:

```php
$table->unsignedBigInteger('school_year_id')->nullable();
$table->foreign('school_year_id')
    ->references('id')
    ->on('school_years')
    ->onDelete('cascade');
```

### Scopes de filtrage

#### Obtenir l'année actuelle

```php
// Depuis un modèle
$students = Student::currentYear()->get();

// Ou directement
$currentYear = app(\Modules\Config\Services\SchoolYearService::class)
    ->getCurrentSchoolYear();
$students = Student::where('school_year_id', $currentYear->id)->get();
```

#### Filtrer par année spécifique

```php
$schoolYear = SchoolYear::byName('2024-2025');
$students = Student::forSchoolYear($schoolYear)->get();

// Ou par ID
$students = Student::where('school_year_id', $schoolYear->id)->get();
```

#### Obtenir toutes les années (y compris historique)

```php
$allStudents = Student::allYears()->get();
```

#### Exclure l'année actuelle

```php
$historicalStudents = Student::excludeCurrentYear()->get();
```

#### Filtrer par multiple années

```php
$schoolYearIds = [1, 2, 3];
$students = Student::forSchoolYears($schoolYearIds)->get();
```

## Cas d'Usage

### 1. Consultation des données d'une année passée

```php
// Obtenir les notes d'un élève pour l'année 2023-2024
$year = SchoolYear::byName('2023-2024');
$grades = Grade::forSchoolYear($year)
    ->where('student_id', $studentId)
    ->get();
```

### 2. Rapport annuel complet

```php
// Obtenir tous les élèves inscrits dans chaque année
$allYears = SchoolYear::allYears()->get();

foreach ($allYears as $year) {
    $students = Student::forSchoolYear($year)
        ->count();
    echo "Année {$year->name}: {$students} élèves\n";
}
```

### 3. Transition vers une nouvelle année scolaire

```php
$service = app(\Modules\Config\Services\SchoolYearService::class);

// 1. Créer la nouvelle année
$newYear = $service->createSchoolYear(2025, 2026, ...);

// 2. Archiver l'année précédente
$previousYear = $service->getPreviousSchoolYear($newYear);
$service->lockSchoolYear($previousYear);

// 3. Activer la nouvelle année
$service->setActiveSchoolYear($newYear);
```

### 4. Vue d'ensemble académique

```php
$service = app(\Modules\Config\Services\SchoolYearService::class);
$currentYear = $service->getCurrentSchoolYear();

// Progression de l'année
$progress = $currentYear->getProgressPercentage(); // 45.5%
$duration = $currentYear->getDuration(); // 365 jours

echo "Année scolaire: {$currentYear->name}\n";
echo "Avancement: {$progress}%\n";
echo "Durée: {$duration} jours\n";
```

### 5. Verrouiller les données avant archivage

```php
// Vérifier si on peut modifier les données
if ($service->canModifyData($schoolYear)) {
    // Effectuer les modifications
} else {
    // Année verrouillée, pas de modifications possibles
}
```

## Middleware de Protection

```php
// Protéger les routes de modification pour années archivées
Route::middleware('school-year:editable')->group(function () {
    Route::post('/grades', [GradeController::class, 'store']);
    Route::put('/grades/{grade}', [GradeController::class, 'update']);
});
```

## Initialisation du système

Pour initialiser le système avec une année par défaut:

```bash
php artisan school-year:manage initialize
```

Cela crée automatiquement l'année scolaire courante (sept - août) et la définit comme active.

## Points Importants

1. **Une seule année active** - Seule une année peut être "active" à la fois
2. **Verrouillage = Archivage** - Les années verrouillées ne peuvent pas être modifiées
3. **Filtrage automatique** - Les requêtes sans `.allYears()` filtrent automatiquement l'année active
4. **Historique complet** - Toutes les données historiques sont conservées et consultables
5. **Permissions** - L'accès aux années archivées peut être restreint via les permissions

## Exemples de Requêtes Courantes

```php
// Tous les élèves de l'année active
$current = Student::currentYear()->get();

// Inscrits cette année ET l'année précédente
$current = Student::currentYear()->get();
$previous = Student::excludeCurrentYear()
    ->forSchoolYear($previousYear)
    ->get();

// Tous les bulletins d'un élève
$allReports = Grade::whereHas('student', 
    fn($q) => $q->where('id', $studentId)
)->allYears()->get();

// Comparaison année sur année
$thisYear = Student::currentYear()->count();
$lastYear = Student::forSchoolYearName('2023-2024')->count();
$growth = (($thisYear - $lastYear) / $lastYear) * 100;
```

---

Le système des années scolaires permet une gestion complète du cycle académique avec accès transparent aux données historiques et à l'année actuelle.
