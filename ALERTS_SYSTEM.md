# System d'Alertes MyScholar

Un système propre et réutilisable pour gérer les alertes (succès, avertissements, erreurs) en backend et frontend.

## 🎯 Caractéristiques

- ✅ Gestion des 3 types d'alertes: success, warning, error
- ✅ Support des codes d'alerte pour identification précise
- ✅ IDs uniques pour suppression/modification
- ✅ Stockage en session
- ✅ Retour automatique dans les réponses API JSON
- ✅ Helpers globaux pour faciliter l'utilisation
- ✅ Trait réutilisable pour les contrôleurs
- ✅ Interface propre et fluide

## 📦 Utilisation de Base

### 1. Via les Helpers Globaux (Recommandé)

```php
// Ajouter une alerte succès
alert_success('Opération réussie');
alert_success('Utilisateur créé', 'USER_CREATED');

// Ajouter une alerte avertissement
alert_warning('Attention: Ceci est important');
alert_warning('Quota atteint', 'QUOTA_WARNING');

// Ajouter une alerte erreur
alert_error('Une erreur est survenue');
alert_error('Accès refusé', 'ACCESS_DENIED');

// Récupérer toutes les alertes
$alerts = get_alerts();

// Effacer toutes les alertes
clear_alerts();
```

### 2. Via le Service Injecté

```php
use App\Services\AlertService;

class UserController extends Controller
{
    public function store(Request $request, AlertService $alerts)
    {
        // Créer un utilisateur
        $user = User::create($request->validated());

        // Ajouter une alerte
        $alerts->success('Utilisateur créé avec succès', 'USER_CREATED');

        return response()->json(['user' => $user]);
    }
}
```

### 3. Via le Trait HasAlerts

```php
use App\Traits\HasAlerts;

class UserController extends Controller
{
    use HasAlerts;

    public function __construct()
    {
        $this->initializeAlerts();
    }

    public function store(Request $request)
    {
        $user = User::create($request->validated());

        $this->success('Utilisateur créé', 'USER_CREATED')
             ->warning('Vérifiez l\'email', 'EMAIL_VERIFY');

        return response()->json(['user' => $user]);
    }
}
```

## 🔌 Chaînage Fluide

```php
alerts()
    ->success('User created', 'USER_CREATED')
    ->success('Email sent', 'EMAIL_SENT')
    ->warning('Check settings', 'CONFIG_WARNING')
    ->error('Backup failed', 'BACKUP_FAILED');
```

## 🗑️ Gestion des Alertes

### Supprimer une Alerte Spécifique

```php
$alerts = alerts()->all();
$alertId = $alerts['success'][0]['id'];

alerts()->delete($alertId);
```

### Supprimer par Type

```php
alerts()->deleteSuccess($id);
alerts()->deleteWarning($id);
alerts()->deleteError($id);
```

### Effacer un Type Entier

```php
alerts()->clearType('warning');
```

### Effacer Tout

```php
alerts()->clear();
```

## 📊 Vérification et Récupération

```php
// Vérifier s'il y a des alertes
alerts()->hasAny();  // true/false

// Vérifier un type spécifique
alerts()->has('error');  // true/false

// Compter les alertes
alerts()->count();  // Nombre total
alerts()->countByType('success');  // Nombre par type

// Récupérer par type
alerts()->getSuccesses();   // Array
alerts()->getWarnings();    // Array
alerts()->getErrors();      // Array
alerts()->all();            // Array avec tous les types

// Regarder sans effacer
$alerts = alerts()->peek();

// Flash (retourner et effacer)
$alerts = alerts()->flash();  // Retourne tout et efface
```

## 📤 Réponses API

Les alertes sont automatiquement ajoutées aux réponses JSON de l'API :

### Request:
```http
POST /api/users
Content-Type: application/json

{"name": "John", "email": "john@example.com"}
```

### Response:
```json
{
  "user": {
    "id": 1,
    "name": "John",
    "email": "john@example.com"
  },
  "alerts": {
    "success": [
      {
        "message": "Utilisateur créé avec succès",
        "code": "USER_CREATED",
        "id": "507f1f77bcf86cd799439011"
      }
    ],
    "warning": [],
    "error": []
  }
}
```

## 🎨 Utilisation en Frontend

### Exemple avec Vue.js

```javascript
// Dans votre composant
export default {
  methods: {
    async createUser() {
      const response = await fetch('/api/users', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.formData)
      });

      const data = await response.json();

      // Traiter les alertes
      if (data.alerts) {
        // Succès
        data.alerts.success.forEach(alert => {
          this.$notify({
            type: 'success',
            message: alert.message,
            code: alert.code
          });
        });

        // Avertissements
        data.alerts.warning.forEach(alert => {
          this.$notify({
            type: 'warning',
            message: alert.message,
            code: alert.code
          });
        });

        // Erreurs
        data.alerts.error.forEach(alert => {
          this.$notify({
            type: 'error',
            message: alert.message,
            code: alert.code
          });
        });
      }
    }
  }
}
```

### Exemple avec React

```jsx
const handleCreateUser = async () => {
  const response = await fetch('/api/users', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
  });

  const data = await response.json();

  if (data.alerts) {
    data.alerts.success.forEach(alert => {
      toast.success(alert.message);
    });
    data.alerts.error.forEach(alert => {
      toast.error(alert.message);
    });
  }
};
```

## 🎯 Cas d'Usage Recommandés

### 1. Créations/Mises à jour
```php
public function store(Request $request)
{
    $user = User::create($request->validated());
    
    alert_success(__('users.created_successfully'), 'USER_CREATED');
    
    return response()->json(['user' => $user]);
}
```

### 2. Suppressions avec Confirmation
```php
public function destroy(User $user)
{
    $user->delete();
    
    alert_success('Utilisateur supprimé', 'USER_DELETED');
    
    return response()->json(['message' => 'Deleted']);
}
```

### 3. Validations Multiples
```php
public function validate(Request $request)
{
    if (!$request->has('email')) {
        alert_error('Email requis', 'EMAIL_REQUIRED');
    }
    
    if (!$request->has('password')) {
        alert_error('Mot de passe requis', 'PASSWORD_REQUIRED');
    }
    
    if (alerts()->has('error')) {
        return response()->json(['errors' => alerts()->getErrors()], 422);
    }
    
    return response()->json(['valid' => true]);
}
```

### 4. Actions Administrateur
```php
public function approvePasswordReset(int $userId)
{
    $user = User::find($userId);
    $tempPassword = $this->generatePassword();
    $user->update(['password' => bcrypt($tempPassword)]);
    
    alert_success('Mot de passe réinitialisé', 'PASSWORD_RESET_APPROVED');
    alert_warning(
        'Le mot de passe temporaire: ' . $tempPassword,
        'TEMPORARY_PASSWORD_GENERATED'
    );
    
    return response()->json(['user' => $user]);
}
```

## 🧪 Tests

Voir `tests/Unit/AlertServiceTest.php` pour les 19 tests couvrant tous les cas d'usage.

Exécuter les tests:
```bash
php vendor/bin/phpunit tests/Unit/AlertServiceTest.php
```

## 📚 API Complète

| Méthode | Description |
|---------|------------|
| `success($msg, $code)` | Ajouter une alerte succès |
| `warning($msg, $code)` | Ajouter une alerte avertissement |
| `error($msg, $code)` | Ajouter une alerte erreur |
| `all()` | Obtenir toutes les alertes |
| `getSuccesses()` | Obtenir les succès |
| `getWarnings()` | Obtenir les avertissements |
| `getErrors()` | Obtenir les erreurs |
| `has($type)` | Vérifier si type existe |
| `hasAny()` | Vérifier s'il y a des alertes |
| `count()` | Nombre total d'alertes |
| `countByType($type)` | Nombre par type |
| `delete($id)` | Supprimer par ID |
| `deleteSuccess($id)` | Supprimer un succès |
| `deleteWarning($id)` | Supprimer un avertissement |
| `deleteError($id)` | Supprimer une erreur |
| `clear()` | Effacer toutes les alertes |
| `clearType($type)` | Effacer un type |
| `flash()` | Retourner et effacer |
| `peek()` | Regarder sans effacer |
| `toJson()` | Convertir en JSON |
