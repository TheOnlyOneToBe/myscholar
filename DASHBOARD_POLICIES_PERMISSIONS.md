# Dashboard Policies & Permissions Guide

**Last Updated**: 2026-06-28

---

## 📋 Vue d'ensemble

Ce guide explique comment implémenter les Policies et Permissions pour sécuriser l'accès aux données du dashboard. Chaque composant du dashboard doit vérifier:

1. **Permission**: L'utilisateur a-t-il la permission d'accéder à cette fonctionnalité?
2. **Autorisation (Policy)**: L'utilisateur peut-il accéder à CES données spécifiques?

---

## 🔐 Différence entre Permission et Autorisation

### Permission (can)
- Définie dans la base de données (table `permissions`)
- Assignée aux rôles
- Globale: "Peut voir les notes?" (oui/non)
- **Exemple**: `grades.view_own`

### Autorisation (Policy)
- Définie en code dans les classes Policy
- Vérifiée par l'utilisateur et les données
- Contextuelle: "Peut voir LES NOTES DE JEAN?" 
- **Exemple**: `$user->can('view', $grade)`

---

## 📊 Architecture de Sécurité du Dashboard

```
Dashboard Component
    ↓
1. Check Role: auth()->user()->hasRole('student')
    ↓
2. Check Permission: auth()->user()->hasPermissionTo('grades.view_own')
    ↓
3. Load Data via Service
    ↓
4. Apply Policy Authorization for each item
    └─ $this->authorize('view', $grade);
    └─ Only return data where user is owner
    ↓
5. Display to User
```

---

## 🛡️ Permissions par Module (StudentDashboard)

### Auth Module
```php
// Changement de mot de passe personnel
'auth.change_password'
```

### Students Module
```php
// Voir sa propre information
'students.view_own'

// Voir les élèves de sa classe (chef_classe)
'students.view_by_class'
```

### Grades Module
```php
// Voir ses propres notes
'grades.view_own'

// Voir les notes de sa classe (chef_classe)
'grades.view_by_class'

// Voir les statistiques de classe (chef_classe)
'grades.view_statistics'
```

### Attendance Module
```php
// Voir ses propres présences
'attendance.view_own'

// Voir les présences de sa classe (chef_classe)
'attendance.view_by_class'

// Enregistrer les présences (chef_classe)
'attendance.record'

// Voir les justifications d'absence (chef_classe)
'attendance.view_justifications'
```

### Classes Module
```php
// Voir les informations de classe
'classes.view'

// Éditer sa classe (chef_classe)
'classes.edit'
```

### Billing Module
```php
// Voir les factures (self/étudiant voit ses propres factures)
'billing.view_invoices'

// Voir les paiements (self/étudiant voit ses propres paiements)
'billing.view_payments'
```

### Notifications Module
```php
// Envoyer des emails à sa classe (chef_classe)
'notifications.send_email'
```

---

## 📝 Implémenter une Policy

### Exemple 1: GradePolicy (Existante)

```php
// modules/Grades/Policies/GradePolicy.php
<?php

namespace Modules\Grades\Policies;

use Modules\Auth\Models\User;
use Modules\Grades\Models\Grade;
use Modules\Students\Models\Student;

class GradePolicy
{
    /**
     * Un étudiant peut voir sa propre note
     */
    public function view(User $user, Grade $grade): bool
    {
        // L'utilisateur doit être étudiant
        if (!$user->hasRole('student')) {
            return false;
        }

        // L'utilisateur doit avoir la permission
        if (!$user->hasPermissionTo('grades.view_own')) {
            return false;
        }

        // La note doit appartenir à l'utilisateur
        $student = Student::where('user_id', $user->id)->first();
        if (!$student || $grade->student_id !== $student->id) {
            return false;
        }

        return true;
    }

    /**
     * Chef de classe peut voir les notes de sa classe
     */
    public function viewByClass(User $user, Grade $grade): bool
    {
        // Vérifier la permission
        if (!$user->hasPermissionTo('grades.view_by_class')) {
            return false;
        }

        // Vérifier que c'est le chef de classe de cette classe
        $chefStudent = Student::where('user_id', $user->id)->first();
        if (!$chefStudent || !$chefStudent->isChefClasseOf($grade->student->class_id)) {
            return false;
        }

        return true;
    }

    /**
     * Enseignant peut voir les notes des élèves qu'il enseigne
     */
    public function viewByTeacher(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('enseignant')) {
            return false;
        }

        // Vérifier que l'enseignant enseigne cette matière à cette classe
        return $user->teacheSubjectToClass(
            $grade->subject_id,
            $grade->student->getCurrentClass()->id
        );
    }
}
```

### Enregistrement de la Policy

```php
// Dans AuthServiceProvider ou GradesServiceProvider
protected $policies = [
    Grade::class => GradePolicy::class,
];
```

---

## 🔑 Vérification dans les Services

### StudentDashboardService

```php
public function getRecentGrades(int $limit = 5): array
{
    $student = $this->getStudent();
    if (!$student) {
        return [];
    }

    return Grade::where('student_id', $student->id)
        ->with('subject')
        ->latest('created_at')
        ->limit($limit)
        ->get()
        ->filter(function ($grade) {
            // Vérifier la permission
            return auth()->user()->can('view', $grade);
        })
        ->map(function ($grade) {
            return [
                'id' => $grade->id,
                'subject' => $grade->subject->name,
                'score' => $grade->score,
                // ... autres champs
            ];
        })
        ->toArray();
}
```

---

## 💻 Vérification dans les Composants Livewire

### StudentGradesSection

```php
<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use App\Services\ModuleManager;

class StudentGradesSection extends Component
{
    public $recentGrades = [];
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $user = auth()->user();
        $moduleManager = app(ModuleManager::class);

        // 1. Vérifier le module
        if (!$moduleManager->canUseModule('Grades')) {
            $this->moduleAvailable = false;
            return;
        }

        // 2. Vérifier la permission
        if (!$user->hasPermissionTo('grades.view_own')) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Vous n\'avez pas la permission de voir les notes';
            return;
        }

        // 3. Vérifier le rôle
        if (!$user->hasRole('student')) {
            $this->moduleAvailable = false;
            return;
        }

        $this->moduleAvailable = true;
        $this->loadGradesData();
    }

    private function loadGradesData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            // Service retourne déjà les données filtrées par policy
            $this->recentGrades = $service->getRecentGrades(5);
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Erreur: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-grades-section');
    }
}
```

---

## 🛠️ Créer une Policy pour un Nouveau Module

### Template de Policy

```php
<?php

namespace Modules\{ModuleName}\Policies;

use Modules\Auth\Models\User;
use Modules\{ModuleName}\Models\{ModelName};
use Modules\Students\Models\Student;

class {ModelName}Policy
{
    /**
     * Déterminer si l'utilisateur peut voir ce modèle
     */
    public function view(User $user, {ModelName} $model): bool
    {
        // 1. Vérifier la permission globale
        if (!$user->hasPermissionTo('{module}.view_own')) {
            return false;
        }

        // 2. Vérifier que l'utilisateur est propriétaire
        if ($user->id !== $model->user_id && !$user->hasRole('admin')) {
            return false;
        }

        return true;
    }

    /**
     * Déterminer si l'utilisateur peut créer
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('{module}.create');
    }

    /**
     * Déterminer si l'utilisateur peut modifier
     */
    public function update(User $user, {ModelName} $model): bool
    {
        if (!$user->hasPermissionTo('{module}.edit')) {
            return false;
        }

        // Vérifier que c'est le propriétaire
        return $user->id === $model->user_id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer
     */
    public function delete(User $user, {ModelName} $model): bool
    {
        return $this->update($user, $model);
    }
}
```

---

## 🔄 Flux de Vérification Complet

### Exemple: Un étudiant accède à ses notes

```
1. StudentDashboardMain monte
   └─ checkModuleAvailability()
      ├─ auth()->user()->hasRole('student') ✓
      ├─ ModuleManager::canUseModule('Grades') ✓
      └─ charger les données

2. StudentGradesSection monte
   └─ checkModuleAvailability()
      ├─ auth()->user()->hasRole('student') ✓
      ├─ ModuleManager::canUseModule('Grades') ✓
      ├─ auth()->user()->hasPermissionTo('grades.view_own') ✓
      └─ loadGradesData()

3. StudentDashboardService->getRecentGrades()
   └─ Grade::where('student_id', $student->id)
      └─ filter(function($grade) {
            auth()->user()->can('view', $grade) // GradePolicy::view()
         })

4. GradePolicy::view()
   ├─ User has role 'student' ✓
   ├─ User has permission 'grades.view_own' ✓
   ├─ Grade belongs to user's student record ✓
   └─ return true ✓

5. Données affichées au composant
   └─ Vue Blade affiche les notes
```

---

## 🚨 Cas Spéciaux: Chef de Classe

### Permissions supplémentaires

```php
public function viewClassGrades(User $user): bool
{
    // Vérifier permission
    if (!$user->hasPermissionTo('grades.view_by_class')) {
        return false;
    }

    // Vérifier que l'utilisateur est chef de classe
    if (!$user->hasRole('chef_classe')) {
        return false;
    }

    // Vérifier qu'il est assigné à une classe
    $student = Student::where('user_id', $user->id)->first();
    if (!$student || !$student->isChefClasse()) {
        return false;
    }

    return true;
}
```

---

## ✅ Checklist de Sécurité pour Chaque Module

- [ ] Table `permissions` contient les permissions du module
- [ ] `PermissionsSeeder.php` assigne les permissions aux rôles
- [ ] Policy classe créée pour chaque modèle sensible
- [ ] Policy enregistrée dans ServiceProvider
- [ ] Service retourne données filtrées par policy
- [ ] Composant Livewire vérifie permissions
- [ ] Routes API vérifient permissions
- [ ] Tests unitaires pour policies
- [ ] Tests d'intégration pour accès aux données
- [ ] Documentation des permissions
- [ ] Audit logging pour actions sensibles

---

## 🔍 Vérification aux Trois Niveaux

### Niveau 1: Composant Livewire
```php
if (!$user->hasPermissionTo('grades.view_own')) {
    return;
}
```

### Niveau 2: Service
```php
->filter(function($grade) {
    return auth()->user()->can('view', $grade);
})
```

### Niveau 3: Policy
```php
public function view(User $user, Grade $grade): bool
{
    // Vérification détaillée
}
```

---

## 📊 Matrice de Permissions - StudentDashboard

| Action | Student | Chef de Classe | Enseignant | Surveillant | Admin |
|--------|:-------:|:-------:|:-------:|:-------:|:-------:|
| Voir ses notes | ✅ | ✅ | ✅ | ❌ | ✅ |
| Voir notes classe | ❌ | ✅ | ✅ | ❌ | ✅ |
| Voir stats classe | ❌ | ✅ | ✅ | ❌ | ✅ |
| Enregistrer présences | ❌ | ✅ | ✅ | ✅ | ✅ |
| Approuver justifications | ❌ | ✅ | ❌ | ❌ | ✅ |
| Voir factures perso | ✅ | ✅ | ❌ | ❌ | ✅ |
| Envoyer emails classe | ❌ | ✅ | ✅ | ❌ | ✅ |

---

## 🚀 Prochaines Étapes

1. [ ] Implémenter toutes les Policies
2. [ ] Ajouter les vérifications de permission dans tous les services
3. [ ] Créer des tests pour chaque policy
4. [ ] Implémenter l'audit logging pour les actions sensibles
5. [ ] Créer un rapport d'audit dans le dashboard admin
6. [ ] Tester les cas de multi-rôle

