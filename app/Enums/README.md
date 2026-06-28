# Enums du Projet MyScholar

## RoleEnum - Rôles des Lycées Camerounais

L'énumération `RoleEnum` définit les 9 rôles du système sans dépendre de la table `roles` ou du modèle `Role`.

### Utilisation

```php
use App\Enums\RoleEnum;

// Accéder aux valeurs des rôles
$adminRole = RoleEnum::SUPER_ADMINISTRATOR->value; // 'super_administrator'
$teacherRole = RoleEnum::ENSEIGNANT->value;         // 'enseignant'

// Obtenir le label en français
echo RoleEnum::SUPER_ADMINISTRATOR->label();        // 'Administrateur Système'
echo RoleEnum::PROVISEUR->label();                   // 'Proviseur (Directeur Général)'

// Obtenir la description
echo RoleEnum::ENSEIGNANT->description();           // 'Formateur. Enseigne ses matières...'

// Obtenir le niveau de hiérarchie
$level = RoleEnum::PROVISEUR->hierarchyLevel();     // 1
$level = RoleEnum::STUDENT->hierarchyLevel();       // 100

// Obtenir la catégorie
$category = RoleEnum::SUPER_ADMINISTRATOR->category(); // 'super_administrator'
$category = RoleEnum::ENSEIGNANT->category();         // 'staff'

// Obtenir tous les rôles
$allRoles = RoleEnum::allValues(); // ['super_administrator', 'proviseur', ...]

// Vérifier les rôles
if ($user->hasRole(RoleEnum::SUPER_ADMINISTRATOR->value)) {
    // L'utilisateur est administrateur système
}

// Assigner un rôle
$user->assignRole(RoleEnum::ENSEIGNANT->value);
$user->giveRole(RoleEnum::SUPER_ADMINISTRATOR->value);
```

### Hiérarchie des Rôles

| Niveau | Rôle | Catégorie | Description |
|--------|------|-----------|-------------|
| 0 | SUPER_ADMINISTRATOR | super_administrator | Administrateur Système |
| 1 | PROVISEUR | hierarchy | Chef exécutif du lycée |
| 2 | CENSEUR | hierarchy | Chef pédagogique |
| 3 | PROF_PRINCIPAL | hierarchy | Responsable administratif classe |
| 3 | CHEF_CLASSE | staff | Leader étudiant |
| 4 | ENSEIGNANT | staff | Professeur |
| 5 | SURVEILLANT | staff | Agent de discipline |
| 99 | PARENT | external | Parent ou tuteur |
| 100 | STUDENT | external | Élève/Apprenant |

### Avantages de l'Enum

✅ **Type-safe** - Les erreurs de typage sont détectées à la compilation
✅ **Autocomplete** - L'IDE fournit l'autocomplétion
✅ **Refactoring** - Renommer un rôle change automatiquement partout
✅ **Documentation** - Les labels et descriptions sont intégrés
✅ **Indépendance DB** - Pas de requête DB pour connaître les rôles disponibles
✅ **Performance** - Pas de cache nécessaire pour les noms de rôles

### Intégration avec Policies

```php
namespace Modules\Auth\Policies;

use App\Enums\RoleEnum;
use Modules\Auth\Models\User;

class UserPolicy
{
    public function view(User $user, User $model): bool
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMINISTRATOR->value)) {
            return true;
        }
        
        if ($user->hasRole(RoleEnum::PROVISEUR->value)) {
            return $this->isInSameSchool($user, $model);
        }
        
        return false;
    }
}
```

### Intégration avec Services

```php
use App\Enums\RoleEnum;

class BillingService
{
    public function canManageInvoices(User $user): bool
    {
        return $user->hasAnyRole([
            RoleEnum::SUPER_ADMINISTRATOR->value,
            RoleEnum::PROVISEUR->value,
            RoleEnum::CENSEUR->value,
        ]);
    }
}
```

### Notes Importantes

1. L'enum fournit les **noms de rôles** (strings), mais les **vérifications de permissions** se font toujours via la table `permissions`.
2. La table `roles` est toujours utilisée pour les relations et les permissions liées aux rôles.
3. Cet enum est utile pour:
   - Les constantes de rôles dans le code
   - Les dropdowns de sélection de rôles
   - Les listes de rôles autorisés dans les policies
   - Les validations sans requête DB
