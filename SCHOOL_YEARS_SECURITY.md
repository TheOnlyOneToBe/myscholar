# Sécurité et Permissions - Gestion des Années Scolaires

Guide complet des permissions, traductions et notifications pour le système de gestion des années scolaires.

## 🔐 Système de Permissions

### Permissions Définies

Le système utilise les permissions suivantes (définies dans `modules/Config/permissions.json`):

| Permission ID | Nom | Description | Rôles par défaut |
|---------------|-----|-------------|------------------|
| `config.school_year.view` | Voir les années scolaires | Consulter la liste des années | admin, directeur, censeur |
| `config.school_year.create` | Créer une année scolaire | Créer une nouvelle année | admin |
| `config.school_year.edit` | Modifier une année scolaire | Modifier les paramètres d'une année | admin, directeur |
| `config.school_year.delete` | Supprimer une année scolaire | Supprimer une année | admin |
| `config.school_year.switch` | Changer l'année scolaire | Basculer vers une autre année | admin, directeur |

### Rôles Recommandés

**Admin (Super Administrateur)**
- Toutes les permissions
- Peut créer, modifier, supprimer, activer les années
- Peut changer l'année en session

**Directeur**
- `config.school_year.view` - Consulter
- `config.school_year.edit` - Modifier et activer
- `config.school_year.switch` - Changer la session
- ❌ CANNOT DELETE

**Censeur**
- `config.school_year.view` - Consulter uniquement
- ❌ CANNOT CREATE, EDIT, DELETE, SWITCH

**Utilisateur Standard**
- ❌ NO PERMISSIONS

## 🔒 Intégration des Permissions

### Routes Web

```php
// Accès à la page de gestion des années
Route::get('/config/school-years', SchoolYearComponent::class)
    ->middleware('can:config.school_year.view');
```

### Routes API

```php
// Lecture - Requirerement config.school_year.view
Route::middleware('can:config.school_year.view')->group(function () {
    Route::get('/school-years', [SchoolYearController::class, 'index']);
    Route::get('/school-years/current', [SchoolYearController::class, 'current']);
    Route::get('/school-years/{schoolYear}', [SchoolYearController::class, 'show']);
});

// Création - Requirerement config.school_year.create
Route::middleware('can:config.school_year.create')->group(function () {
    Route::post('/school-years', [SchoolYearController::class, 'store']);
});

// Modification et Activation - Requirerement config.school_year.edit
Route::middleware('can:config.school_year.edit')->group(function () {
    Route::put('/school-years/{schoolYear}', [SchoolYearController::class, 'update']);
    Route::post('/school-years/{schoolYear}/activate', [SchoolYearController::class, 'activate']);
});

// Suppression - Requirerement config.school_year.delete
Route::middleware('can:config.school_year.delete')->group(function () {
    Route::delete('/school-years/{schoolYear}', [SchoolYearController::class, 'destroy']);
});
```

### Vérifications dans le Composant Livewire

```php
// Vérification en début de méthode
public function createYear(): void
{
    if (!auth()->user()->can('config.school_year.create')) {
        $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
        return;
    }
    // ...
}

// Affichage conditionnel dans la vue
@if($canCreate)
    <button wire:click="toggleForm">{{ __('config.labels.create_year') }}</button>
@endif
```

## 🌐 Système de Traductions

### Structure des Traductions

Les traductions sont stockées en JSON dans `modules/Config/translations/{lang}/config.json`:

```
modules/Config/translations/
├── fr/
│   └── config.json
└── en/
    └── config.json
```

### Clés de Traduction

#### Labels (Étiquettes)
```json
{
  "labels": {
    "school_years": "Années Scolaires",
    "school_year_name": "Nom de l'année",
    "start_year": "Année de début",
    "create_year": "Créer une nouvelle année",
    "edit_year": "Modifier l'année",
    "activate_year": "Activer",
    "switch_session": "Session",
    "delete_year": "Supprimer l'année"
  }
}
```

#### Messages (Messages d'information)
```json
{
  "messages": {
    "school_year_created": "Année scolaire créée avec succès.",
    "school_year_updated": "Année scolaire modifiée avec succès.",
    "school_year_deleted": "Année scolaire supprimée avec succès.",
    "active": "En cours",
    "archived": "Archivée",
    "available": "Disponible"
  }
}
```

#### Erreurs
```json
{
  "errors": {
    "no_school_year_available": "Aucune année scolaire disponible.",
    "cannot_delete_active": "Impossible de supprimer l'année scolaire active.",
    "permission_denied_past_year": "Vous n'avez pas la permission..."
  }
}
```

#### Alertes
```json
{
  "alerts": {
    "created": "Année scolaire créée avec succès",
    "updated": "Année scolaire modifiée avec succès",
    "error_creating": "Erreur lors de la création"
  }
}
```

### Utilisation dans le Code

#### Blade Templates
```blade
<!-- Traduction simple -->
<h1>{{ __('config.labels.school_years') }}</h1>

<!-- Traduction avec paramètres -->
<p>{{ __('config.messages.activated', ['name' => $year->name]) }}</p>

<!-- Traduction conditionnelle -->
@if($year->is_active)
    <span>{{ __('config.messages.active') }}</span>
@endif
```

#### PHP (Contrôleurs, Composants)
```php
// Message simple
$this->success(__('config.messages.school_year_created'), 'SCHOOL_YEAR_CREATED');

// Avec paramètres
$this->success(
    __('config.alerts.activated', ['name' => $year->name]), 
    'SCHOOL_YEAR_ACTIVATED'
);

// En français par défaut
trans('config.labels.school_year_name');
```

## 📬 Système d'Alertes et Notifications

### Alertes (Toast Notifications)

Les alertes sont affichées comme des toasts Bootstrap en haut de la page avec auto-dismiss après 5 secondes.

#### Types d'Alertes

1. **Succès** (vert)
```php
$this->success(
    __('config.messages.school_year_created'), 
    'SCHOOL_YEAR_CREATED'
);
```

2. **Erreur** (rouge)
```php
$this->error(
    __('config.alerts.error_creating'), 
    'CREATE_ERROR'
);
```

3. **Avertissement** (jaune)
```php
$this->warning(
    __('config.messages.warning_message'), 
    'WARNING_CODE'
);
```

#### Codes d'Alerte

| Code | Opération | Type |
|------|-----------|------|
| `SCHOOL_YEAR_CREATED` | Création réussie | Success |
| `SCHOOL_YEAR_UPDATED` | Modification réussie | Success |
| `SCHOOL_YEAR_DELETED` | Suppression réussie | Success |
| `SCHOOL_YEAR_ACTIVATED` | Activation réussie | Success |
| `SESSION_SWITCHED` | Changement de session | Success |
| `CANNOT_DELETE_ACTIVE` | Tentative de suppression d'année active | Error |
| `CREATE_ERROR` | Erreur lors de la création | Error |
| `UPDATE_ERROR` | Erreur lors de la modification | Error |
| `DELETE_ERROR` | Erreur lors de la suppression | Error |
| `PERMISSION_DENIED` | Accès refusé | Error |

### Notifications (Optionnel)

Pour ajouter des notifications persistantes (base de données):

```php
// Envoyer une notification aux administrateurs
Notification::send(
    User::where('role', 'admin')->get(),
    new SchoolYearChangedNotification($year)
);
```

## 🔍 Vérifications de Sécurité

### 1. Authentification
- Toutes les routes protégées par le middleware `auth`
- Aucun accès non authentifié

### 2. Autorisation
- Permissions vérifiées sur chaque opération
- Messages d'erreur sans révéler les permissions manquantes
- Logs des tentatives d'accès non autorisé

### 3. Validation des Données
```php
#[Validate('required|string|max:255|unique:school_years,name')]
public string $name = '';

#[Validate('required|date|after:start_date')]
public string $end_date = '';
```

### 4. Constraints Métier
- Une seule année peut être active à la fois
- Impossible de supprimer l'année active
- Années archivées non modifiables

### 5. Logging
```php
try {
    // Opération
} catch (\Exception $e) {
    \Log::error('Error creating school year', ['error' => $e->getMessage()]);
}
```

## 📋 Checklist de Déploiement

### Avant la Production

- [ ] Permissions synchronisées avec `php artisan permissions:sync`
- [ ] Traductions vérifiées pour FR et EN
- [ ] Tests de permissions avec différents rôles
- [ ] Alertes fonctionnelles et visibles
- [ ] Logs d'erreur configurés
- [ ] Rate limiting sur les routes API
- [ ] Backup des années scolaires actives
- [ ] Documentation utilisateur mise à jour

### Configuration Recommandée

```env
# .env
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

## 🔧 Maintenance

### Synchroniser les Permissions

```bash
# Après avoir ajouté de nouvelles permissions
php artisan permissions:sync
```

### Vérifier les Traductions Manquantes

```php
// Ajouter à tinker pour vérifier une clé
__('config.labels.new_key') // Retourne la clé si non trouvée
```

### Auditer les Accès

```php
// Logs des tentatives non autorisées
\Log::warning('Unauthorized access attempt', [
    'user_id' => auth()->id(),
    'permission' => 'config.school_year.delete',
    'ip' => request()->ip()
]);
```

## ⚠️ Bonnes Pratiques

1. **Toujours vérifier les permissions** avant de modifier des données critiques
2. **Utiliser des traductions** pour tous les messages utilisateur
3. **Logger les opérations sensibles** pour l'audit
4. **Tester avec différents rôles** avant de déployer
5. **Documenter les nouvelles permissions** dans ce fichier
6. **Valider les entrées utilisateur** côté client et serveur

## 📞 Support et Troubleshooting

### Permission Denied mais utilisateur devrait avoir accès?
- Vérifier que l'utilisateur a le rôle correct
- Vérifier que le rôle a la permission assignée
- Synchroniser les permissions: `php artisan permissions:sync`
- Vérifier les logs: `storage/logs/laravel.log`

### Traduction introuvable?
- Vérifier que la clé existe dans `modules/Config/translations/{lang}/config.json`
- Vérifier le format de la clé (points pour les niveaux imbriqués)
- Utiliser le fallback locale (EN) si langue courante ne la trouve pas

### Alerte ne s'affiche pas?
- Vérifier que AlertToast Livewire component est enregistré
- Vérifier que la page inclut le layout app
- Vérifier les logs pour les erreurs Livewire

---

✅ **Système de sécurité complet et intégré!**
