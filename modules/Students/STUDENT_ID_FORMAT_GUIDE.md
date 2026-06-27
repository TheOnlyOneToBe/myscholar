# Guide de Configuration du Format des Matricules Étudiants

## Vue d'ensemble

Le système MyScholar permet au directeur de configurer le format des matricules étudiants de manière flexible. Vous pouvez choisir:
- Les éléments à inclure
- L'ordre de ces éléments
- Le séparateur entre les éléments

## Éléments disponibles

| Élément | Description | Exemple |
|---------|-------------|---------|
| `filiere` | Filière/Programme d'études | SCI, LIT, MATH, ENG |
| `YYYY` | Année complète (4 chiffres) | 2024 |
| `YY` | Année courte (2 chiffres) | 24 |
| `MM` | Mois (2 chiffres) | 06 |
| `DD` | Jour (2 chiffres) | 27 |
| `####` | Numéro séquentiel (4 chiffres) | 0001, 0125, 5432 |
| `###` | Numéro séquentiel (3 chiffres) | 001, 125, 543 |
| `##` | Numéro séquentiel (2 chiffres) | 01, 12, 54 |
| `#` | Numéro séquentiel (1 chiffre) | 1, 2, 5 |

## Exemples de configurations

### Configuration 1: Format classique avec tirets
**Éléments:** filiere, YYYY, ####  
**Séparateur:** `-`  
**Résultat:** `SCI-2024-0001`, `LIT-2024-0125`

### Configuration 2: Format sans séparateur (collé)
**Éléments:** filiere, YYYY, ####  
**Séparateur:** `` (vide)  
**Résultat:** `SCI20240001`, `LIT20240125`

### Configuration 3: Ordre inversé avec tirets
**Éléments:** ####, filiere, YY  
**Séparateur:** `-`  
**Résultat:** `0001-SCI-24`, `0125-LIT-24`

### Configuration 4: Avec date complète et slash
**Éléments:** filiere, MM, DD, YY, ###  
**Séparateur:** `/`  
**Résultat:** `SCI/06/27/24/001`, `LIT/06/27/24/125`

### Configuration 5: Format numérique avec année
**Éléments:** YYYY, ##, ####  
**Séparateur:** `-`  
**Résultat:** `2024-12-0001` (où 12 est le mois)

## Comment configurer via la ligne de commande

Exécutez la commande:
```bash
php artisan school:configure-student-id-format
```

La commande vous guidera de manière interactive:
1. Affichage des éléments disponibles
2. Sélection des éléments à inclure
3. Choix de l'ordre des éléments
4. Choix du séparateur
5. Aperçu du format avant confirmation

## Comment configurer via l'API REST

### 1. Récupérer la configuration actuelle
```bash
GET /api/students/id-format
```

**Réponse:**
```json
{
  "elements": ["filiere", "YYYY", "####"],
  "separator": "-",
  "pattern": "{filiere}-{YYYY}-{####}",
  "example": "SCI-2024-0001",
  "available_elements": {
    "filiere": "Filière/Programme",
    "YYYY": "Année (4 chiffres)",
    ...
  }
}
```

### 2. Récupérer les éléments disponibles
```bash
GET /api/students/id-format/available-elements
```

**Réponse:**
```json
{
  "available_elements": {
    "filiere": "Filière/Programme",
    "YYYY": "Année (4 chiffres)",
    "YY": "Année (2 chiffres)",
    ...
  }
}
```

### 3. Aperçu d'un format avant sauvegarde
```bash
POST /api/students/id-format/preview
Content-Type: application/json

{
  "elements": ["####", "filiere", "YY"],
  "separator": "-"
}
```

**Réponse:**
```json
{
  "pattern": "{####}-{filiere}-{YY}",
  "example": "0001-SCI-24"
}
```

### 4. Mettre à jour la configuration
```bash
PUT /api/students/id-format
Content-Type: application/json

{
  "elements": ["filiere", "YYYY", "####"],
  "separator": "-"
}
```

**Réponse:**
```json
{
  "message": "Format des matricules mis à jour avec succès",
  "elements": ["filiere", "YYYY", "####"],
  "separator": "-",
  "pattern": "{filiere}-{YYYY}-{####}",
  "example": "SCI-2024-0001"
}
```

## Notes importantes

1. **Filière requise:** Si vous utilisez l'élément `filiere`, il doit être fourni lors de la création d'un étudiant. Assurez-vous que la filière de l'étudiant est définie.

2. **Numéro séquentiel:** Les numéros séquentiels (####, ###, ##, #) sont générés automatiquement. Choisissez selon le nombre d'étudiants attendus:
   - `#` : jusqu'à 9 étudiants
   - `##` : jusqu'à 99 étudiants
   - `###` : jusqu'à 999 étudiants
   - `####` : jusqu'à 9999 étudiants

3. **Modification du format:** Si vous modifiez le format après avoir créé des étudiants, les matricules existants ne seront pas affectés. Seuls les nouveaux étudiants utiliseront le nouveau format.

4. **Validation:** Le système valide automatiquement que chaque matricule généré correspond au format configuré.

## Bonnes pratiques

- **Simplicité:** Limitez le nombre d'éléments à 3-4 pour une meilleure lisibilité
- **Pertinence:** Incluez des éléments qui ont du sens pour votre institution
- **Unicité:** Assurez-vous que le format garantit l'unicité des matricules
- **Pérennité:** Évitez les changements de format trop fréquents

## Exemple complet d'utilisation

**Scénario:** Vous dirigez un lycée et souhaitez que les matricules soient:
- Composés de la filière (SCI, LIT, etc.)
- De l'année d'entrée (2024)
- D'un numéro séquentiel (0001, 0002, etc.)
- Séparés par des tirets

**Configuration:**
```json
{
  "elements": ["filiere", "YYYY", "####"],
  "separator": "-"
}
```

**Matricules générés:**
- SCI-2024-0001
- SCI-2024-0002
- LIT-2024-0001
- LIT-2024-0002
- MATH-2024-0001
