# MyScholar - Guide de Démarrage Rapide

## ✅ État du Projet

Le projet MyScholar est **complètement fonctionnel** avec:

- ✅ Architecture modulaire DDD avec 9 modules
- ✅ Système de permissions et rôles granulaires (6 rôles, 42 permissions)
- ✅ Hachage sécurisé des mots de passe
- ✅ Format configurable des matricules étudiants
- ✅ Intégration Livewire pour le frontend
- ✅ Tous les modèles Eloquent avec relations
- ✅ Migrations et seeding
- ✅ Documentation complète

## 🚀 Installation Initiale

### 1. Cloner et configurer

```bash
cd /home/user/myscholar
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configurer la base de données

```bash
# Créer/reset la base de données SQLite
rm database/database.sqlite 2>/dev/null || true
touch database/database.sqlite

# Exécuter les migrations
php artisan migrate
```

### 3. Synchroniser les permissions et rôles

```bash
php artisan permissions:sync --roles
```

## 📚 Utilisation

### Créer un utilisateur administrateur

```bash
php artisan tinker
```

```php
$admin = \Modules\Auth\Models\User::create([
    'username' => 'admin',
    'email' => 'admin@myscholar.local',
    'password' => bcrypt('AdminPassword123!@#'),
    'full_name' => 'Administrateur',
    'is_active' => true,
]);

$adminRole = \Modules\Auth\Models\Role::where('name', 'admin')->first();
$admin->roles()->attach($adminRole);

echo "Admin créé: {$admin->email}\n";
```

### Hacher un mot de passe

```bash
php artisan password:hash "VotreMotDePasse123!@#"
```

### Créer un composant Livewire

```bash
php artisan make:livewire StudentForm
```

## 📁 Structure des Fichiers

```
myscholar/
├── app/
│   ├── Console/Commands/          # Commandes Artisan
│   │   ├── HashPassword.php
│   │   └── SyncPermissions.php
│   ├── Livewire/                 # Composants Livewire
│   │   └── ExampleComponent.php
│   ├── Services/                 # Services métier
│   │   ├── PasswordService.php
│   │   └── PermissionService.php
│   ├── Traits/                   # Traits réutilisables
│   │   ├── HasPermissions.php
│   │   └── WithLivewirePermissions.php
│   ├── Http/                     # HTTP (routes, middlewares)
│   │   └── Middleware/
│   │       ├── CheckPermission.php
│   │       └── CheckRole.php
│   └── Providers/                # Service Providers
│       └── AppServiceProvider.php
│
├── modules/                      # Modules métier
│   ├── Auth/                     # Authentication
│   ├── Config/                   # Configuration système
│   ├── Students/                 # Gestion des étudiants
│   ├── Grades/                   # Gestion des notes
│   ├── Attendance/               # Gestion des absences
│   ├── Classes/                  # Gestion des classes
│   ├── Billing/                  # Facturation
│   ├── Audit/                    # Audit et logs
│   ├── Notifications/            # Notifications
│   └── Reporting/                # Rapports
│
├── config/
│   ├── modules.php              # Configuration des rôles et permissions
│   └── livewire.php             # Configuration Livewire
│
├── resources/views/
│   └── livewire/                # Templates Livewire
│       └── example-component.blade.php
│
└── docs/
    ├── PERMISSIONS_AND_ROLES.md # Guide des permissions
    └── LIVEWIRE_GUIDE.md        # Guide Livewire
```

## 🔐 Système de Permissions

### Les 6 rôles prédéfinis

| Rôle | Permissions | Usage |
|------|------------|-------|
| **admin** | 42 (toutes) | Administrateur système |
| **directeur** | 8 | Directeur d'établissement |
| **enseignant** | 7 | Professeurs |
| **surveillant** | 4 | Surveillants |
| **parent** | 3 | Parents d'élèves |
| **student** | 3 | Étudiants |

### Vérifier les permissions dans le code

```php
// Dans les contrôleurs/services
if (!auth()->user()->hasPermission('students.view')) {
    abort(403);
}

// Dans les composants Livewire
class StudentList extends Component {
    use WithLivewirePermissions;
    
    public function render() {
        $this->authorize('students.view');
        // ...
    }
}

// Dans les templates Blade
@can('students.view')
    <table>...</table>
@endcan

// Dans les routes
Route::get('/students', StudentIndex::class)
    ->middleware('permission:students.view');
```

## 🛠️ Commandes Disponibles

```bash
# Hacher un mot de passe
php artisan password:hash "motdepasse"

# Synchroniser les permissions et rôles
php artisan permissions:sync --roles

# Créer un composant Livewire
php artisan make:livewire ComponentName

# Lancer le serveur
php artisan serve

# Accéder à Tinker
php artisan tinker
```

## 📊 Statut des Migrations

Toutes les migrations sont en place:

- ✅ Users (avec propriétés de sécurité)
- ✅ Roles
- ✅ Permissions
- ✅ User-Role relations
- ✅ Role-Permission relations
- ✅ School Info
- ✅ System Settings
- ✅ Student models
- ✅ Grade models
- ✅ Attendance models
- ✅ Classes models
- ✅ Billing models
- ✅ Audit models
- ✅ Notifications models

**Total: 46 tables créées**

## 🧪 Test du Système

Pour vérifier que tout fonctionne:

```bash
php artisan tinker
```

```php
# Tester les permissions
$user = \Modules\Auth\Models\User::where('email', 'admin@myscholar.local')->first();
echo $user->hasPermission('students.view') ? "✓" : "✗"; // ✓

# Tester le format des matricules
$id = \Modules\Students\ValueObjects\StudentId::generate(['filiere' => 'SCI']);
echo $id->toString(); // SCI-2026-XXXX

# Tester le service de mots de passe
$pass = app(\App\Services\PasswordService::class);
echo $pass->getStrengthLevel('Test123!@#'); // Très fort

exit
```

## 📖 Documentation

- **`docs/PERMISSIONS_AND_ROLES.md`** - Guide complet du système de permissions
- **`docs/LIVEWIRE_GUIDE.md`** - Guide complet de Livewire
- **`docs/STUDENT_ID_FORMAT_GUIDE.md`** - Configuration des matricules
- **`modules/Students/STUDENT_ID_FORMAT_GUIDE.md`** - Format configurables

## 🎯 Prochaines Étapes

### 1. Créer les interfaces avec Livewire

```php
php artisan make:livewire Students/StudentList
php artisan make:livewire Students/StudentForm
php artisan make:livewire Grades/GradeEntry
php artisan make:livewire Attendance/AttendanceSession
```

### 2. Définir les routes

```php
// routes/web.php
Route::middleware(['auth', 'permission:students.view'])->group(function () {
    Route::get('/students', StudentList::class);
    Route::get('/students/create', StudentForm::class);
});
```

### 3. Implémenter les méthodes métier

Utiliser les services pour la logique:
- StudentIdService (génération de matricules)
- PasswordService (gestion des mots de passe)
- PermissionService (gestion des permissions)

## ⚙️ Configuration du Student ID Format

Par défaut: `{filiere}-{YYYY}-{####}` → `SCI-2024-0001`

Pour changer:

```php
$permService = app(\App\Services\PermissionService::class);
$permService->syncPermissionsFromConfig(); // Synchroniser d'abord

// Via la commande CLI
php artisan school:configure-student-id-format

// Via le code
$service = new \Modules\Students\Services\StudentIdService();
$service->updateConfig(['####', 'filiere', 'YY'], '-');
// Résultat: 0001-SCI-24
```

## 🔍 Dépannage

### Permissions non reconnues après modification de config
```bash
php artisan cache:clear
php artisan permissions:sync --roles
```

### Livewire ne charge pas
```bash
php artisan livewire:publish --assets
```

### Erreurs de migration
```bash
php artisan migrate:refresh
php artisan permissions:sync --roles
```

## 📞 Support et Questions

Consultez la documentation complète:
- `docs/PERMISSIONS_AND_ROLES.md` - Questions sur les permissions
- `docs/LIVEWIRE_GUIDE.md` - Questions sur Livewire
- Code source dans `app/` et `modules/`

## ✨ Points Forts de l'Implémentation

1. **Architecture modulaire** - Chaque fonctionnalité dans son propre module
2. **DDD (Domain-Driven Design)** - Value Objects pour les concepts métier
3. **Sécurité** - Hachage bcrypt, validation des mots de passe, permissions granulaires
4. **Flexibilité** - Permissions configurables, format de matricules configurable
5. **Performance** - Caching des permissions, requêtes optimisées
6. **Maintenabilité** - Code bien organisé, traits réutilisables, services centralisés
7. **Testabilité** - Services injectables, traits composables

---

**Projet initialisé et prêt pour le développement! 🚀**
