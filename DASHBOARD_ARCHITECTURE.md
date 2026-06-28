# Dashboard Architecture - Gestion par Rôles

**Last Updated**: 2026-06-28

---

## 📋 Vue d'ensemble

L'architecture des dashboards est basée sur un système de **rôles multiples** où chaque utilisateur peut avoir plusieurs rôles simultanément. Le système vérifie l'activation des modules avant d'afficher les composants.

---

## 🗂️ Structure des Dossiers

```
modules/Dashboard/
├── DashboardData/
│   ├── StudentDashboard/          # Données pour les élèves
│   ├── TeacherDashboard/          # Données pour les enseignants
│   └── ParentDashboard/           # Données pour les parents
├── Livewire/
│   ├── AdminDashboard.php         # Composant admin (existant)
│   └── StudentDashboard/
│       ├── StudentDashboardMain.php
│       ├── StudentGradesSection.php
│       ├── StudentAttendanceSection.php
│       ├── StudentBillingSection.php
│       ├── StudentClassSection.php
│       └── ChefClasseSection.php
├── Services/
│   ├── DashboardService.php       # Service admin (existant)
│   ├── StudentDashboardService.php
│   └── ModuleAvailabilityService.php
├── Controllers/
│   └── StudentDashboardController.php
├── Resources/views/
│   └── livewire/
│       └── student-dashboard/
│           ├── student-dashboard-main.blade.php
│           ├── student-grades-section.blade.php
│           ├── student-attendance-section.blade.php
│           ├── student-billing-section.blade.php
│           ├── student-class-section.blade.php
│           └── chef-classe-section.blade.php
└── Providers/
    └── DashboardServiceProvider.php
```

---

## 🎯 Composants Disponibles

### StudentDashboard

#### Composant Principal
- **StudentDashboardMain**: Composant racine qui coordonne tous les sous-composants
  - Charge les infos étudiant
  - Affiche les stats rapides
  - Gère la navigation par onglets
  - Détecte le rôle chef_classe

#### Sous-Composants
1. **StudentGradesSection**
   - Module requis: Grades
   - Affiche: Notes récentes, performance par matière, appels en attente
   - Permissions: `grades.view_own`

2. **StudentAttendanceSection**
   - Module requis: Attendance
   - Affiche: Résumé présences, taux présence, statistiques
   - Permissions: `attendance.view_own`

3. **StudentBillingSection**
   - Module requis: Billing
   - Affiche: Factures impayées, solde dû, paiements à venir
   - Permissions: `billing.view_invoices` (propriétaire)

4. **StudentClassSection**
   - Module requis: Classes
   - Affiche: Info classe, effectif, responsable de classe
   - Permissions: `classes.view`

5. **ChefClasseSection** (Multi-rôle)
   - Modules requis: Students, Grades, Attendance, Classes
   - Affiche: Données de gestion de classe
   - Accessible si: `hasRole('chef_classe')`
   - Permissions supplémentaires:
     - `students.view_by_class`
     - `grades.view_by_class`
     - `grades.view_statistics`
     - `attendance.view_by_class`
     - `attendance.record`

---

## 🔐 Vérification des Modules

### ModuleAvailabilityService

Vérifie l'activation des modules avant d'afficher les composants:

```php
$availabilityService = app(ModuleAvailabilityService::class);

// Vérifier si un module est disponible pour un rôle
if ($availabilityService->isModuleAvailableForRole('Grades', 'student')) {
    // Afficher le composant des notes
}

// Obtenir tous les modules disponibles pour un rôle
$modules = $availabilityService->getAvailableModulesForRole('student');
```

### Vérification dans les Composants Livewire

Chaque composant Livewire vérifie automatiquement:

```php
private function checkModuleAvailability(): void
{
    $moduleManager = app(ModuleManager::class);

    // 1. Vérifier si le module est installé et actif
    if (!$moduleManager->canUseModule('Grades')) {
        $this->moduleAvailable = false;
        $this->moduleError = $moduleManager->getModuleError('Grades');
        return;
    }

    // 2. Vérifier les permissions utilisateur
    $user = auth()->user();
    if (!$user || !$user->hasRole('student')) {
        $this->moduleAvailable = false;
        $this->moduleError = 'Permission denied';
        return;
    }

    // 3. Charger les données
    $this->moduleAvailable = true;
    $this->loadData();
}
```

---

## 📊 Flux de Données

```
Livewire Component (Renderer)
    ↓
checkModuleAvailability()
    ├─ ModuleManager::canUseModule()
    ├─ User->hasRole() check
    └─ Load data if available
    ↓
Service (Business Logic)
    ├─ StudentDashboardService
    ├─ Verify tables exist
    └─ Return formatted data
    ↓
Blade View (Display)
    └─ Show data or error message
```

---

## 🎨 Intégration des Rôles Multiples

### Exemple: Étudiant + Chef de Classe

Un utilisateur avec les deux rôles reçoit:

**À partir du rôle "student":**
- Voir ses propres notes
- Voir ses propres présences
- Voir ses factures
- Voir sa classe

**À partir du rôle "chef_classe":**
- Voir les notes de toute la classe
- Voir les présences de toute la classe
- Enregistrer les présences
- Approuver les justifications d'absence
- Voir les statistiques de classe
- Envoyer des emails à la classe

### Implémentation

```php
// Dans StudentDashboardMain.php
$this->isChefClasse = $service->isChefClasse();

// Dans la vue
@if($isChefClasse)
    <button wire:click="switchTab('chef-classe')">
        Chef de Classe 👨‍💼
    </button>
@endif
```

---

## ➕ Ajouter un Nouveau Composant

### Étape 1: Créer le Composant Livewire

```php
// modules/Dashboard/Livewire/StudentDashboard/NewComponentSection.php
<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use App\Services\ModuleManager;

class NewComponentSection extends Component
{
    public $data = [];
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('ModuleName')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('ModuleName');
            return;
        }

        $user = auth()->user();
        if (!$user || !$user->hasRole('student')) {
            $this->moduleAvailable = false;
            return;
        }

        $this->moduleAvailable = true;
        $this->loadData();
    }

    private function loadData(): void
    {
        try {
            $service = app(SomeService::class);
            $this->data = $service->getData();
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.new-component-section');
    }
}
```

### Étape 2: Créer la Vue Blade

```blade
{{-- modules/Dashboard/resources/views/livewire/student-dashboard/new-component-section.blade.php --}}
<div class="new-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">Section Title</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Module not available: {{ $moduleError }}</p>
        </div>
    @else
        <!-- Afficher les données -->
    @endif
</div>
```

### Étape 3: Enregistrer dans le ServiceProvider

```php
// Dans DashboardServiceProvider.php
Livewire::component(
    'new-component-section',
    \Modules\Dashboard\Livewire\StudentDashboard\NewComponentSection::class
);
```

### Étape 4: Ajouter dans la Vue Principale

```blade
{{-- Dans student-dashboard-main.blade.php --}}
<livewire:new-component-section />
```

---

## 🔑 Permissions par Rôle

### Student
- `students.view_own`
- `grades.view_own`
- `attendance.view_own`
- `auth.change_password`

### Chef de Classe (Supplémentaires)
- `students.view_by_class`
- `grades.view_by_class`
- `grades.view_statistics`
- `attendance.view_by_class`
- `attendance.record`
- `attendance.view_justifications`
- `classes.view`
- `notifications.send_email`

### Enseignant
- `students.view_by_class`
- `grades.create`
- `grades.edit`
- `grades.view_by_class`
- `attendance.view_by_class`
- `attendance.record`
- `classes.view`

### Surveillant
- `students.view_by_class`
- `attendance.view_by_class`
- `attendance.record`
- `attendance.view_justifications`
- `classes.view`

---

## 📱 Points d'Entrée API

```
GET /api/dashboard/student/                # Dashboard complet
GET /api/dashboard/student/grades          # Onglet Notes
GET /api/dashboard/student/attendance      # Onglet Présences
GET /api/dashboard/student/billing         # Onglet Facturation
GET /api/dashboard/student/profile         # Profil étudiant
GET /api/dashboard/student/chef-classe     # Données chef de classe
```

---

## ✅ Checklist de Vérification

Avant d'ajouter un nouveau module au dashboard:

- [ ] Module installé et activé
- [ ] Permissions définies dans `PermissionsSeeder.php`
- [ ] Service créé pour récupérer les données
- [ ] Composant Livewire créé avec vérification de module
- [ ] Vue Blade créée avec gestion d'erreur
- [ ] Composant enregistré dans `DashboardServiceProvider`
- [ ] Composant intégré dans la vue principale
- [ ] Tests unitaires créés
- [ ] Tests d'intégration créés
- [ ] Documentation mise à jour

---

## 🚀 Prochaines Étapes

1. Créer les dashboards pour les autres rôles:
   - **TeacherDashboard**: Notes, Présences, Classes
   - **ParentDashboard**: Notes enfant, Présences enfant, Facturation enfant
   - **AdminDashboard**: Statistiques système (existant, à améliorer)

2. Ajouter les policies d'autorisation:
   - Vérifier que l'utilisateur ne peut voir que ses propres données
   - Implémenter les vérifications au niveau de la base de données

3. Implémenter les fonctionnalités chef_classe:
   - Enregistrement des présences
   - Approbation des justifications
   - Envoi de messages à la classe

4. Ajouter les tests:
   - Tests unitaires pour les services
   - Tests d'intégration pour les API
   - Tests de UI/UX avec Dusk

