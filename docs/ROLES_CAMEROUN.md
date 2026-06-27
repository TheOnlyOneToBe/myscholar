# 🇨🇲 Structure Administrative des Lycées Camerounais

## Vue d'Ensemble

Un lycée camerounais (public ou privé) a une structure hiérarchique bien définie. Voici les acteurs principaux et leurs responsabilités.

---

## 1. **PROVISEUR (Directeur Général)**

**Nature** : Chef exécutif du lycée

**Responsabilités** :
- Direction générale de l'établissement
- Gestion administrative et pédagogique
- Discipline générale
- Rapport avec l'administration (Délégation Régionale)
- Approbation des décisions majeures
- Signature des documents officiels

**Hiérarchie** : ⭐ NIVEAU 1 (Le plus élevé)

**Pouvoirs sur les autres** :
- Peut créer/modifier/supprimer tous les utilisateurs sauf Admin
- Peut assigner/retirer tous les rôles sauf Admin
- Peut approuver les demandes importantes

**Permissions système** :
- Accès complet configuration
- Gestion complète des utilisateurs
- Gestion complète des classes
- Gestion des notes (consultation + approbation)
- Gestion des présences (consultation)
- Gestion financière complète
- Audit logs complet

---

## 2. **CENSEUR (Censeur Pédagogique / Sous-directeur)**

**Nature** : Responsable pédagogique direct

**Responsabilités** :
- Supervision pédagogique quotidienne
- Gestion des absences et discipline
- Contrôle des notes et évaluations
- Planning des classes et emploi du temps
- Suivi des parents
- Remplace le Proviseur en son absence

**Hiérarchie** : ⭐⭐ NIVEAU 2

**Pouvoirs sur les autres** :
- Peut créer/modifier : Prof Principal, Chef Classe, Enseignant, Surveillant
- Ne peut PAS toucher à Proviseur
- Suivi quotidien de tous les cours

**Permissions système** :
- Gestion des classes
- Gestion complète des notes
- Gestion complète des présences
- Gestion des utilisateurs (inférieurs)
- Consultation des finances
- Audit logs

---

## 3. **PROFESSEUR PRINCIPAL**

**Nature** : Responsable d'une classe

**Responsabilités** :
- Gestion administrative de la classe
- Liaison entre élèves, parents, enseignants
- Appel quotidien et gestion des absences
- Discipline de la classe
- Moyenne générale de la classe
- Rencontre avec les parents

**Hiérarchie** : ⭐⭐⭐ NIVEAU 3

**Pouvoirs sur les autres** :
- Peut assigner Chef de Classe pour sa classe
- Aucun autre pouvoir de création d'utilisateurs

**Permissions système** :
- Voir/modifier notes de ses élèves uniquement
- Voir/modifier présences de sa classe
- Voir informations financières de ses élèves
- Communiquer avec parents

**Cas spécial** :
- Un Enseignant peut être Prof Principal en plus de sa classe
- Rôle : Enseignant + Prof Principal (rôles multiples)

---

## 4. **CHEF DE CLASSE**

**Nature** : Leader étudiant de la classe

**Responsabilités** :
- Représentant des élèves
- Liaison entre classe et direction
- Aide au contrôle de discipline
- Collecte d'informations pour le prof principal
- Leader émotionnel de la classe

**Hiérarchie** : ⭐⭐⭐ NIVEAU 3 (same as Prof Principal)

**Pouvoirs sur les autres** :
- Aucun pouvoir administratif
- Rôle plutôt représentatif

**Permissions système** :
- Voir notes de sa classe
- Voir présences de sa classe
- Voir infos d'élèves de sa classe
- Envoyer messages au prof principal

---

## 5. **ENSEIGNANT (Professeur)**

**Nature** : Formateur

**Responsabilités** :
- Enseignement des matières
- Évaluation et notes
- Suivi académique des élèves
- Participation aux réunions pédagogiques
- Contrôle de présence en cours

**Hiérarchie** : ⭐⭐⭐⭐ NIVEAU 4

**Pouvoirs sur les autres** :
- Aucun pouvoir de création d'utilisateurs
- Aucun pouvoir de gestion

**Permissions système** :
- Créer/modifier notes pour ses classes
- Voir présences pour ses classes
- Voir dossiers élèves de ses classes
- Consultation documents pédagogiques

**Cas spécial** :
- Un Enseignant peut AUSSI être :
  - Prof Principal (1 classe)
  - Censeur (supervision pédagogique - rare)
  - Chef d'examen (pendant examens)

---

## 6. **SURVEILLANT (Moniteur / Pion)**

**Nature** : Responsable de discipline et surveillance

**Responsabilités** :
- Surveillance générale du lycée
- Discipline des élèves
- Appel en classe
- Rapport des absences
- Supervision pendant examens
- Tâches administratives légères

**Hiérarchie** : ⭐⭐⭐⭐⭐ NIVEAU 5

**Cas spécial - SURVEILLE PENDANT EXAMENS** :
- Rôle peut être TEMPORAIRE (dates début/fin)
- Un Enseignant peut devenir "Surveillant d'examen" pendant examens
- Rôle : Enseignant + Surveillant (temporaire)
- Fin automatiquement après les examens

**Permissions système** :
- Prendre présences
- Voir liste élèves par classe
- Rapport d'absences
- Feedback sur discipline

---

## 7. **PARENT (Responsable d'élève)**

**Nature** : Externe à l'organisation

**Responsabilités** :
- Éducation de l'enfant
- Suivi des performances
- Communication avec l'école
- Paiement des frais

**Hiérarchie** : 🚫 HORS HIÉRARCHIE (Niveau 99)

**Permissions système** :
- Voir notes de SON/SES enfant(s) uniquement
- Voir présences de SON/SES enfant(s)
- Voir messages du prof principal
- Payer frais en ligne

**Limitation** :
- Aucun accès aux autres élèves
- Aucun accès aux données sensibles
- Lecture seule

---

## 8. **ÉLÈVE (Étudiant)**

**Nature** : Principal acteur

**Responsabilités** :
- Étudier et progresser
- Respect du règlement
- Participation aux cours

**Hiérarchie** : 🚫 HORS HIÉRARCHIE (Niveau 100)

**Permissions système** :
- Voir ses propres notes
- Voir ses propres présences
- Voir ses infos personnelles
- Télécharger certificats

**Limitation** :
- Lecture seule complète
- Aucune modification

---

## 9. **ADMINISTRATEUR (Admin Système)**

**Nature** : Technique - Gestion du système

**Responsabilités** :
- Configuration technique
- Gestion des données
- Backups et sécurité
- Support technique

**Hiérarchie** : ⭐ NIVEAU 0 (Hors hiérarchie, niveau technique)

**Pouvoirs** :
- Accès total au système
- Peut tout créer/modifier/supprimer
- Accès aux logs sensibles

**Note** : En petit lycée, l'Admin = Proviseur

---

## 📊 Tableau Récapitulatif

| Rôle | Hiérarchie | Niveau | Multiples | Temporaire | Nature |
|------|-----------|--------|-----------|-----------|--------|
| Admin | 0 | Technique | Non | Non | Technique |
| Proviseur | 1 | Chef | Non | Non | Chef exécutif |
| Censeur | 2 | Gestionnaire | Non | Non | Chef pédagogique |
| Prof Principal | 3 | Responsable | ✅ Oui | Non | Responsable classe |
| Chef de Classe | 3 | Représentant | Rare | Non | Leader élève |
| Enseignant | 4 | Formateur | ✅ Oui | Non | Éducateur |
| Surveillant | 5 | Discipline | ✅ Oui | ⏰ Examens | Agent discipline |
| Parent | 99 | Externe | N/A | Non | Responsable |
| Élève | 100 | Externe | N/A | Non | Apprenant |

---

## 🔄 Rôles Multiples (Cas Réels)

### Cas 1 : Enseignant qui est aussi Prof Principal
```
Utilisateur : Jean NKOMO
Rôles :
  • Enseignant (principal) - Enseigne Mathématiques
  • Prof Principal (secondaire) - Responsable classe 2nde A
```
**Permissions** :
- Notes : Pour classes de Maths ET pour classe 2nde A
- Présences : Pour classes de Maths ET classe 2nde A
- Parent contact : Pour 2nde A seulement

---

### Cas 2 : Enseignant qui devient Surveillant pendant examens
```
Utilisateur : Sophie MVONDO
Rôles :
  • Enseignant (principal) - Enseigne Français
  • Surveillant (temporaire) - Du 15/05 au 30/06/2025
```
**Permissions** :
- Pendant examens : Surveillance + notes Français
- Après examens : Rôle Surveillant auto-retiré

---

### Cas 3 : Enseignant qui est aussi Censeur (rare)
```
Utilisateur : Dr. Pierre SANDA
Rôles :
  • Enseignant (principal) - Enseigne Philosophie
  • Censeur (secondaire) - Supervision pédagogique générale
```
**Permissions** :
- Notes : Voir TOUTES les notes (pas juste Philo)
- Classes : Voir TOUTES les classes
- Absences : Gestion générale

---

## ⚡ Cas d'Exceptions Camerounaises

### 1. **Petit Lycée** (< 300 élèves)
- Proviseur = Admin du système
- Peut ne pas avoir Censeur
- Peut ne pas avoir Prof Principal

### 2. **Lycée Privé**
- Structure plus flexible
- Rôles peuvent être combinés différemment
- Propriétaire peut être Proviseur

### 3. **Lycée Technique**
- Peut avoir des rôles supplémentaires (Chef atelier, Maître d'ouvrage)
- Hors scope pour l'instant

### 4. **Lycée en Crise**
- Peut n'avoir qu'un Proviseur et des Enseignants
- Admin peut être fait par Proviseur lui-même

---

## 🎯 Permissions par Rôle - Détail Camerounais

### **PROVISEUR**
```
Configuration
├── Voir config ✅
├── Modifier config ✅
└── Gérer années scolaires ✅

Utilisateurs
├── Créer utilisateurs ✅ (sauf Admin)
├── Modifier utilisateurs ✅
├── Assigner rôles ✅
└── Voir audit logs ✅

Académique
├── Voir toutes classes ✅
├── Voir toutes notes ✅
├── Valider notes (approbation) ✅
├── Voir présences ✅
└── Gestion finances ✅
```

### **CENSEUR**
```
Utilisateurs
├── Créer utilisateurs ✅ (sauf Proviseur)
├── Modifier utilisateurs ✅
└── Assigner rôles ✅

Académique
├── Voir toutes classes ✅
├── Gérer notes ✅
├── Gérer présences ✅
├── Planning examens ✅
└── Consultation finances ✅

Discipline
├── Voir absences ✅
├── Approuver justifications ✅
└── Rapport discipline ✅
```

### **PROF PRINCIPAL**
```
Sa classe seulement
├── Voir notes élèves ✅
├── Voir présences ✅
├── Voir infos élèves ✅
├── Contacter parents ✅
└── Rapport absences ✅

Hors classe
├── Aucun accès ❌
```

### **ENSEIGNANT**
```
Ses classes seulement
├── Créer notes ✅
├── Modifier notes ✅
├── Voir présences ✅
├── Voir infos élèves ✅
└── Prendre présence ✅

Autres classes
├── Aucun accès ❌
```

### **SURVEILLANT**
```
Discipline/Présences
├── Prendre présences ✅
├── Voir absences ✅
├── Rapport comportement ✅
└── Voir élèves ✅

Académique
├── Voir notes ❌
├── Créer données ❌
└── Modifier données ❌
```

### **PARENT**
```
Son enfant seulement
├── Voir notes ✅
├── Voir présences ✅
├── Voir frais/paiements ✅
└── Recevoir messages ✅

Autres élèves
├── Aucun accès ❌

Autres parents
├── Aucun accès ❌
```

### **ÉLÈVE**
```
Ses données seulement
├── Voir ses notes ✅
├── Voir ses présences ✅
├── Voir son dossier ✅
└── Télécharger bulletins ✅

Autres élèves
├── Aucun accès ❌

Modification
├── Aucun ❌
```

---

## 💡 Conclusions pour l'Implémentation

### ✅ À Faire
1. **9 rôles** : Admin, Proviseur, Censeur, Prof Principal, Chef Classe, Enseignant, Surveillant, Parent, Élève
2. **Hiérarchie stricte** : Pour contrôler qui crée qui
3. **Rôles multiples** : Un utilisateur = plusieurs rôles
4. **Rôles temporaires** : Surveillant pendant examens
5. **Permissions granulaires** : Par classe, par matière, par enfant
6. **Audit complet** : Qui a fait quoi

### ❌ À Éviter
- Admin et Proviseur mixés
- Permissions globales (doit être scopé)
- Rôles mixtes sans hiérarchie (Enseignant + Proviseur = non)
- Pas de limitation pour Parent (doit voir QUE ses enfants)

---

## 🚀 Prochains Pas

1. Créer les modèles Role et Permission avec ces rôles
2. Implémenter la hiérarchie dans UserManagementService
3. Créer les Policies (rôle-based access)
4. Tester chaque rôle avec ses permissions
5. Implémenter les rôles temporaires

Prêt à coder ? 🎓
