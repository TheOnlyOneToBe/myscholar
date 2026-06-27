# Gestion des Années Scolaires

Guide complet pour gérer les années académiques de votre établissement avec l'interface Livewire.

## 🎯 Vue d'ensemble

Le système de gestion des années scolaires permet de:
- Créer et gérer les années académiques
- Définir une année comme "active" (année en cours)
- Sélectionner l'année "en session" (année pour les opérations courantes)
- Afficher les informations de chaque année
- Respecter les règles métier (une seule année active)

## 📱 Interface Utilisateur

### Accès à la page de gestion

```
URL: /config/school-years
Permission requise: config.edit
```

La page affiche trois sections principales:

### 1️⃣ Année Scolaire en Cours (Active)

```
┌─────────────────────────────────────────┐
│ 📅 Année Scolaire en Cours              │
├─────────────────────────────────────────┤
│ 2024-2025                         [●Active]│
│ Du 01/09/2024 au 30/06/2025             │
│ Description optionnelle...              │
└─────────────────────────────────────────┘
```

- **Une seule année peut être active à la fois**
- L'année active est celle en cours depuis une perspective administrative
- Affichée avec un badge vert "Active"

### 2️⃣ Année Scolaire en Session

```
┌─────────────────────────────────────────┐
│ 🎯 Année Scolaire en Session            │
├─────────────────────────────────────────┤
│ 2024-2025                         [●Session]│
│ Du 01/09/2024 au 30/06/2025             │
│                                         │
│ (Année utilisée pour les opérations     │
│  courantes: saisie des notes, etc.)     │
└─────────────────────────────────────────┘
```

- L'année sélectionnée pour le travail actuel
- Peut être différente de l'année active
- Utilisée comme filtre pour les données (notes, présences, etc.)
- Affichée avec un badge bleu "Session"

### 3️⃣ Liste des Années

Tableau avec toutes les années et leurs actions:

| Année | Période | Années | Statut | Actions |
|-------|---------|--------|--------|---------|
| 2024-2025 | 01/09/2024 - 30/06/2025 | 2024-2025 | En cours | [Activer] [Session] [Modifier] [Supprimer] |
| 2023-2024 | 01/09/2023 - 30/06/2024 | 2023-2024 | Disponible | [Activer] [Session] [Modifier] [Supprimer] |

## 🔧 Opérations Disponibles

### Créer une Année

1. Cliquez sur **"Nouvelle Année"**
2. Remplissez le formulaire:
   - **Nom** (ex: 2024-2025) - Unique et requis
   - **Année de début** (ex: 2024)
   - **Année de fin** (ex: 2025)
   - **Date de début** (ex: 01/09/2024)
   - **Date de fin** (ex: 30/06/2025) - Doit être après la date de début
   - **Description** (optionnel)

3. Cliquez **"Créer"**
4. Un message de confirmation s'affiche

```
✅ Année scolaire créée avec succès [SCHOOL_YEAR_CREATED]
```

### Modifier une Année

1. Cliquez sur le bouton **"Modifier"** (icône ✏️) d'une année
2. Le formulaire se remplisse avec les données actuelles
3. Modifiez les champs nécessaires
4. Cliquez **"Modifier"**

**Restrictions:**
- Les années archivées (verrouillées) ne peuvent pas être modifiées
- L'année active ne peut pas être supprimée

### Activer une Année

1. Cliquez sur le bouton **"Activer"** pour une année non-active
2. Confirmez l'action
3. L'année précédemment active est automatiquement désactivée

```
✅ Année scolaire 2024-2025 activée [SCHOOL_YEAR_ACTIVATED]
```

**Résultat:**
- Une seule année est active à la fois
- L'année activée devient l'année "en session" par défaut
- Elle apparaît dans le calendrier administratif

### Changer l'Année en Session

1. Cliquez sur le bouton **"Session"** pour une année
2. L'année est sélectionnée comme année de travail actuelle

```
✅ Année en session changée vers 2024-2025 [SESSION_SWITCHED]
```

**Utilisation:**
- Permet de travailler sur une année différente de l'année active
- Les données filtrées sont basées sur l'année en session
- Un événement Livewire est déclenché: `school-year-changed`

### Supprimer une Année

1. Cliquez sur le bouton **"Supprimer"** (icône 🗑️)
2. Confirmez la suppression

**Restrictions:**
- **Impossible de supprimer l'année active**
- Impossible de supprimer une année verrouillée (archivée)

```
❌ Impossible de supprimer l'année scolaire active
```

## 📊 Statuts des Années

### En cours (Active)
- Une seule année peut avoir ce statut
- Badge vert avec la mention "En cours"
- Ne peut pas être supprimée
- Activée manuellement

### Disponible
- Année pas encore activée ou ancienne
- Badge gris "Disponible"
- Peut être activée ou supprimée

### Archivée
- Année verrouillée (is_locked = true)
- Badge gris "Archivée"
- Ne peut pas être modifiée ou supprimée

## 🔗 Intégration Livewire

### Événements

L'année de session est gérée via les événements Livewire:

```javascript
// Lorsqu'une année est activée ou la session change
Livewire.on('school-year-changed', (schoolYearId) => {
    console.log('Year changed to:', schoolYearId);
    // Recharger les données avec la nouvelle année
});
```

### Utilisation dans d'autres Composants

Pour écouter les changements d'année:

```php
#[On('school-year-changed')]
public function onSchoolYearChanged($schoolYearId)
{
    // Recharger les données de l'année
    $this->schoolYear = SchoolYear::find($schoolYearId);
    $this->loadData();
}
```

## 💾 Gestion des Sessions

### Session Utilisateur

L'année en session est stockée dans la session Laravel:

```php
// Clé de session: active_school_year_id
session('active_school_year_id') // Retourne l'ID de l'année
```

### Service SchoolYearSessionService

Classe utilitaire pour gérer la session:

```php
use Modules\Config\Services\SchoolYearSessionService;

$service = new SchoolYearSessionService();

// Obtenir l'année en session
$year = $service->getActiveYear();

// Définir l'année en session
$service->setActiveYear($year);

// Initialiser la session avec l'année active
$service->initializeSession();

// Vérifier si c'est l'année en session
$isSession = $service->isCurrentSession($year);
```

## 🎨 Alerts et Notifications

Toutes les opérations génèrent des alertes visibles en haut de la page:

### Succès
```
✅ Année scolaire créée avec succès [SCHOOL_YEAR_CREATED]
✅ Année scolaire modifiée avec succès [SCHOOL_YEAR_UPDATED]
✅ Année scolaire supprimée avec succès [SCHOOL_YEAR_DELETED]
✅ Année scolaire 2024-2025 activée [SCHOOL_YEAR_ACTIVATED]
✅ Année en session changée vers 2024-2025 [SESSION_SWITCHED]
```

### Erreurs
```
❌ Impossible de supprimer l'année scolaire active [CANNOT_DELETE_ACTIVE]
❌ Erreur lors de la création: ... [CREATE_ERROR]
❌ Erreur lors de la modification: ... [UPDATE_ERROR]
❌ Erreur lors de la suppression: ... [DELETE_ERROR]
❌ Erreur lors de l'activation: ... [ACTIVATION_ERROR]
❌ Erreur lors du changement de session: ... [SWITCH_ERROR]
```

## 🔐 Règles Métier

### Année Active
- **Une seule année active à la fois**
- L'activation d'une nouvelle année désactive automatiquement la précédente
- Ne peut pas être supprimée
- Ne peut pas être dupliquée

### Année en Session
- Sélectable librement parmi les années disponibles
- Indépendante de l'année active
- Utilisée pour filtrer les données opérationnelles

### Dates
- La date de fin doit être après la date de début
- Les années ne peuvent pas se chevaucher (optionnel, selon implémentation)

### Noms Uniques
- Le champ `name` doit être unique
- Format recommandé: "2024-2025"

## 📱 API REST (Alternative)

Pour accéder à la gestion des années via l'API:

### Lister les années
```bash
GET /api/config/school-years
```

### Obtenir l'année active
```bash
GET /api/config/school-years/current
```

### Créer une année
```bash
POST /api/config/school-years
Content-Type: application/json

{
  "name": "2024-2025",
  "start_year": 2024,
  "end_year": 2025,
  "start_date": "2024-09-01",
  "end_date": "2025-06-30",
  "description": "Année scolaire 2024-2025"
}
```

### Modifier une année
```bash
PUT /api/config/school-years/{id}
```

### Activer une année
```bash
POST /api/config/school-years/{id}/activate
```

### Supprimer une année
```bash
DELETE /api/config/school-years/{id}
```

## 🎓 Cas d'Usage

### 1. Début de l'année scolaire

À la rentrée:
1. ✅ Créer la nouvelle année (ex: 2025-2026)
2. ✅ Sélectionner comme "Session"
3. ✅ Activer comme année "En cours"

### 2. Gestion multi-annuelle

Pour travailler sur des années passées:
1. Garder l'année courante active
2. Changer l'année en session vers l'année passée
3. Les opérations utiliseront les données de l'année en session

### 3. Archivage d'années

Pour archiver les données:
1. Verrouiller l'année (marquer comme is_locked = true)
2. L'année ne peut plus être modifiée
3. Les données restent accessibles en lecture

## 🐛 Troubleshooting

### "Aucune année scolaire active configurée"

**Cause**: Pas d'année a l'statut "active"

**Solution**:
1. Créez une nouvelle année
2. Cliquez sur "Activer"

### "Impossible de supprimer l'année scolaire active"

**Cause**: Vous tentez de supprimer l'année active

**Solution**:
1. Activez une autre année d'abord
2. Puis supprimez l'année précédente

### Les données ne changent pas avec la session

**Cause**: Le composant n'écoute pas l'événement `school-year-changed`

**Solution**:
1. Ajoutez `#[On('school-year-changed')]` au composant
2. Recharger les données dans cette méthode

## 📚 Documentation Supplémentaire

- [Système d'Alertes](./ALERTS_SYSTEM.md)
- [Configuration de Base de Données](./DATABASE_CONFIG.md)
- [Architecture Modulaire](./plan.md)

---

✅ **Système de gestion d'années scolaires opérationnel et intégré!**
