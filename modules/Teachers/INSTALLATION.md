# Installation du Module Teachers

## 🎯 Objectif
Gestion complète des enseignants avec leurs qualifications, matières et assignations aux classes.

## 📋 Nouveautés

### ✅ Nouveaux Rôles Créés
1. **Secrétaire** (niveau 3) - Administration générale
2. **Comptable/Trésorier** (niveau 3) - Gestion financière
3. **Infirmier Scolaire** (niveau 3) - Santé scolaire
4. **Bibliothécaire/Documentaliste** (niveau 3) - Documentation
5. **Gardien/Agent d'Entretien** (niveau 4) - Maintenance

### ✅ Structure Adaptée au Cameroun
- **Filières** : Générale et Technique (pas de "département")
- **Rôles hiérarchisés** : 9 rôles de base + 5 nouveaux = 14 rôles totaux
- **Permissions granulaires** : Contrôle d'accès par action

## 📦 Fichiers Créés

### Migrations
- `2024_01_01_000001_create_teachers_table.php` - Profil enseignant
- `2024_01_01_000002_create_teacher_qualifications_table.php` - Diplômes
- `2024_01_01_000003_create_teacher_subjects_table.php` - Matières (Many-to-Many)
- `2024_01_01_000004_create_teacher_classes_table.php` - Classes assignées
- `2024_01_01_000005_create_teacher_history_table.php` - Historique

### Modèles
- `Models/Teacher.php` - Modèle enseignant principal
- `Models/TeacherQualification.php` - Qualifications
- `Models/TeacherClass.php` - Assignations classes
- `Models/TeacherHistory.php` - Historique

### Contrôleurs
- `Controllers/TeacherController.php` - CRUD + informations
- `Controllers/TeacherAssignmentController.php` - Gestion assignations

### Permissions & Rôles
- `permissions.json` - 10 permissions Teachers
- `Seeders/TeachersPermissionsSeeder.php` - Création permissions
- `Seeders/AdditionalRolesSeeder.php` - 5 nouveaux rôles
- `Seeders/TeacherRolePermissionsSeeder.php` - Assignation permissions

### Autres
- `Routes/api.php` - API endpoints
- `Policies/TeacherPolicy.php` - Autorisations
- `Providers/TeacherServiceProvider.php` - Service provider
- `Tests/Unit/TeacherModelTest.php` - Tests unitaires

## 🚀 Installation

### 1. Enregistrer le Service Provider
Dans `config/app.php` ou `bootstrap/providers.php`, ajouter :
```php
Modules\Teachers\Providers\TeacherServiceProvider::class,
```

### 2. Exécuter les migrations
```bash
php artisan migrate
```

### 3. Seeder les données
```bash
php artisan db:seed --class="Modules\\Teachers\\Seeders\\DatabaseSeeder"
```

Cela créera :
- ✅ 5 nouveaux rôles (Secrétaire, Comptable, Infirmier, Bibliothécaire, Gardien)
- ✅ 10 permissions Teachers
- ✅ Assignations de permissions aux rôles appropriés

## 📊 Hiérarchie Complète Après Installation

```
NIVEAU 0
└─ super_administrator (Administrateur Système)

NIVEAU 1
└─ proviseur (Directeur Général)

NIVEAU 2
└─ censeur (Responsable Pédagogique)

NIVEAU 3
├─ prof_principal (Professeur Principal)
├─ chef_classe (Chef de Classe)
├─ secretaire (Secrétaire) ← NOUVEAU
├─ comptable (Comptable/Trésorier) ← NOUVEAU
├─ infirmier (Infirmier Scolaire) ← NOUVEAU
└─ bibliothecaire (Bibliothécaire) ← NOUVEAU

NIVEAU 4
├─ enseignant (Professeur)
└─ gardien (Agent d'Entretien) ← NOUVEAU

NIVEAU 5
└─ surveillant (Surveillant/Pion)

NIVEAU 99
└─ parent (Parent/Tuteur)

NIVEAU 100
└─ student (Élève)
```

## 🔍 Tests

Exécuter les tests du module :
```bash
php artisan test modules/Teachers/Tests/Unit/
```

## 📝 Utilisation

### Créer un enseignant
```php
$teacher = Teacher::create([
    'user_id' => $user->id,
    'teacher_code' => 'PROF001',
    'specialization' => 'Mathématiques',
    'qualification_level' => 'Master',
    'filiere' => 'generale',
    'hire_date' => '2020-09-01',
    'years_of_experience' => 5,
]);
```

### Assigner une matière
```php
$teacher->subjects()->attach($mathSubject->id, [
    'proficiency_level' => 5,
    'since_year' => 2020,
    'is_primary' => true,
]);
```

### Assigner à une classe
```php
$teacher->classes()->attach($class->id, [
    'subject_id' => $mathSubject->id,
    'school_year_id' => $schoolYear->id,
    'hours_per_week' => 12,
    'status' => 'active',
]);
```

## ✅ Checklist Post-Installation

- [ ] Migrations exécutées
- [ ] Service Provider enregistré
- [ ] Seeders exécutés
- [ ] Nouveaux rôles visibles dans la DB
- [ ] Permissions assignées correctement
- [ ] Tests passent
- [ ] API endpoints accessibles
