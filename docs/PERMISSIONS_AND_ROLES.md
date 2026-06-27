# Système de Permissions et Rôles - MyScholar

## Vue d'ensemble

MyScholar utilise un système granulaire de permissions et de rôles pour contrôler l'accès aux fonctionnalités de l'application. Le système est configuré dans `config/modules.php` et peut être synchronisé avec la base de données via une commande.

## Configuration Initiale

### 1. Synchroniser les permissions et rôles

```bash
php artisan permissions:sync --roles
```

Cela crée:
- **42 permissions** réparties dans 8 modules
- **6 rôles prédéfinis** avec leurs permissions respectives

## Rôles Disponibles

| Rôle | Description | Cas d'usage |
|------|-------------|-----------|
| **admin** | Accès complet | Administrateur système |
| **directeur** | Gestion complète | Directeur d'établissement |
| **enseignant** | Gestion des notes | Professeurs |
| **surveillant** | Suivi des présences | Surveillants |
| **parent** | Accès limité | Parents d'élèves |
| **student** | Accès en lecture | Étudiants |

## Permissions par Module

### Config Module
- `config.view` - Voir la configuration
- `config.edit` - Modifier la configuration
- `config.school_info` - Gérer les informations du lycée
- `config.settings` - Gérer les paramètres système

### Auth Module
- `auth.users.view` - Voir les utilisateurs
- `auth.users.create` - Créer des utilisateurs
- `auth.users.edit` - Modifier les utilisateurs
- `auth.users.delete` - Supprimer les utilisateurs
- `auth.roles.view` - Voir les rôles
- `auth.roles.edit` - Modifier les rôles
- `auth.permissions.manage` - Gérer les permissions

### Students Module
- `students.view` - Voir les étudiants
- `students.view_own` - Voir ses propres informations
- `students.create` - Créer des étudiants
- `students.edit` - Modifier les étudiants
- `students.delete` - Supprimer les étudiants
- `students.export` - Exporter les données étudiants

### Grades Module
- `grades.view` - Voir les notes
- `grades.view_own` - Voir ses propres notes
- `grades.create` - Créer des notes
- `grades.edit` - Modifier les notes
- `grades.delete` - Supprimer les notes
- `grades.appeal` - Contester une note

### Attendance Module
- `attendance.view` - Voir les présences
- `attendance.view_own` - Voir ses présences
- `attendance.create` - Créer des enregistrements
- `attendance.edit` - Modifier les présences
- `attendance.justifications` - Gérer les justificatifs

### Classes Module
- `classes.view` - Voir les classes
- `classes.create` - Créer des classes
- `classes.edit` - Modifier les classes
- `classes.delete` - Supprimer les classes
- `classes.assignments` - Gérer les affectations

### Billing Module
- `billing.view` - Voir la facturation
- `billing.invoices` - Gérer les factures
- `billing.payments` - Gérer les paiements
- `billing.scholarships` - Gérer les bourses
- `billing.reports` - Voir les rapports

### Audit Module
- `audit.logs` - Voir les journaux d'audit
- `audit.manage` - Gérer les journaux

## Utilisation avec Livewire

### 1. Créer un Composant avec Permissions

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\WithLivewirePermissions;

class StudentForm extends Component
{
    use WithLivewirePermissions;

    public function createStudent()
    {
        // Vérifier la permission avant d'exécuter
        $this->authorize('students.create');

        // L'action continue...
        Student::create([...]);
    }

    public function render()
    {
        return view('livewire.student-form', [
            'canCreate' => $this->userCan('students.create'),
            'canEdit' => $this->userCan('students.edit'),
            'canDelete' => $this->userCan('students.delete'),
        ]);
    }
}
```

### 2. Utiliser dans les Templates Livewire

```blade
@if($canCreate)
    <button wire:click="createStudent" class="btn btn-primary">
        Créer un étudiant
    </button>
@endif

@if($userHasRole('directeur'))
    <div class="admin-panel">
        <!-- Panel pour les directeurs -->
    </div>
@endif

@if($userCanAny(['students.edit', 'students.delete']))
    <div class="actions">
        <!-- Actions d'édition/suppression -->
    </div>
@endif
```

### 3. Méthodes Disponibles dans WithLivewirePermissions

```php
// Vérifier une permission
$this->userCan('students.view')

// Vérifier plusieurs permissions (au moins une)
$this->userCanAny(['students.edit', 'students.delete'])

// Vérifier un rôle
$this->userHasRole('directeur')

// Autoriser une action (lève une exception)
$this->authorize('students.create')

// Obtenir l'utilisateur actuel
$this->getCurrentUser()

// Obtenir les permissions de l'utilisateur
$this->getCurrentUserPermissions()

// Obtenir les rôles de l'utilisateur
$this->getCurrentUserRoles()
```

## Utilisation avec Blade

### Directives Blade Personnalisées

```blade
<!-- Vérifier une permission -->
@can('students.view')
    <p>Vous pouvez voir les étudiants</p>
@endcan

<!-- Vérifier un rôle -->
@hasRole('directeur')
    <p>Vous êtes un directeur</p>
@endhasRole

<!-- Vérifier plusieurs permissions -->
@canAny(['students.edit', 'students.delete'])
    <p>Vous pouvez éditer ou supprimer des étudiants</p>
@endcanAny
```

## Utilisation avec les Routes

### Middleware de Permission

```php
Route::get('/students', StudentIndex::class)
    ->middleware('permission:students.view');

Route::post('/students', StudentStore::class)
    ->middleware('permission:students.create');
```

### Middleware de Rôle

```php
Route::group(['middleware' => 'role:directeur'], function () {
    // Routes seulement pour les directeurs
});
```

## Service de Gestion des Permissions

### PermissionService

```php
$permService = app(\App\Services\PermissionService::class);

// Vérifier une permission pour un utilisateur
$permService->hasPermission($user, 'students.view');

// Vérifier plusieurs permissions
$permService->hasAnyPermission($user, ['students.edit', 'students.delete']);

// Obtenir toutes les permissions d'un utilisateur
$permService->getUserPermissions($user);

// Obtenir tous les rôles d'un utilisateur
$permService->getUserRoles($user);

// Obtenir les permissions disponibles
$permService->getAvailablePermissions();

// Obtenir les permissions d'un module
$permService->getModulePermissions('students');

// Accorder une permission à un rôle
$permService->grantPermissionToRole($role, 'students.create');

// Révoquer une permission d'un rôle
$permService->revokePermissionFromRole($role, 'students.create');

// Invalider le cache
$permService->clearCache();
$permService->clearCache($userId); // Pour un utilisateur spécifique
```

## Service de Gestion des Mots de Passe

### PasswordService

```php
$passwordService = app(\App\Services\PasswordService::class);

// Hacher un mot de passe
$hash = $passwordService->hash('password123');

// Vérifier un mot de passe
$passwordService->check('password123', $hash); // true

// Générer un mot de passe aléatoire
$randomPassword = $passwordService->generateRandomPassword(16);

// Valider la force du mot de passe
$validation = $passwordService->validateStrength('MyPassword123!');
// Retourne: ['valid' => bool, 'errors' => []]

// Obtenir le niveau de force
$level = $passwordService->getStrengthLevel('MyPassword123!');
// Retourne: 'Très fort', 'Fort', 'Moyen', 'Faible', 'Très faible'

// Vérifier si un hash doit être rehashé
$passwordService->needsRehash($hash);
```

## Commandes Console

### Hash Password

```bash
php artisan password:hash

# Ou avec le mot de passe en argument
php artisan password:hash "MyPassword123!"
```

Affiche:
- Hash généré
- Niveau de force
- Avertissements si faible

### Synchroniser les Permissions

```bash
# Synchroniser uniquement les permissions
php artisan permissions:sync

# Synchroniser permissions et rôles
php artisan permissions:sync --roles
```

## Ajouter une Nouvelle Permission

1. Ajouter dans `config/modules.php`:

```php
'permissions' => [
    'students' => [
        'students.import' => 'Importer des étudiants',
        'students.bulk_edit' => 'Édition en masse',
    ],
],
```

2. Synchroniser:

```bash
php artisan permissions:sync --roles
```

3. Assigner aux rôles via le service:

```php
$permService = app(\App\Services\PermissionService::class);
$directeur = Role::where('name', 'directeur')->first();
$permService->grantPermissionToRole($directeur, 'students.import');
```

## Ajouter un Nouveau Rôle

1. Ajouter dans `config/modules.php`:

```php
'roles' => [
    'coordinateur' => [
        'name' => 'Coordinateur',
        'description' => 'Coordinateur pédagogique',
        'permissions' => [
            'students.view',
            'grades.view',
            'classes.view',
        ],
    ],
],
```

2. Synchroniser:

```bash
php artisan permissions:sync --roles
```

## Bonnes Pratiques

1. **Toujours vérifier les permissions** avant d'exécuter des actions sensibles
2. **Utiliser le caching** - le PermissionService met en cache les permissions
3. **Grouper les permissions** - utilisez des conventions de nommage cohérentes
4. **Audit** - enregistrez toutes les actions sensibles
5. **Moindre privilège** - accordez uniquement les permissions nécessaires
6. **Tester les permissions** - testez le contrôle d'accès à chaque niveau

## Exemples Complets

### Exemple 1: Liste des Étudiants avec Actions

```php
// StudentList.php (Livewire Component)
class StudentList extends Component
{
    use WithLivewirePermissions;

    public function render()
    {
        $students = Student::all();
        
        return view('livewire.student-list', [
            'students' => $students,
            'canCreate' => $this->userCan('students.create'),
            'canEdit' => $this->userCan('students.edit'),
            'canDelete' => $this->userCan('students.delete'),
            'canExport' => $this->userCan('students.export'),
        ]);
    }

    public function delete($studentId)
    {
        $this->authorize('students.delete');
        Student::find($studentId)->delete();
    }

    public function export()
    {
        $this->authorize('students.export');
        // Export logic...
    }
}
```

```blade
<!-- student-list.blade.php -->
<table>
    @foreach($students as $student)
        <tr>
            <td>{{ $student->name }}</td>
            <td>
                @if($canEdit)
                    <a href="{{ route('students.edit', $student) }}">Éditer</a>
                @endif
                @if($canDelete)
                    <button wire:click="delete({{ $student->id }})">Supprimer</button>
                @endif
            </td>
        </tr>
    @endforeach
</table>

@if($canExport)
    <button wire:click="export">Exporter</button>
@endif
```

### Exemple 2: Formulaire avec Validation de Rôle

```php
class UserForm extends Component
{
    use WithLivewirePermissions;

    public function submit()
    {
        // Seul un admin peut créer d'autres admins
        if ($this->role === 'admin' && !$this->userHasRole('admin')) {
            throw new \Exception('Seul un admin peut créer des admins');
        }

        User::create([...]);
    }
}
```

## Dépannage

### Les permissions ne sont pas mises à jour
- Vérifiez le cache: `php artisan cache:clear`
- Resynchronisez: `php artisan permissions:sync --roles`

### Un utilisateur n'a pas les permissions attendues
- Vérifiez les rôles: `php artisan tinker` → `User::find(1)->getRoles()`
- Vérifiez les permissions: `User::find(1)->getPermissions()`

### Une permission ne fonctionne pas
- Vérifiez que l'ID de permission est correct dans la config
- Vérifiez que la permission a été synchronisée dans la DB
- Utilisez `@can('permission_id')` pas `@can('Permission Name')`
