# Module Teachers

Gestion complète des enseignants du lycée camerounais.

## 📋 Fonctionnalités

- ✅ **Gestion des profils enseignants** : Création, modification, suppression
- ✅ **Filières** : Classification par filière générale ou technique
- ✅ **Matières** : Assignation et suivi des matières enseignées
- ✅ **Qualifications** : Enregistrement des diplômes et certifications
- ✅ **Assignations** : Attribution des classes et heures d'enseignement
- ✅ **Historique** : Suivi complet des modifications et actions
- ✅ **Permissions** : Contrôle d'accès granulaire

## 🗂️ Structure des Tables

### teachers
Profil de l'enseignant
```
- id
- user_id (FK)
- teacher_code (matricule unique)
- specialization (matière principale)
- qualification_level (diplôme)
- hire_date (date d'embauche)
- filiere (generale | technique)
- office_location
- years_of_experience
- is_active
```

### teacher_qualifications
Qualifications et diplômes
```
- id
- teacher_id
- qualification_name (CAPES, Master, etc)
- issuing_institution
- year_obtained
- is_verified
```

### teacher_subjects
Matières enseignées (Many-to-Many)
```
- id
- teacher_id
- subject_id
- proficiency_level (1-5)
- since_year
- is_primary
```

### teacher_classes
Classes assignées (Many-to-Many)
```
- id
- teacher_id
- class_id
- subject_id
- school_year_id
- hours_per_week
- status (active | suspended | completed)
```

### teacher_history
Historique des actions
```
- id
- teacher_id
- action (hired, transferred, promoted, etc)
- description
- metadata (JSON)
- created_by
```

## 🔑 Rôles et Permissions

### Rôles Concernés

| Rôle | Niveau | Permissions |
|------|--------|------------|
| **super_administrator** | 0 | Tout gérer |
| **proviseur** | 1 | Créer, modifier, supprimer enseignants |
| **censeur** | 2 | Assigner classes et matières |
| **prof_principal** | 3 | Voir leurs assignations, assigner dans leur classe |
| **enseignant** | 4 | Voir leurs propres infos |
| **secretaire** | 3 | Gestion administrative |
| **comptable** | 3 | Suivi de la masse salariale |

### Permissions Teachers

- `teachers.view_all` - Voir tous les enseignants
- `teachers.view` - Voir détails enseignant
- `teachers.create` - Créer un enseignant
- `teachers.update` - Modifier un enseignant
- `teachers.delete` - Supprimer un enseignant
- `teachers.manage_assignments` - Assigner/désassigner classes
- `teachers.manage_qualifications` - Gérer qualifications
- `teachers.manage_subjects` - Gérer matières enseignées
- `teachers.view_classes` - Voir classes assignées
- `teachers.view_history` - Voir historique

## 🔌 API Endpoints

### Teachers CRUD
```
GET    /api/teachers              - Lister tous les enseignants
POST   /api/teachers              - Créer un enseignant
GET    /api/teachers/{id}         - Détails d'un enseignant
PATCH  /api/teachers/{id}         - Modifier un enseignant
DELETE /api/teachers/{id}         - Supprimer un enseignant
```

### Informations Complémentaires
```
GET /api/teachers/{id}/qualifications  - Qualifications
GET /api/teachers/{id}/classes         - Classes assignées
GET /api/teachers/{id}/hours           - Total heures/semaine
GET /api/teachers/{id}/history         - Historique
```

### Assignations
```
POST   /api/teachers/{id}/assignments/classes           - Assigner à une classe
DELETE /api/teachers/{id}/assignments/classes/{assign}  - Retirer d'une classe
PATCH  /api/teachers/{id}/assignments/classes/{assign}/status - Modifier statut

POST   /api/teachers/{id}/assignments/subjects          - Ajouter une matière
DELETE /api/teachers/{id}/assignments/subjects          - Retirer une matière
```

## 📦 Nouvelle Hiérarchie des Rôles

```
NIVEAU 0 : Administrateur Système
├─ super_administrator

NIVEAU 1 : Direction
├─ proviseur

NIVEAU 2 : Pédagogie
├─ censeur

NIVEAU 3 : Encadrement & Administration
├─ prof_principal (classe)
├─ chef_classe (leader élève)
├─ secretaire (administration)
├─ comptable (finances)
├─ infirmier (santé)
├─ bibliothecaire (documentation)

NIVEAU 4 : Enseignement & Maintenance
├─ enseignant
├─ gardien

NIVEAU 5 : Surveillance
├─ surveillant

NIVEAU 99 : Externes (Parents)
├─ parent

NIVEAU 100 : Externes (Apprenants)
└─ student
```

## 🚀 Installation et Seeders

Exécuter les migrations et seeders :

```bash
php artisan migrate
php artisan db:seed --class="Modules\\Teachers\\Seeders\\DatabaseSeeder"
```

Cela va :
1. ✅ Créer les 5 nouveaux rôles (Secrétaire, Comptable, Infirmier, Bibliothécaire, Gardien)
2. ✅ Créer toutes les permissions Teachers
3. ✅ Assigner les permissions aux rôles
