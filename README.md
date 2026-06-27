# 🎓 MyScholar - Système de Gestion Scolaire Modulaire

MyScholar est un logiciel de gestion scolaire moderne et modulaire conçu spécifiquement pour les lycées camerounais. Le système est **multi-client** avec une **architecture modulaire** permettant à chaque école d'installer uniquement les modules dont elle a besoin.

## 🌟 Caractéristiques Principales

### Architecture Modulaire
- **10 modules indépendants** avec système de dépendances automatiques
- **Installation flexible** : installez tous les modules ou seulement ceux dont vous avez besoin
- **Isolation complète** : chaque module peut fonctionner indépendamment avec ses dépendances
- **Système de ponts** : les modules communiquent via des migrations de pont bien documentées

### Modules Disponibles

| Module | Type | Fonction | Dépend de |
|--------|------|----------|-----------|
| **Config** | Cœur | Configuration et branding du lycée | Aucun |
| **Auth** | Cœur | Authentification et contrôle d'accès | Aucun |
| **Audit** | Cœur | Journalisation des activités | Aucun |
| **Notifications** | Cœur | Emails et SMS | Aucun |
| **Reporting** | Cœur | Rapports et analytics | Aucun |
| **Students** | Métier | Gestion des élèves | Config, Auth |
| **Classes** | Métier | Gestion des classes | Config, Auth |
| **Grades** | Métier | Gestion des notes | Config, Auth, Students, Classes |
| **Attendance** | Métier | Suivi des présences | Config, Auth, Students, Classes |
| **Billing** | Métier | Gestion financière | Config, Auth, Students |

## 🚀 Démarrage Rapide

### Prérequis

- PHP 8.1+
- MySQL ou SQLite
- Composer
- Node.js et npm (pour le frontend)

### Installation Initiale

```bash
# 1. Cloner le projet
git clone <repo-url>
cd myscholar

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Initialiser la base de données
php artisan migrate
```

### Configuration du Client (École)

```bash
# Lancer l'initialisation interactive
php artisan client:initialize

# OU installer tous les modules directement
php artisan client:initialize --all

# OU installer des modules spécifiques
php artisan client:initialize --modules=Students,Classes,Grades
```

La commande va :
1. ✅ Vous demander les informations de votre lycée (nom, logo, adresse, etc.)
2. ✅ Créer 6 rôles prédéfinis (admin, directeur, enseignant, surveillant, parent, élève)
3. ✅ Créer les permissions appropriées pour les modules installés
4. ✅ Configurer l'utilisateur administrateur
5. ✅ Initialiser les années scolaires
6. ✅ Configurer les paramètres système

## 📦 Sélection des Modules

### Installer Tous les Modules (Recommandé)

```bash
php artisan client:initialize --all
```

**Résultat** : 10 modules installés, 27 permissions créées

### Installer des Modules Spécifiques

```bash
php artisan client:initialize --modules=Students,Classes,Grades
```

**Résultat** : 
- Modules demandés : Students, Classes, Grades
- ⚠️ **Avertissement de dépendance** : Config et Auth ajoutés automatiquement
- Total : 5 modules installés

### Mode Interactif

```bash
php artisan client:initialize
```

Le système vous montrera :
- Les modules obligatoires (Config, Auth)
- Les modules optionnels avec leurs dépendances
- Un menu de sélection pour choisir

## ⚙️ Configuration Système

### Paramètres par Défaut

```php
timezone: Africa/Douala
currency: FCFA
date_format: d/m/Y
language: fr
max_students_per_class: 45
```

Pour modifier :

```bash
php artisan tinker

# Changer le fuseau horaire
SystemSetting::where('key', 'timezone')->update(['value' => 'Africa/Lagos']);

# Changer le nombre max d'élèves par classe
SystemSetting::where('key', 'max_students_per_class')->update(['value' => '50']);
```

## 👥 Rôles et Permissions

### 6 Rôles Prédéfinis

#### 1. **Admin** (Administrateur)
- Accès complet au système
- Toutes les 27 permissions
- Gestion des utilisateurs et rôles

#### 2. **Directeur** (Directeur/Principal)
- Configuration de l'école
- Gestion des élèves et classes
- Vue d'ensemble des notes et présences
- Gestion des utilisateurs

#### 3. **Enseignant** (Professeur)
- Enregistrement des notes
- Prise des présences
- Consultation des élèves de leurs classes

#### 4. **Surveillant** (Moniteur)
- Prise des présences
- Consultation des élèves

#### 5. **Parent** (Parent/Tuteur)
- Consultation des notes de son enfant
- Consultation des présences
- Consultation des informations d'élève

#### 6. **Élève** (Student)
- Consultation de ses propres notes
- Consultation de ses présences

## 📅 Gestion des Années Scolaires

MyScholar utilise un système **basé sur la session** pour gérer les années scolaires :

```bash
php artisan tinker

# Voir l'année scolaire active
$year = app(\Modules\Config\Services\SchoolYearSessionService::class)->getActiveYear();
echo $year->name;  # Ex: 2024-2025

# Changer l'année scolaire en session
app(\Modules\Config\Services\SchoolYearSessionService::class)
    ->setActiveYear($year);
```

### Points Clés

- ✅ Les données sont **filtrées par année en session** (pas de modification DB)
- ✅ Les utilisateurs peuvent **voir toutes les années** mais **modifier seulement l'année active**
- ✅ Les années passées sont **protégées** contre les modifications
- ✅ Seuls les admins peuvent modifier les données d'années précédentes avec la permission spéciale

## 🔒 Sécurité

### Protection des Données

- **Chiffrement des mots de passe** avec bcrypt
- **Historique des mots de passe** pour prévenir les réutilisations
- **Verrouillage de compte** après tentatives échouées
- **Whitelist IP** pour les comptes sensibles
- **Vérification email/téléphone** optionnelle

### Permission Spéciale : scholarity.modify_past_years

Cette permission contrôle qui peut modifier les données des années passées :

```php
# Accorder la permission au directeur
$director = User::where('email', 'director@school.edu')->first();
$permission = Permission::where('permission_id', 'scholarity.modify_past_years')->first();
$director->givePermission($permission);
```

## 📚 Documentation Complète

| Document | Description |
|----------|-------------|
| **[GUIDE_INSTALLATION.md](docs/GUIDE_INSTALLATION.md)** | Guide complet d'installation et configuration |
| **[GUIDE_DEPENDANCES.md](docs/GUIDE_DEPENDANCES.md)** | Arbre des dépendances et scénarios d'installation |
| **[VERIFICATION_ETAT.md](docs/VERIFICATION_ETAT.md)** | Vérification de l'état du système |
| **[GUIDE_ANNEES_SCOLAIRES.md](docs/GUIDE_ANNEES_SCOLAIRES.md)** | Gestion des années scolaires |
| **[GUIDE_ELEVES.md](docs/GUIDE_ELEVES.md)** | Module Students détaillé |
| **[STRUCTURE_MODULES.md](docs/STRUCTURE_MODULES.md)** | Architecture modulaire |
| **[OPTIMIZATION_DB.md](docs/OPTIMIZATION_DB.md)** | Optimisation des performances |

## 🔧 Commandes Artisan Principales

```bash
# Initialiser un nouveau client
php artisan client:initialize

# Voir l'état des migrations
php artisan migrate:status

# Exécuter les migrations
php artisan migrate

# Accéder à la console PHP
php artisan tinker

# Lancer le serveur de développement
php artisan serve
```

## 📊 Structure de Base de Données

Le système crée **46+ tables** organisées par module :

### Tables Core (Config)
- `school_info` - Informations du lycée
- `system_settings` - Paramètres système
- `school_years` - Années scolaires

### Tables Auth
- `users` - Comptes utilisateurs
- `roles` - Rôles disponibles
- `permissions` - Permissions système
- `role_permissions` - Attribution des permissions aux rôles

### Tables Métier
- **Students** : students, student_contacts, student_enrollments
- **Classes** : classes, class_assignments, class_subjects, timetables
- **Grades** : subjects, grades, grade_periods, grade_appeals
- **Attendance** : attendance_records, attendance_sessions, justifications
- **Billing** : invoices, payments, fee_structures, scholarships

## 🌍 Localisation

Actuellement supporté :
- 🇫🇷 **Français** (FR)
- 🇬🇧 **Anglais** (EN)

Fuseau horaire par défaut : Africa/Douala

## 📱 Environnement de Développement

### Avec Docker (Optionnel)

```bash
# Démarrer les services
docker-compose up -d

# Exécuter les migrations
docker-compose exec app php artisan migrate

# Initialiser un client
docker-compose exec app php artisan client:initialize
```

### Sans Docker

```bash
# Vérifier PHP 8.1+
php -v

# Installer les dépendances
composer install

# Configurer .env
cp .env.example .env
php artisan key:generate

# Initialiser la BD
php artisan migrate

# Lancer le serveur
php artisan serve
```

Accédez à : `http://localhost:8000`

## 🐛 Dépannage

### Problème : "Roles already exist"

```bash
php artisan client:initialize --skip-roles
```

### Problème : "School info already exists"

```bash
# Mettre à jour en base de données
php artisan tinker
> $school = SchoolInfo::first();
> $school->update(['name' => 'Nouveau Nom']);
```

### Problème : Les migrations ne s'exécutent pas

```bash
# Vérifier l'état des migrations
php artisan migrate:status

# Relancer les migrations
php artisan migrate --force
```

### Vérifier les permissions

```bash
php artisan tinker

# Lister tous les rôles
> Role::all();

# Vérifier les permissions de l'admin
> Role::where('name', 'admin')->first()->permissions->count();
```

## 🎯 Prochaines Étapes Après Installation

1. **Accédez à l'application** : `http://localhost:8000`
2. **Connectez-vous** avec les identifiants admin créés
3. **Configurez le logo** de votre lycée
4. **Créez les utilisateurs** : enseignants, surveillants, etc.
5. **Définissez les classes** pour l'année scolaire
6. **Importez les élèves** (en masse ou individuellement)
7. **Assignez les enseignants** aux classes
8. **Testez les workflows** : entrée de notes, présences, etc.

## 📞 Support et Questions

Pour tout problème ou question :

1. Consultez la documentation pertinente dans `/docs`
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Lancez `php artisan tinker` pour déboguer
4. Vérifiez l'état de la base de données

## 📄 Licence

© 2026 MyScholar - Tous droits réservés

---

**Système prêt pour le déploiement** ✅

Pour commencer : `php artisan client:initialize`
