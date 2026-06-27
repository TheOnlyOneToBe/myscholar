# Système d'Authentification MyScholar

## Vue d'ensemble

Le système d'authentification de MyScholar a été complètement implémenté pour le contexte du système éducatif camerounais. Il comprend :

- **9 rôles** hiérarchisés (admin, proviseur, censeur, prof_principal, chef_classe, enseignant, surveillant, parent, élève)
- **50+ permissions** répartis sur 8 modules
- **Rôles multiples par utilisateur** avec support temporaire (examens, remplacements)
- **Gestion des mots de passe** avec historique et politique de non-réutilisation
- **Tokens API** Sanctum avec expiration 2 jours
- **Audit de sécurité** : tentatives de connexion, verrouillage de compte

---

## Architecture

### Modèles

#### 1. User
- Relations avec UserRole pour support de rôles multiples
- Méthodes : `currentRoles()`, `canCreateRole()`, `assignRole()`
- Permissions via les rôles

#### 2. Role
- Hiérarchie : niveau 0 (admin) à 100 (élève)
- Catégories : admin, hierarchy, staff, external
- Méthode clé : `canCreateRole()` - détermine quels rôles un rôle peut créer

#### 3. UserRole
- Table de liaison avec dates temporelles
- Champs : `started_at`, `ended_at` (nullable)
- Support pour rôles temporaires (surveillant pendant examens)

#### 4. Permission
- Scope : global, by_class, by_subject, by_student
- Module et catégorie pour organisation
- Relation many-to-many avec Role via `role_permissions`

#### 5. Sécurité
- **PasswordHistory** : historique des mots de passe (5 derniers)
- **LoginAttempt** : audit des tentatives (email, IP, succès/échec)
- **PasswordReset** : tokens pour réinitialisation (expiration 1h)

---

## Services

### AuthService
```php
// Login avec gestion du verrouillage de compte
$result = $authService->login(email_or_username, password, ip_address);

// Changement de mot de passe
$authService->changePassword(user, current_password, new_password);

// Déconnexion (révoque tokens)
$authService->logout(user);
```

### AccountLockingService
- Verrouille après 5 tentatives échouées en 15 minutes
- Bloque IP après 10 tentatives échouées
- Méthodes : `isAccountLocked()`, `getMinutesUntilUnlock()`

### PasswordResetService
- Génère tokens SHA256
- Validité : 1 heure
- Vérification : nouveau mot de passe dans historique

### UserManagementService
- Création d'utilisateur avec validation de permissions
- Attribution/retrait de rôles
- Activation/désactivation de comptes
- Respect de la hiérarchie : on ne peut assigner que les rôles en dessous de soi

---

## Hiérarchie des Rôles

| Niveau | Rôle | Catégorie | Permissions |
|--------|------|-----------|------------|
| 0 | Admin | admin | Tous |
| 1 | Proviseur | hierarchy | Tous sauf `auth.manage_permissions` |
| 2 | Censeur | hierarchy | Gestion académique + discipline |
| 3 | Prof Principal | hierarchy | Gestion de classe + élèves |
| 3 | Chef de Classe | staff | Limité (sa classe) |
| 4 | Enseignant | staff | Pédagogie (saisie notes) |
| 5 | Surveillant | staff | Présences + discipline |
| 99 | Parent | external | Lecture seule (son enfant) |
| 100 | Élève | external | Lecture seule (ses données) |

### Règle de Création de Rôle

- **Admin (0)** : peut créer tous les rôles SAUF admin
- **Autres hiérarchiques** : peuvent créer les rôles EN DESSOUS d'eux

```php
// Exemple
$proviseur->canCreateRole($enseignantRole); // true
$proviseur->canCreateRole($proviseurRole);  // false
```

---

## API Endpoints

### Authentification (Public)

```
POST   /api/auth/login
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
POST   /api/auth/validate-token
```

### Authentification (Protégé)

```
POST   /api/auth/logout
GET    /api/auth/me
POST   /api/auth/change-password
```

### Gestion d'Utilisateurs (Protégé)

```
GET    /api/auth/users                      # List
POST   /api/auth/users                      # Create
GET    /api/auth/users/{id}                 # Show
PUT    /api/auth/users/{id}                 # Update
POST   /api/auth/users/{id}/assign-role     # Assigner rôle
POST   /api/auth/users/{id}/remove-role     # Retirer rôle
POST   /api/auth/users/{id}/deactivate      # Désactiver
POST   /api/auth/users/{id}/activate        # Activer
```

### Gestion des Rôles (Protégé)

```
GET    /api/auth/roles                      # List
GET    /api/auth/roles/{id}                 # Show
GET    /api/auth/roles/{id}/permissions     # Permissions du rôle
POST   /api/auth/roles/{id}/give-permissions
POST   /api/auth/roles/{id}/revoke-permissions
```

### Gestion des Permissions (Protégé)

```
GET    /api/auth/permissions                # List
GET    /api/auth/permissions/{id}           # Show
GET    /api/auth/permissions/by-module      # Filtrer par module
GET    /api/auth/me/permissions             # Mes permissions
POST   /api/auth/me/check-permission        # Vérifier une permission
```

---

## Middleware

### `auth:sanctum`
Vérifie la présence d'un token Sanctum valide (standard Laravel).

### `check.permission:permission.id`
```php
Route::post('/admin', Controller@action)
    ->middleware('auth:sanctum', 'check.permission:config.edit_system_settings');
```

### `check.role:admin,proviseur`
```php
Route::post('/sensitive', Controller@action)
    ->middleware('auth:sanctum', 'check.role:admin,proviseur');
```

---

## Validation

### LoginRequest
```php
'email_or_username' => 'required|string|max:255'
'password'          => 'required|string|min:8'
```

### CreateUserRequest
```php
'first_name'       => 'required|string|max:100'
'last_name'        => 'required|string|max:100'
'email'            => 'required|email|unique:users'
'password'         => 'required|string|min:10|confirmed'
'role_id'          => 'required|exists:roles'
```

### ChangePasswordRequest
```php
'current_password'      => 'required|string'
'new_password'          => 'required|string|min:10|confirmed|different:current'
```

### ResetPasswordRequest
```php
'email'            => 'required|email'
'token'            => 'required|string'
'password'         => 'required|string|min:10|confirmed'
```

---

## Permissions par Module

### Auth Module (11 permissions)
- `auth.view_users` - Voir liste utilisateurs
- `auth.create_user` - Créer utilisateur
- `auth.edit_user` - Modifier utilisateur
- `auth.delete_user` - Supprimer utilisateur
- `auth.view_roles` - Voir rôles
- `auth.assign_role` - Assigner rôles
- `auth.view_permissions` - Voir permissions
- `auth.manage_permissions` - Admin seul
- `auth.change_password` - Tous
- `auth.reset_password` - Avec permission
- `auth.view_login_attempts` - Audit

### Config Module (4 permissions)
- `config.view_school_info`
- `config.edit_school_info`
- `config.manage_logo`
- `config.edit_system_settings`

### Audit Module (3 permissions)
- `audit.view_logs`
- `audit.export_logs`
- `audit.delete_logs`

### Autres Modules (32+ permissions)
- Students, Grades, Attendance, Classes, Billing, Notifications

---

## Flux d'Authentification

### 1. Login
```
POST /api/auth/login
{
  "email_or_username": "user@example.com",
  "password": "password123"
}

Response:
{
  "success": true,
  "user": { ... },
  "token": "token_sanctum_ici",
  "expires_at": "2024-06-29T15:33:00Z"
}
```

### 2. Utilisation du Token
```
GET /api/auth/me
Authorization: Bearer token_sanctum_ici

Response:
{
  "user": {
    "id": 1,
    "email": "user@example.com",
    "current_roles": [
      {
        "role": {
          "name": "enseignant",
          "label": "Enseignant",
          "hierarchy_level": 4
        }
      }
    ]
  }
}
```

### 3. Vérification Permission
```
POST /api/auth/me/check-permission
{
  "permission_id": "grades.create"
}

Response:
{
  "has_permission": true,
  "permission_id": "grades.create"
}
```

### 4. Changement Mot de Passe
```
POST /api/auth/change-password
{
  "current_password": "old_password",
  "new_password": "NewP@ssw0rd",
  "new_password_confirmation": "NewP@ssw0rd"
}
```

### 5. Oubli de Mot de Passe
```
POST /api/auth/forgot-password
{ "email": "user@example.com" }

# Utilisateur reçoit email avec lien contenant token

POST /api/auth/reset-password
{
  "email": "user@example.com",
  "token": "sha256_token_du_email",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

---

## Seeders

### RolesSeeder
Crée les 9 rôles camerounais avec :
- Noms français
- Descriptions détaillées
- Hiérarchie correcte
- Catégories appropriées

### PermissionsSeeder
Crée 50+ permissions et les assigne aux rôles selon :
- Rôle admin : toutes les permissions
- Rôles hiérarchiques : selon niveau
- Rôles staff : selon domaine
- Rôles externes : lecture seule

---

## Sécurité

### Mot de Passe
- Min 10 caractères (résistance brute-force)
- Historique des 5 derniers (pas de réutilisation)
- Hash bcrypt (Laravel standard)
- Expiration (90 jours) en PasswordHistory

### Tokens
- Sanctum (Laravel official)
- Expiration 2 jours (configurable)
- Révocation au logout
- Base de données (pas JWT)

### Audit
- **LoginAttempt** : chaque tentative enregistrée
  - Email/username tenté
  - IP address
  - User agent
  - Succès/échec + raison
- **Verrouillage** : 5 tentatives échouées = 15 min lock
- **IP Blocking** : 10 tentatives = blocage IP

### Hiérarchie
- Admin ne peut pas être créé par simple utilisateur
- Chacun ne peut créer que des rôles en dessous
- Vérification stricte à chaque création d'utilisateur

---

## Tests Recommandés

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email_or_username":"admin@school.com","password":"password"}'

# Get Current User
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer TOKEN"

# Check Permission
curl -X POST http://localhost:8000/api/auth/me/check-permission \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"permission_id":"auth.create_user"}'

# Create User (Proviseur)
curl -X POST http://localhost:8000/api/auth/users \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name":"Jean",
    "last_name":"Dupont",
    "email":"jean@school.com",
    "password":"SecurePass123",
    "password_confirmation":"SecurePass123",
    "role_id":4
  }'

# Change Password
curl -X POST http://localhost:8000/api/auth/change-password \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password":"old_pass",
    "new_password":"NewP@ss123",
    "new_password_confirmation":"NewP@ss123"
  }'
```

---

## Configuration

### .env
```
SANCTUM_STATEFUL_DOMAINS=localhost
SESSION_DOMAIN=localhost
```

### app/Http/Kernel.php
```php
protected $routeMiddleware = [
    // ...
    'check.permission' => \Modules\Auth\Middleware\CheckPermission::class,
    'check.role' => \Modules\Auth\Middleware\CheckRole::class,
];
```

---

## Prochaines Étapes

1. **Authentification via Email** - Envoyer le token par email pour password reset
2. **2FA (optionnel)** - Support TOTP si demandé
3. **Audit Logs** - Intégrer avec module Audit
4. **Rate Limiting** - Limiter tentatives login par IP
5. **SSO (futur)** - LDAP/AD pour intégration réseau scolaire

---

## Fichiers Créés

### Services (4 fichiers)
- `modules/Auth/Services/AuthService.php`
- `modules/Auth/Services/AccountLockingService.php`
- `modules/Auth/Services/PasswordResetService.php`
- `modules/Auth/Services/UserManagementService.php`

### Controllers (4 fichiers)
- `modules/Auth/Controllers/AuthController.php`
- `modules/Auth/Controllers/UserController.php`
- `modules/Auth/Controllers/RoleController.php`
- `modules/Auth/Controllers/PermissionController.php`

### Requests (5 fichiers)
- `modules/Auth/Requests/LoginRequest.php`
- `modules/Auth/Requests/CreateUserRequest.php`
- `modules/Auth/Requests/ForgotPasswordRequest.php`
- `modules/Auth/Requests/ResetPasswordRequest.php`
- `modules/Auth/Requests/ChangePasswordRequest.php`

### Middleware (3 fichiers)
- `modules/Auth/Middleware/Authenticate.php`
- `modules/Auth/Middleware/CheckPermission.php`
- `modules/Auth/Middleware/CheckRole.php`

### Seeders (2 fichiers)
- `modules/Auth/Seeders/RolesSeeder.php`
- `modules/Auth/Seeders/PermissionsSeeder.php`
- `modules/Auth/Seeders/DatabaseSeeder.php`

### Configuration
- `modules/Auth/Routes/api.php`
- `modules/Auth/module.json`
- `modules/Auth/ModuleServiceProvider.php`

---

## Commandes pour Démarrage

```bash
# Installation
composer install

# Migrations
php artisan migrate

# Seeders
php artisan db:seed --class=Modules\\Auth\\Seeders\\DatabaseSeeder

# Test du serveur
php artisan serve

# Les rôles et permissions sont maintenant chargés
```
