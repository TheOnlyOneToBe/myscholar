# Système de Gestion des Enseignants - Documentation Complète

## Vue d'ensemble

Le système MyScholar inclut un module complet de gestion des enseignants permettant:
- La candidature en ligne des enseignants
- La déclaration des compétences et matières
- L'approbation des candidatures par l'administration
- L'attribution des matières aux classes
- Le suivi des heures d'enseignement
- L'historique pédagogique

## Architecture

### Modèles de Base

#### 1. **User** (Module Auth)
- Représente l'utilisateur système
- Lié à un `Teacher` via foreign key
- Possède des rôles: enseignant, administrateur, etc.

#### 2. **Teacher**
- Profil complet de l'enseignant approuvé
- Champs:
  - `user_id` - Référence à l'utilisateur
  - `teacher_code` - Matricule unique (ex: PROF-12345)
  - `specialization` - Spécialisation principale (Mathématiques, etc.)
  - `qualification_level` - Niveau de qualification (Bac+2, Bac+3, Master, Doctorat)
  - `hire_date` - Date d'embauche
  - `filiere` - Type de filière (générale, technique)
  - `office_location` - Bureau/salle (ex: A-201)
  - `years_of_experience` - Années d'expérience
  - `is_active` - Statut actif/inactif
  - `bio` - Biographie professionnelle
  - `phone_office` - Téléphone professionnel
  - `email_office` - Email professionnel

#### 3. **TeacherApplication** (Nouveau)
- Formulaire de candidature rempli en ligne par les futurs enseignants
- Statuts: pending, approved, rejected
- Contient tous les détails que l'admin doit approuver avant création du Teacher record
- Champs:
  - `user_id` - Utilisateur candidat
  - `subjects_data` - JSON avec les matières déclarées (id, niveau, année)
  - `status` - État de la candidature
  - `rejection_reason` - Raison du rejet (si applicable)
  - `approved_by` - Admin qui a approuvé
  - `approved_at` - Date d'approbation

#### 4. **TeacherSubject** (Pivot Table)
- Relation many-to-many entre Teacher et Subject
- Champs pivot:
  - `proficiency_level` - Niveau de maîtrise (1-5: Débutant, Intermédiaire, Compétent, Expert, Maître)
  - `since_year` - Année de début d'enseignement de cette matière
  - `is_primary` - Indique la spécialité principale

#### 5. **TeacherClass** (Pivot Table)
- Relation many-to-many entre Teacher et SchoolClass
- Champs pivot:
  - `subject_id` - La matière enseignée dans cette classe
  - `school_year_id` - L'année scolaire
  - `hours_per_week` - Nombre d'heures par semaine
  - `status` - État de l'attribution (active, suspended, completed)

#### 6. **TeacherQualification**
- Historique des qualifications professionnelles

#### 7. **TeacherHistory**
- Historique des modifications et actions effectuées sur le profil

## Flux de Travail

### Phase 1: Candidature en Ligne

1. **L'utilisateur accède à**: `/teacher-application`
2. **Remplit le formulaire** avec:
   - Informations personnelles
   - Qualifications
   - Matières enseignées (avec niveau de maîtrise 1-5)
   - Expérience

3. **Soumet la candidature**
   - État initial: `pending`
   - Les données sont stockées dans `teacher_applications`

### Phase 2: Approbation Administrative

1. **L'administrateur accède à**: `/admin/teacher-applications`
2. **Voit la liste** des candidatures en attente
3. **Peut**:
   - Approuver: Crée un `Teacher` record et attache les matières
   - Rejeter: Enregistre la raison du rejet

### Phase 3: Gestion des Matières

1. Une fois approuvé, l'enseignant peut gérer ses matières
2. **Accès**: `/teacher/{teacher}/subjects`
3. **Peut**:
   - Ajouter de nouvelles matières
   - Modifier niveau de maîtrise
   - Marquer une spécialité principale
   - Supprimer des matières

### Phase 4: Attribution aux Classes

L'administrateur peut assigner l'enseignant à des classes:
- Une classe + une matière
- Nombre d'heures par semaine
- Pour une année scolaire spécifique

## Composants Livewire

### 1. **TeacherApplicationForm**
- Formulaire de candidature pour les futurs enseignants
- Localisation: `modules/Teachers/Livewire/TeacherApplicationForm.php`
- Fonctionnalités:
  - Multi-étapes
  - Sélection dynamique des matières
  - Niveaux de maîtrise (1-5)
  - Validation en temps réel

### 2. **TeacherApplicationReview**
- Interface d'examen des candidatures (admin)
- Localisation: `modules/Teachers/Livewire/TeacherApplicationReview.php`
- Fonctionnalités:
  - Filtrage (pending, approved, rejected)
  - Affichage détaillé
  - Approbation/Rejet avec raison

### 3. **TeacherSubjectManagement**
- Gestion des matières pour les enseignants approuvés
- Localisation: `modules/Teachers/Livewire/TeacherSubjectManagement.php`
- Fonctionnalités:
  - Ajout/suppression de matières
  - Modification du niveau de maîtrise
  - Marquage de spécialité principale
  - Affichage des années d'enseignement

## Routes

### Routes Web (Livewire)
```
GET  /teacher-application              → TeacherApplicationForm
GET  /teacher/{teacher}/subjects        → TeacherSubjectManagement
GET  /admin/teacher-applications       → TeacherApplicationReview (admin only)
```

### Routes API
```
GET    /api/teachers                           → Liste des enseignants
POST   /api/teachers                           → Créer enseignant
GET    /api/teachers/{teacher}                 → Détails enseignant
PUT    /api/teachers/{teacher}                 → Modifier enseignant
DELETE /api/teachers/{teacher}                 → Supprimer enseignant

GET    /api/teachers/{teacher}/qualifications  → Qualifications
GET    /api/teachers/{teacher}/classes         → Classes assignées
GET    /api/teachers/{teacher}/hours           → Total heures/semaine
GET    /api/teachers/{teacher}/history         → Historique

POST   /api/teachers/{teacher}/assignments/classes           → Assigner classe
DELETE /api/teachers/{teacher}/assignments/classes/{id}      → Retirer classe
PATCH  /api/teachers/{teacher}/assignments/classes/{id}/status → Modifier statut

POST   /api/teachers/{teacher}/assignments/subjects          → Ajouter matière
DELETE /api/teachers/{teacher}/assignments/subjects          → Retirer matière

GET    /api/teachers/applications/              → Lister candidatures (admin)
GET    /api/teachers/applications/my            → Ma candidature (candidat)
GET    /api/teachers/applications/{id}          → Détails candidature
POST   /api/teachers/applications/{id}/approve  → Approuver (admin)
POST   /api/teachers/applications/{id}/reject   → Rejeter (admin)
```

## Permissions & Rôles

### Rôles Impliqués
- **super_administrator**: Accès complet
- **proviseur**: Peut approuver/rejeter, gérer enseignants
- **censeur**: Peut approuver/rejeter, gérer enseignants
- **enseignant**: Peut remplir candidature, gérer ses matières

### Gates Définis
- `review-teacher-applications`: Voir les candidatures (admin)
- `approve-teacher-application`: Approuver/rejeter (admin)

## Niveaux de Maîtrise (Proficiency)

| Niveau | Nom | Description |
|--------|-----|-------------|
| 1 | Débutant | Peut enseigner mais a besoin de soutien |
| 2 | Intermédiaire | Peut enseigner de manière indépendante |
| 3 | Compétent | Maîtrise bien la matière (par défaut) |
| 4 | Expert | Très bon pédagogue, peut former d'autres |
| 5 | Maître | Expert confirmé, leader pédagogique |

## Utilisation

### Pour un Candidat Enseignant

1. Accéder à `/teacher-application`
2. Remplir le formulaire complet
3. Ajouter les matières qu'on peut enseigner (avec niveau 1-5)
4. Marquer sa spécialité principale
5. Soumettre
6. Attendre approbation de l'admin

### Pour l'Administration

1. Accéder à `/admin/teacher-applications`
2. Consulter les candidatures en attente
3. Examiner les qualifications et matières
4. Approuver (crée le Teacher record) ou Rejeter (avec raison)

### Pour la Gestion des Classes

1. Via l'API ou l'interface admin
2. Assigner un enseignant à une classe pour une matière
3. Définir heures par semaine
4. Suivre les heures totales par enseignant

## Exemple de Requête API

### Approuver une candidature
```bash
POST /api/teachers/applications/123/approve
Content-Type: application/json
Authorization: Bearer {token}
```

Réponse:
```json
{
  "message": "Candidature approuvée avec succès.",
  "data": {
    "id": 123,
    "user_id": 45,
    "status": "approved",
    "approved_by": 1,
    "approved_at": "2026-06-28T10:30:00Z"
  }
}
```

## Validations

### TeacherApplication
- `specialization`: Requis, string, max 255
- `qualification_level`: Requis
- `hire_date`: Date valide
- `filiere`: générale ou technique
- `years_of_experience`: Integer, min 0
- `selectedSubjects`: Array, min 1 matière

### TeacherSubject Pivot
- `proficiency_level`: 1-5
- `since_year`: 1900 à année courante
- `is_primary`: Boolean

### TeacherClass Pivot
- `hours_per_week`: Integer, min 1
- `status`: active, suspended, ou completed

## Sécurité

- Authentification requise pour tous les endpoints
- Authorization check via Gates et Policies
- Validation des données côté serveur
- CSRF protection sur les formulaires web
- Rate limiting sur les API sensibles

## Prochaines Étapes

1. Tests unitaires pour TeacherApplication
2. Tests d'intégration pour le flux complet
3. Notifications email aux enseignants (approbation/rejet)
4. Rapport d'heures d'enseignement
5. Dashboard statistiques enseignants
