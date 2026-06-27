# Guide des Dépendances des Modules MyScholar

## Vue d'Ensemble

MyScholar utilise 10 modules avec un graphe de dépendances clair. Quand vous sélectionnez un module pour l'installation, toutes les dépendances requises sont automatiquement incluses avec des avertissements clairs.

---

## Carte des Dépendances des Modules

### Modules Cœur (Aucune Dépendance)
Ces modules peuvent être installés indépendamment :

```
Config
├── Aucune dépendance
└── Requis par : Tous les autres modules

Auth
├── Aucune dépendance
└── Requis par : Tous les autres modules

Audit
├── Aucune dépendance
└── Requis par : Rien (optionnel)

Notifications
├── Aucune dépendance
└── Requis par : Rien (optionnel)

Reporting
├── Aucune dépendance
└── Requis par : Rien (optionnel)
```

### Modules Métier (Ont des Dépendances)

```
Students
├── Dépend de : [Config, Auth]
├── Requis par : [Classes, Grades, Attendance, Billing]
└── Tables : students, student_contacts, student_enrollments, family_contacts, student_history

Classes
├── Dépend de : [Config, Auth]
├── Requis par : [Grades, Attendance]
└── Tables : classes, class_assignments, class_subjects, rooms, timetables

Grades
├── Dépend de : [Config, Auth, Students, Classes]
├── Requis par : Rien
└── Tables : subjects, grade_periods, grades, averages_cache, class_averages, appeals

Attendance
├── Dépend de : [Config, Auth, Students, Classes]
├── Requis par : Rien
└── Tables : attendance_sessions, attendance_records, justifications, absence_counters, absence_alerts

Billing
├── Dépend de : [Config, Auth, Students]
├── Requis par : Rien
└── Tables : fee_structures, invoices, payments, installments, payment_plans, scholarships, fee_waivers
```

---

## Arbre de Décision pour la Sélection des Modules

### Si vous sélectionnez : **Config ou Auth**
```
✓ S'installe : Config, Auth
✓ Total : 2 modules
⚠️ Avertissement : Aucun (modules cœur)
```

### Si vous sélectionnez : **Audit, Notifications, ou Reporting**
```
✓ S'installe : Config, Auth, [sélectionné]
✓ Total : 3 modules
⚠️ Avertissement : Aucun (pas de dépendances)
```

### Si vous sélectionnez : **Students**
```
Demandé : Students
Auto-ajouté : (aucun - Config & Auth toujours inclus)
✓ S'installe : Config, Auth, Students
✓ Total : 3 modules
✓ Permissions : 7 (config 3 + auth 1 + students 4)
```

### Si vous sélectionnez : **Classes**
```
Demandé : Classes
Auto-ajouté : (aucun - Config & Auth toujours inclus)
✓ S'installe : Config, Auth, Classes
✓ Total : 3 modules
✓ Permissions : 8 (config 3 + auth 1 + classes 4)
```

### Si vous sélectionnez : **Students, Classes**
```
Demandé : Students, Classes
Auto-ajouté : (aucun)
✓ S'installe : Config, Auth, Students, Classes
✓ Total : 4 modules
✓ Permissions : 11 (config 3 + auth 1 + students 4 + classes 4)
```

### Si vous sélectionnez : **Grades** ⚠️
```
Demandé : Grades

⚠️  AVERTISSEMENT DE DÉPENDANCE
Les modules suivants ont été ajoutés automatiquement car ils sont requis :
  • Students (requis par : Grades)
  • Classes (requis par : Grades)

✓ Final : Config, Auth, Students, Classes, Grades
✓ Total : 5 modules
✓ Permissions : 15 (config 3 + auth 1 + students 4 + classes 4 + grades 4)
```

### Si vous sélectionnez : **Attendance** ⚠️
```
Demandé : Attendance

⚠️  AVERTISSEMENT DE DÉPENDANCE
Les modules suivants ont été ajoutés automatiquement car ils sont requis :
  • Students (requis par : Attendance)
  • Classes (requis par : Attendance)

✓ Final : Config, Auth, Students, Classes, Attendance
✓ Total : 5 modules
✓ Permissions : 14 (config 3 + auth 1 + students 4 + classes 4 + attendance 3)
```

### Si vous sélectionnez : **Grades, Attendance** ⚠️
```
Demandé : Grades, Attendance

⚠️  AVERTISSEMENT DE DÉPENDANCE
Les modules suivants ont été ajoutés automatiquement car ils sont requis :
  • Students (requis par : Grades, Attendance)
  • Classes (requis par : Grades, Attendance)

✓ Final : Config, Auth, Students, Classes, Grades, Attendance
✓ Total : 6 modules
✓ Permissions : 18
```

### Si vous sélectionnez : **Billing** ⚠️
```
Demandé : Billing

⚠️  AVERTISSEMENT DE DÉPENDANCE
Le module suivant a été ajouté automatiquement car il est requis :
  • Students (requis par : Billing)

✓ Final : Config, Auth, Students, Billing
✓ Total : 4 modules
✓ Permissions : 11 (config 3 + auth 1 + students 4 + billing 3)
```

### Si vous sélectionnez : **Tous** (flag --all)
```
Demandé : Tous les 10 modules

Aucun avertissement (toutes les dépendances déjà incluses)

✓ Final : Config, Auth, Audit, Notifications, Reporting, Students, Classes, Grades, Attendance, Billing
✓ Total : 10 modules
✓ Permissions : 27 (tous)
```

---

## Scénarios Réels d'Installation

### Scénario 1 : Petit Lycée Privé (Académie Seulement)
```bash
php artisan client:initialize --modules=Students,Classes,Grades
```

**Auto-inclusion :**
```
Demandé : Students, Classes, Grades
Auto-ajouté : (aucun)
Final : Config, Auth, Students, Classes, Grades
Total modules : 5
Permissions créées : 15
```

**Pourquoi ça fonctionne :**
- Students et Classes n'ont pas de dépendances supplémentaires
- Grades a besoin de Students et Classes (déjà sélectionnés)
- Config et Auth toujours inclus

---

### Scénario 2 : Lycée Orienté Facturation
```bash
php artisan client:initialize --modules=Billing
```

**Auto-inclusion :**
```
Demandé : Billing

⚠️  AVERTISSEMENT DE DÉPENDANCE
  • Students (requis par : Billing)

Final : Config, Auth, Students, Billing
Total modules : 4
Permissions créées : 11
```

**Pourquoi Students est ajouté :**
- Billing doit suivre quel élève doit quoi payer
- Sans le module Students, la facturation n'a pas de sens

---

### Scénario 3 : Système Orienté Présences
```bash
php artisan client:initialize --modules=Attendance
```

**Auto-inclusion :**
```
Demandé : Attendance

⚠️  AVERTISSEMENT DE DÉPENDANCE
  • Students (requis par : Attendance)
  • Classes (requis par : Attendance)

Final : Config, Auth, Students, Classes, Attendance
Total modules : 5
Permissions créées : 14
```

**Pourquoi Students et Classes sont ajoutés :**
- Attendance suit quel élève a assisté à quelle classe
- Les deux sont essentiels pour la fonctionnalité des présences

---

### Scénario 4 : Installation Audit Seulement
```bash
php artisan client:initialize --modules=Audit
```

**Auto-inclusion :**
```
Demandé : Audit

Aucun avertissement (Audit n'a pas de dépendances)

Final : Config, Auth, Audit
Total modules : 3
Permissions créées : 4
```

**Pourquoi aucun avertissement :**
- Le module Audit est autonome
- Fonctionne sans les autres modules métier
- Enregistre simplement toutes les activités système

---

### Scénario 5 : Installation Complète
```bash
php artisan client:initialize --all
```

**Auto-inclusion :**
```
Aucun avertissement (tous les modules demandés)

Final : Config, Auth, Audit, Notifications, Reporting, Students, Classes, Grades, Attendance, Billing
Total modules : 10
Permissions créées : 27
```

**Tout est disponible :**
- Ensemble de fonctionnalités complet pour la gestion scolaire
- Tous les ponts et relations actifs
- Capacités maximales et rapports complets

---

## Exemples de Chaînes de Dépendances

### Chaîne de Dépendance la Plus Longue
```
Grades
  ↓ (dépend de Students)
  ↓ (dépend de Classes)
  ↓ (dépend de Config, Auth)
Final : Config, Auth, Students, Classes, Grades (5 modules)
```

### Dépendance Multi-Complexe
```
Grades + Attendance
  ↓ (tous deux dépendent de Students)
  ↓ (tous deux dépendent de Classes)
  ↓ (tous dépendent de Config, Auth)
Final : Config, Auth, Students, Classes, Grades, Attendance (6 modules)
```

### Dépendance Simple
```
Students
  ↓ (dépend de Config, Auth)
Final : Config, Auth, Students (3 modules)
```

---

## Nombre de Permissions par Combinaison de Modules

| Sélection | Modules | Permissions | Notes |
|-----------|---------|-------------|-------|
| Config, Auth | 2 | 4 | Minimal - config seulement |
| + Students | 3 | 8 | Gestion basique des élèves |
| + Classes | 4 | 11 | Gestion des classes + élèves |
| + Grades | 5 (auto +Students, Classes) | 15 | Évaluation académique |
| + Attendance | 5 (auto +Students, Classes) | 14 | Suivi des présences |
| + Billing | 4 (auto +Students) | 11 | Gestion financière |
| Tous les 10 modules | 10 | 27 | Système complet |

---

## Commandes et Leurs Effets

### Commande 1 : Installer Tous
```bash
php artisan client:initialize --all
```
- ✓ Aucun avertissement
- ✓ 10 modules installés
- ✓ 27 permissions créées
- ✓ Tous les ponts actifs

### Commande 2 : Installer Spécifique avec Auto-inclusion
```bash
php artisan client:initialize --modules=Grades,Billing
```
- ⚠️ Les avertissements montrent Students, Classes ajoutés automatiquement
- ✓ 6 modules installés (Config, Auth, Students, Classes, Grades, Billing)
- ✓ 18 permissions créées
- ✓ Seuls les ponts pertinents actifs

### Commande 3 : Mode Interactif avec Infos de Dépendance
```bash
php artisan client:initialize
```
Affiche :
```
📌 Modules cœur (obligatoires) :
  ✓ Config
  ✓ Auth

📦 Modules optionnels (dépendances affichées) :
  • Audit (pas de dépendances supplémentaires)
  • Notifications (pas de dépendances supplémentaires)
  • Reporting (pas de dépendances supplémentaires)
  • Students (dépend de : Config, Auth)
  • Classes (dépend de : Config, Auth)
  • Grades (dépend de : Config, Auth, Students, Classes)
  • Attendance (dépend de : Config, Auth, Students, Classes)
  • Billing (dépend de : Config, Auth, Students)
```

---

## Dépannage des Dépendances des Modules

### Problème : Le module ne s'installe pas même s'il est sélectionné

**Vérifiez :** Le module sélectionné a-t-il des dépendances non satisfaites ?

```bash
# Exemple : Grades sélectionné mais Students non disponible
php artisan client:initialize --modules=Grades
# Résultat : Students est auto-ajouté avec avertissement
```

**Solution :** Le système inclut automatiquement les dépendances. Vérifiez les avertissements pour voir ce qui a été ajouté.

### Problème : Trop de modules installés

**Vérifiez :** Les dépendances ont-elles auto-ajouté des modules inattendus ?

```bash
# Exemple : Vouliez juste Attendance
php artisan client:initialize --modules=Attendance
# Résultat : Students, Classes auto-ajoutés (requis par Attendance)
```

**Solution :** Si vous voulez seulement des modules spécifiques, commencez avec Config + Auth seulement, puis ajoutez les modules indépendants (Audit, Notifications, Reporting).

### Problème : Les permissions n'apparaissent pas pour le module

**Vérifiez :** Les dépendances du module étaient-elles satisfaites ?

```bash
# Exemple : Grades sélectionné mais Classes non inclus
# Résultat : Les permissions Grades ne seront pas créées
```

**Solution :** Assurez-vous que toutes les dépendances sont dans les modules finaux. Vérifiez les avertissements lors de l'initialisation.

---

## Bonnes Pratiques

✅ **FAIRE :**
- Lire attentivement les avertissements de dépendance
- Commencer avec les modules cœur seulement si incertain
- Utiliser `--all` pour les installations complètes
- Vérifier `config/modules.json` après initialisation

❌ **NE PAS :**
- Essayer de modifier manuellement `config/modules.json` (relancez la commande à la place)
- S'attendre à ce que les modules fonctionnent sans leurs dépendances
- Ignorer les avertissements de dépendance
- Essayer d'installer Grades sans Students et Classes

---

## Tableau de Référence Rapide

| Module | Dépendances | Peut Utiliser Sans | Meilleur Avec |
|--------|-------------|-------------------|---------------|
| **Config** | Aucun | N'importe quoi | Tous les modules |
| **Auth** | Aucun | N'importe quoi | Tous les modules |
| **Audit** | Aucun | N'importe quoi | N'importe quel module |
| **Notifications** | Aucun | N'importe quoi | Tous les modules |
| **Reporting** | Aucun | N'importe quoi | Autres modules |
| **Students** | Config, Auth | Classes, Grades, Attendance | Billing |
| **Classes** | Config, Auth | Grades, Attendance | Students |
| **Grades** | Config, Auth, Students, Classes | Rien (dépend des deux) | Attendance |
| **Attendance** | Config, Auth, Students, Classes | Rien (dépend des deux) | Grades |
| **Billing** | Config, Auth, Students | Rien (dépend de Students) | Grades |

---

## Résumé

✅ **Gestion automatique :** Toutes les dépendances sont incluses automatiquement
✅ **Avertissements clairs :** Le système vous avertit quels modules ont été auto-ajoutés et pourquoi
✅ **Aide interactive :** Affiche les dépendances lors de la sélection des modules
✅ **Validation :** Empêche les combinaisons invalides
✅ **Configurable :** Choisissez tous les modules ou seulement ceux dont vous avez besoin

**Vous n'avez jamais à gérer manuellement les dépendances — le système le fait pour vous !**

---

## Notes Importantes pour les Administrateurs

### Pour une Petite École
Recommandé : `--modules=Students,Classes,Grades` (5 modules, 15 permissions)

### Pour une École Complète
Recommandé : `--all` (10 modules, 27 permissions)

### Pour une École Avec Budget Limité
Recommandé : `--modules=Students,Classes` (4 modules, 11 permissions) - Ajoutez d'autres modules plus tard

### Pour une École Orientée Finances
Recommandé : `--modules=Students,Billing` (auto-inclut Classes si nécessaire)

---

Pour plus de détails sur chaque module, consultez le [GUIDE_INSTALLATION.md](./GUIDE_INSTALLATION.md).
