# Guide Complet d'Installation MyScholar

## Aperçu

Ce guide couvre le processus complet de déploiement de MyScholar pour un nouveau client (lycée). Le processus est automatisé via la commande `php artisan client:initialize`.

## Prérequis

Avant de lancer l'initialisation du client, assurez-vous que :

1. **Base de données préparée**
   ```bash
   php artisan migrate
   ```

2. **Environnement Laravel configuré**
   - Fichier `.env` créé et configuré
   - Connexion à la base de données fonctionnelle
   - APP_KEY généré

3. **Système prêt**
   - PHP 8.1+
   - SQLite ou MySQL
   - Espace disque suffisant pour les fichiers/uploads

## Démarrage Rapide (5 minutes)

Le moyen le plus rapide de configurer un nouveau client :

```bash
php artisan client:initialize
```

Cette commande interactive va :
1. Vous demander les informations du lycée (nom, adresse, contact)
2. Créer 6 rôles (admin, directeur, enseignant, surveillant, parent, élève)
3. Créer 27 permissions (seulement pour les modules installés)
4. Assigner les permissions aux rôles
5. Configurer l'utilisateur administrateur
6. Initialiser les paramètres système
7. Vérifier les années scolaires

## Étapes Détaillées

### Étape 1 : Exécuter la Migration Initiale

```bash
php artisan migrate
```

Cela crée toutes les tables de la base de données pour les modules installés.

**Résultat attendu :**
```
Migrating: ...
...toutes les migrations effectuées
```

### Étape 2 : Exécuter l'Initialisation du Client

```bash
php artisan client:initialize
```

#### Invites Interactives

La commande vous demandera de fournir :

**Informations du Lycée :**
```
Nom de l'École : [Par défaut: My School]
  → Saisissez le nom officiel de votre lycée

Sigle du Lycée (ex: MS) : [Par défaut: MS]
  → Abréviation courte (ex: "LBY" pour Lycée Bawock)

Devise du Lycée (optionnel) : []
  → Devise du lycée si applicable

Type d'Établissement :
  [0] public
  [1] privé        ← Sélectionnez pour les écoles privées
  [2] confessionnel ← Sélectionnez pour les écoles religieuses

Adresse Postale : [Par défaut: 123 Main Street]
  → Adresse complète

Ville : [Par défaut: Douala]
  → Ville du lycée

Région/Province : [Par défaut: Littoral]
  → Région administrative

Téléphone Contact : [Par défaut: +237612345678]
  → Numéro de téléphone du lycée (avec code pays)

Email Contact : [Par défaut: contact@myschool.edu]
  → Email officiel du lycée

Site Web (optionnel) : []
  → URL du site web du lycée

Boîte Postale (optionnel) : []
  → Boîte postale si applicable

Numéro d'Agrément (optionnel) : []
  → Numéro d'agrément gouvernemental

Arrêté de Création (optionnel) : []
  → Numéro de l'arrêté officiel

Nom du Fondateur (optionnel) : []
  → Nom du fondateur du lycée

Nom du Directeur (optionnel) : []
  → Nom du directeur actuel

Année de Fondation (optionnel) : []
  → Année de création du lycée
```

### Étape 3 : Vérifier la Configuration

Après l'initialisation, vérifiez que tout a été configuré correctement :

```bash
php artisan tinker
```

Vérifiez ensuite :

```php
# Vérifier les informations du lycée
$school = SchoolInfo::first();
echo $school->name; // Doit afficher le nom du lycée

# Vérifier les rôles créés
Role::all()->pluck('name'); // Doit afficher 6 rôles

# Vérifier les permissions créées
Permission::count(); // Doit afficher les permissions pour les modules installés

# Vérifier l'utilisateur admin
$admin = User::first();
echo $admin->roles->first()->name; // Doit afficher 'admin'
```

## Options de Commande

### Sélection des Modules

**Installer tous les modules (recommandé) :**
```bash
php artisan client:initialize --all
```

**Installer des modules spécifiques :**
```bash
php artisan client:initialize --modules=Config,Auth,Students,Classes,Grades
```

**Sélection interactive des modules :**
```bash
php artisan client:initialize
# Puis sélectionnez les modules optionnels à installer
```

**Modules disponibles :**
- Config (cœur, obligatoire)
- Auth (cœur, obligatoire)
- Audit (cœur, optionnel)
- Notifications (cœur, optionnel)
- Reporting (cœur, optionnel)
- Students (métier, optionnel)
- Classes (métier, optionnel)
- Grades (métier, optionnel)
- Attendance (métier, optionnel)
- Billing (métier, optionnel)

### Résolution Automatique des Dépendances

Quand vous sélectionnez des modules, les dépendances sont incluses automatiquement :

```
Config, Auth toujours requis
├─ Students dépend de : Config, Auth
├─ Classes dépend de : Config, Auth
├─ Grades dépend de : Config, Auth, Students, Classes
├─ Attendance dépend de : Config, Auth, Students, Classes
└─ Billing dépend de : Config, Auth, Students
```

**Exemple :** Si vous sélectionnez seulement "Grades", le système inclut automatiquement :
- Config, Auth, Students, Classes

### Sauter la Configuration du Lycée

Si vous voulez configurer les infos du lycée plus tard :

```bash
php artisan client:initialize --all --skip-school
```

### Sauter les Rôles et Permissions

Si vous avez déjà les rôles/permissions configurés :

```bash
php artisan client:initialize --all --skip-roles
```

### Combiner les Options

```bash
php artisan client:initialize --modules=Config,Auth,Students --skip-school --skip-roles
```

## Rôles Créés

Les 6 rôles suivants sont créés avec les permissions prédéfinies :

### 1. Admin (Administrateur)
- **Permissions** : Toutes les 27 permissions
- **Cas d'usage** : Accès complet au système
- **Assignation** : Au moins un administrateur système

### 2. Directeur (Directeur/Principal)
- **Permissions** :
  - config.view, config.edit, config.manage_years
  - students.view, students.create, students.edit
  - classes.view, classes.create, classes.edit
  - grades.view
  - attendance.view
  - scholarity.view, scholarity.manage
  - users.view, users.create, users.edit
  - audit.view
- **Cas d'usage** : Direction/administration du lycée
- **Assignation typique** : 1-2 administrateurs

### 3. Enseignant (Professeur)
- **Permissions** :
  - students.view
  - classes.view
  - grades.view, grades.create, grades.edit
  - attendance.view, attendance.record, attendance.edit
- **Cas d'usage** : Professeurs en classe
- **Assignation typique** : Tous les enseignants

### 4. Surveillant (Moniteur/Surveillant)
- **Permissions** :
  - students.view
  - classes.view
  - attendance.view, attendance.record
- **Cas d'usage** : Surveillance et discipline
- **Assignation typique** : Membres du comité de discipline

### 5. Parent (Parent/Tuteur)
- **Permissions** :
  - students.view
  - grades.view
  - attendance.view
- **Cas d'usage** : Voir la progression de l'enfant
- **Assignation typique** : Comptes parents

### 6. Élève
- **Permissions** :
  - grades.view
  - attendance.view
- **Cas d'usage** : Voir ses propres notes et présences
- **Assignation typique** : Tous les élèves inscrits

## Permissions Créées

Le nombre de permissions dépend des modules installés :

**Seuls les modules sélectionnés reçoivent des permissions :**
- 10 modules au total disponibles
- 27 permissions totales possibles
- Seules les permissions pour les modules installés sont créées

**Exemples :**

**Config + Auth seulement :**
- 6 permissions (3 config + 3 gestion utilisateurs)

**Config + Auth + Students + Classes :**
- 11 permissions (3 config + 4 students + 4 classes)

**Tous les 10 modules :**
- 27 permissions (tous les modules activés)

**Pour vérifier les permissions créées :**
```bash
php artisan tinker
# Permission::all();
# Ou par module :
# Permission::where('module', 'students')->get();
```

## Toutes les Permissions Disponibles (27 au Total)

### Module Config (3)
- `config.view` - Voir la configuration système
- `config.edit` - Modifier les paramètres
- `config.manage_years` - Gérer les années scolaires

### Module Students (4)
- `students.view` - Voir les dossiers élèves
- `students.create` - Créer de nouveaux élèves
- `students.edit` - Modifier les informations d'élève
- `students.delete` - Supprimer les dossiers élèves

### Module Classes (4)
- `classes.view` - Voir les classes
- `classes.create` - Créer des classes
- `classes.edit` - Modifier les classes
- `classes.delete` - Supprimer les classes

### Module Grades (4)
- `grades.view` - Voir les notes
- `grades.create` - Enregistrer des notes
- `grades.edit` - Modifier les notes
- `grades.delete` - Supprimer les notes

### Module Attendance (3)
- `attendance.view` - Voir les présences
- `attendance.record` - Enregistrer les présences
- `attendance.edit` - Modifier les présences

### Module Billing/Scholarity (3)
- `scholarity.view` - Voir les informations de facturation
- `scholarity.manage` - Gérer la facturation et les paiements
- `scholarity.modify_past_years` - Modifier les données d'années passées (permission spéciale)

### Gestion des Utilisateurs (5)
- `users.view` - Voir les comptes utilisateurs
- `users.create` - Créer de nouveaux utilisateurs
- `users.edit` - Modifier les informations d'utilisateur
- `users.delete` - Supprimer les comptes utilisateurs
- `users.manage_roles` - Assigner les rôles aux utilisateurs

### Module Audit (1)
- `audit.view` - Voir les journaux d'audit

## Permission Spéciale : scholarity.modify_past_years

Cette permission est critique pour l'intégrité des données :

- **Par défaut** : Seulement l'admin l'a
- **Objectif** : Empêcher la modification accidentelle des données d'années passées
- **Effet** : Les enseignants ne peuvent pas modifier les notes des années précédentes sans cette permission
- **Cas d'usage** : Corrections exceptionnelles de données historiques

Pour accorder cette permission à un utilisateur :

```bash
php artisan tinker
```

```php
$director = User::where('email', 'director@school.edu')->first();
$permission = Permission::where('permission_id', 'scholarity.modify_past_years')->first();
$director->givePermission($permission);
```

## Fichier de Configuration des Modules

Après l'initialisation, `config/modules.json` est mis à jour avec :

```json
{
    "clientId": "Nom de l'École",
    "installedModules": [
        "Config",
        "Auth",
        "Students",
        "Classes",
        "Grades"
    ],
    "installedAt": "2026-06-27T15:03:10+00:00"
}
```

**Ce que cela signifie :**
- Seulement ces 5 modules sont installés
- Les migrations des autres modules ne s'exécutent pas
- Les migrations de pont s'exécutent seulement pour les modules sélectionnés
- Les permissions sont créées seulement pour les modules sélectionnés
- Les routes API sont chargées seulement pour les modules sélectionnés

**Pour voir les modules installés :**
```bash
cat config/modules.json
```

**Pour réinstaller avec des modules différents :**
Relancez simplement la commande avec un flag `--modules` différent.

## Paramètres Système Initialisés

Les paramètres système par défaut suivants sont créés :

```php
[
    'timezone' => 'Africa/Douala',
    'currency' => 'FCFA',
    'date_format' => 'd/m/Y',
    'language' => 'fr',
    'max_students_per_class' => 45,
]
```

Pour les modifier plus tard :

```bash
php artisan tinker
```

```php
SystemSetting::where('key', 'timezone')->update(['value' => 'Africa/Lagos']);
SystemSetting::where('key', 'max_students_per_class')->update(['value' => '50']);
```

## Années Scolaires

Quatre années scolaires par défaut sont créées :

- **2022-2023** : Verrouillée (données historiques)
- **2023-2024** : Verrouillée (année précédente)
- **2024-2025** : ACTIVE (année actuelle)
- **2025-2026** : Année future

L'année 2024-2025 est automatiquement définie comme active. Pour changer l'année active :

```bash
php artisan tinker
```

```php
SchoolYear::where('name', '2024-2025')->update(['is_active' => false]);
SchoolYear::where('name', '2025-2026')->update(['is_active' => true]);
```

## Création de Nouveaux Utilisateurs

Après l'initialisation, créez les utilisateurs du personnel et des élèves :

### Créer un Compte Enseignant

```bash
php artisan tinker
```

```php
$teacher = User::create([
    'username' => 'jdoe',
    'email' => 'john.doe@school.edu',
    'password' => bcrypt('SecurePassword123!'),
    'full_name' => 'John Doe',
    'is_active' => true,
]);

// Assigner le rôle enseignant
$teacher->assignRole('enseignant');

// Ou assigner plusieurs rôles
$teacher->assignRole(['enseignant', 'surveillant']);
```

### Créer un Compte Parent

```php
$parent = User::create([
    'username' => 'parent_123',
    'email' => 'parent@example.com',
    'password' => bcrypt('SecurePassword123!'),
    'full_name' => 'Parent Name',
    'is_active' => true,
]);

$parent->assignRole('parent');
```

## Tâches Post-Configuration

Après l'exécution de `client:initialize`, effectuez ces tâches :

### 1. Vérifier l'Accès Admin

```bash
php artisan serve
```

- Naviguer vers `/api/config/school`
- Se connecter avec les identifiants admin
- Vérifier que vous pouvez voir et modifier les informations de l'école

### 2. Configurer le Logo de l'École

Télécharger le logo via :
- Point de terminaison API : `POST /api/config/school/logo`
- Ou manuellement via la base de données

### 3. Créer les Premiers Élèves

Créer des dossiers élèves initials pour tester :

```bash
php artisan tinker
```

```php
$student = Student::create([
    'student_id_number' => 'EST-2024-0001',
    'first_name' => 'Test',
    'last_name' => 'Student',
    'date_of_birth' => '2006-01-15',
    'sex' => 'M',
    'email' => 'test.student@school.edu',
    'phone_number' => '+237612345678',
]);

// Créer l'inscription pour l'année scolaire actuelle
$enrollment = StudentEnrollment::create([
    'student_id' => $student->id,
    'school_year_id' => SchoolYear::where('is_active', true)->first()->id,
    'enrollment_status' => EnrollmentStatus::ACTIVE,
]);
```

### 4. Assigner les Enseignants aux Classes

Une fois les classes créées, assignez les enseignants :

```php
$teacher = User::where('email', 'john.doe@school.edu')->first();
$class = Classes::where('name', '1ère Année A')->first();

// Ajouter l'enseignant à la classe (dépend de la structure du modèle)
// L'implémentation spécifique varie selon votre bridge Classes
```

### 5. Tester le Changement d'Année Scolaire

Vérifier que le filtrage basé sur la session fonctionne :

```bash
php artisan tinker
```

```php
// Définir l'année de session actuelle
app(\Modules\Config\Services\SchoolYearSessionService::class)
    ->setActiveYear(SchoolYear::where('name', '2023-2024')->first());

// Vérifier que les données se filtrent par année de session
Student::sessionYear()->count(); // Doit retourner les élèves de 2023-2024
```

## Dépannage

### Problème : "Les rôles existent déjà"

Si vous obtenez une erreur que les rôles existent, exécutez avec `--skip-roles` :

```bash
php artisan client:initialize --skip-roles
```

### Problème : "Les infos de l'école existent déjà"

Si vous avez besoin de mettre à jour les infos de l'école, exécutez avec `--skip-school` :

```bash
php artisan client:initialize --skip-school
```

Puis mettez à jour manuellement en base de données :

```bash
php artisan tinker
```

```php
$school = SchoolInfo::first();
$school->update(['name' => 'Nouveau Nom de l\'École']);
```

### Problème : Les permissions ne sont pas assignées aux rôles

Vérifiez que les relations rôle-permission sont correctes :

```php
$admin = Role::where('name', 'admin')->first();
echo $admin->permissions->count(); // Doit afficher le nombre de permissions
```

Si manquantes, relancez :

```bash
php artisan client:initialize --skip-school --skip-roles
```

Attendez le message "Permissions assignées aux rôles".

### Problème : L'utilisateur admin n'a pas de rôle

Assignez manuellement :

```php
$admin = User::first();
$adminRole = Role::where('name', 'admin')->first();
$admin->roles()->attach($adminRole);
```

## Prochaines Étapes

Après une initialisation réussie :

1. **Documentez vos identifiants** : Sauvegardez le nom d'utilisateur/mot de passe admin de manière sécurisée
2. **Créez les comptes du personnel** : Ajoutez enseignants, surveillants, directeur
3. **Créez la structure des classes** : Définissez les classes pour l'année actuelle
4. **Importez les données d'élèves** : Import en masse ou création manuelle
5. **Assignez les enseignants aux classes** : Liez les enseignants à leurs classes
6. **Testez les workflows** : Entrée de notes, présences, facturation
7. **Formez le personnel** : Formation utilisateur sur le système

## Support

Pour toute question ou problème :

1. Consultez les logs : `storage/logs/laravel.log`
2. Vérifiez la base de données : `php artisan db:seed`
3. Synchronisez les permissions : `php artisan permissions:sync`
4. Vérifiez les migrations : `php artisan migrate:status`

## Documentation Connexe

- [STRUCTURE_MODULES.md](./STRUCTURE_MODULES.md) - Architecture des modules
- [Ponts et Dépendances](../bridges/BRIDGES.md) - Structure des ponts
- [GUIDE_ANNEES_SCOLAIRES.md](./GUIDE_ANNEES_SCOLAIRES.md) - Gestion des années scolaires
- [OPTIMIZATION_DB.md](./OPTIMIZATION_DB.md) - Optimisation de la base de données
