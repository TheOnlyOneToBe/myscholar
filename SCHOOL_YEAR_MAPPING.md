# Cartographie Complète des Liaisons SchoolYear

## Analyse des Liaisons par Module

### ✅ TABLES QUI DOIVENT AVOIR school_year_id

#### **Students Module**
- ✅ `student_enrollments` - Inscription d'un étudiant pour une année
- ✅ `student_history` - Historique des élèves par année

#### **Classes Module**
- ✅ `classes` - Classe créée pour une année scolaire spécifique
- ✅ `class_assignments` - Assignation d'étudiant à classe (par année)
- ✅ `class_subjects` - Sujets enseignés dans une classe (par année)
- ✅ `timetables` - Horaires d'une classe (par année)

#### **Grades Module**
- ✅ `subjects` - **NON** (sujets permanents, mais relation via Grade)
- ✅ `grade_periods` - Périodes de notation (trimestre/année/semestre)
- ✅ `grades` - Notes des élèves (par année)
- ✅ `averages_cache` - Moyennes mises en cache (par année)
- ✅ `class_averages` - Moyennes de classe (par année)
- ✅ `appeals` - Appels de notes (par année)

#### **Attendance Module**
- ✅ `attendance_sessions` - Sessions d'appel (par année)
- ✅ `attendance_records` - Enregistrements d'appel (par année)
- ✅ `justifications` - Justifications d'absence (par année)
- ✅ `absence_counters` - Compteurs d'absence (par année)
- ✅ `absence_alerts` - Alertes d'absence (par année)

#### **Billing Module**
- ✅ `fee_structures` - Structures de frais (par année)
- ✅ `invoices` - Factures (par année)
- ✅ `payments` - Paiements (par année)
- ✅ `payment_plans` - Plans de paiement (par année)
- ✅ `installments` - Versements (par année)
- ✅ `scholarships` - Bourses (par année)
- ✅ `fee_waivers` - Exonérations de frais (par année)

### ❌ TABLES QUI NE DOIVENT PAS AVOIR school_year_id

#### **Config Module (Core)**
- `school_info` - Informations du lycée (permanentes, une seule par lycée)
- `system_settings` - Paramètres système (permanents)

#### **Auth Module (Core)**
- `users` - Utilisateurs (permanents, pas liés à une année)
- `roles` - Rôles (permanents)
- `permissions` - Permissions (permanentes)
- `role_permissions` - Relations permanentes
- `user_roles` - Relations permanentes

#### **Classes Module**
- `rooms` - Salles (ressource permanente)

#### **Grades Module**
- `subjects` - Sujets (permanents, mais créent des grades liés à années)

#### **Audit Module (Core)**
- `audit_logs` - Logs d'audit (transcendent les années)
- `deleted_records` - Enregistrements supprimés (transcendent les années)

#### **Notifications Module (Core)**
- `notifications` - Notifications (transcendent les années)
- `notification_preferences` - Préférences (permanentes)
- `email_templates` - Templates (permanents)
- `sms_templates` - Templates (permanents)

## Bridges Nécessaires

### Bridge 1: Config → Students (SchoolYear)
```
school_years (Config) ←→ student_enrollments (Students)
Relation: 1 SchoolYear : N StudentEnrollments
```

### Bridge 2: Config → Classes (SchoolYear)
```
school_years (Config) ←→ classes (Classes)
Relation: 1 SchoolYear : N Classes
```

### Bridge 3: Classes → Grades (SchoolYear & Subject)
```
classes (Classes) ←→ grades (Grades)
school_years (Config) ←→ grades (Grades)
Relation: Multiple - une classe peut avoir plusieurs sujets et plusieurs années
```

### Bridge 4: Classes → Attendance (SchoolYear)
```
classes (Classes) ←→ attendance_sessions (Attendance)
school_years (Config) ←→ attendance_sessions (Attendance)
Relation: 1 Class : N AttendanceSessions (per year)
```

### Bridge 5: Classes → Billing (SchoolYear)
```
classes (Classes) ←→ fee_structures (Billing)
school_years (Config) ←→ fee_structures (Billing)
Relation: 1 Class : N FeeStructures (per year)
```

### Bridge 6: Students → Billing (via StudentEnrollment → SchoolYear)
```
students (Students) ←→ student_enrollments (Students)
student_enrollments (Students) ←→ invoices (Billing) [via student + year]
school_years (Config) ←→ invoices (Billing)
Relation: 1 Student : N Enrollments : N Invoices (per year/enrollment)
```

## Dépendances de Modules

```
Config (Core)
    ↓
    ├──→ Auth (Core) [independent, no school_year needed]
    ├──→ Students
    │    ↓
    │    ├──→ Classes
    │    │    ↓
    │    │    ├──→ Grades
    │    │    ├──→ Attendance
    │    │    └──→ Billing
    │    │
    │    └──→ Billing
    │
    ├──→ Audit (Core) [cross-cutting]
    └──→ Notifications (Core) [cross-cutting]
```

## Détail des Colonnes à Ajouter

### Students Module
```sql
ALTER TABLE student_enrollments ADD school_year_id BIGINT;
ALTER TABLE student_history ADD school_year_id BIGINT;
```

### Classes Module
```sql
ALTER TABLE classes ADD school_year_id BIGINT;
ALTER TABLE class_assignments ADD school_year_id BIGINT;
ALTER TABLE class_subjects ADD school_year_id BIGINT;
ALTER TABLE timetables ADD school_year_id BIGINT;
```

### Grades Module
```sql
ALTER TABLE grade_periods ADD school_year_id BIGINT;
ALTER TABLE grades ADD school_year_id BIGINT;
ALTER TABLE averages_cache ADD school_year_id BIGINT;
ALTER TABLE class_averages ADD school_year_id BIGINT;
ALTER TABLE appeals ADD school_year_id BIGINT;
```

### Attendance Module
```sql
ALTER TABLE attendance_sessions ADD school_year_id BIGINT;
ALTER TABLE attendance_records ADD school_year_id BIGINT;
ALTER TABLE justifications ADD school_year_id BIGINT;
ALTER TABLE absence_counters ADD school_year_id BIGINT;
ALTER TABLE absence_alerts ADD school_year_id BIGINT;
```

### Billing Module
```sql
ALTER TABLE fee_structures ADD school_year_id BIGINT;
ALTER TABLE invoices ADD school_year_id BIGINT;
ALTER TABLE payments ADD school_year_id BIGINT;
ALTER TABLE payment_plans ADD school_year_id BIGINT;
ALTER TABLE installments ADD school_year_id BIGINT;
ALTER TABLE scholarships ADD school_year_id BIGINT;
ALTER TABLE fee_waivers ADD school_year_id BIGINT;
```

## Migrations Nécessaires

```
Priority 1 (Core Bridges):
├── 900001_add_school_year_to_classes.php (Config → Classes)
├── 900002_add_school_year_to_student_enrollments.php (Config → Students)
└── 900003_add_school_year_to_grade_periods.php (Config → Grades)

Priority 2 (Secondary Relationships):
├── 900004_add_school_year_to_attendance_sessions.php
├── 900005_add_school_year_to_fee_structures.php
├── 900006_add_school_year_to_grades.php
└── 900007_add_school_year_to_payment_plans.php

Priority 3 (Complete Coverage):
├── 900008_add_school_year_to_remaining_tables.php
├── 900009_create_school_year_indices.php
└── 900010_add_school_year_constraints.php
```

## Contraintes d'Intégrité

```sql
-- Chaque table avec school_year_id doit avoir:
ALTER TABLE table_name ADD CONSTRAINT fk_school_year
    FOREIGN KEY (school_year_id) 
    REFERENCES school_years(id) 
    ON DELETE CASCADE;

-- Index pour performance
CREATE INDEX idx_table_school_year ON table_name(school_year_id);
```

## Scopes Nécessaires pour chaque Modèle

```php
// Tous les modèles avec school_year_id doivent avoir:

// Filtrager par année active
scope currentYear(query) → where school_year_id = getCurrentYear()

// Filtrer par année spécifique
scope forSchoolYear(query, year) → where school_year_id = year->id

// Tous les enregistrements
scope allYears(query) → [no filter]

// Historique
scope history(query) → where school_year_id != getCurrentYear()
```

## Vérification de Complétude

### Tables à 25 total:
- [ ] Classes: 5 tables
- [ ] Students: 2 tables
- [ ] Grades: 5 tables
- [ ] Attendance: 5 tables
- [ ] Billing: 7 tables

### Migrations à 3 batches:
- [ ] Batch 1: Classes, StudentEnrollments, GradePeriods
- [ ] Batch 2: Attendance, FeeStructures, Grades, PaymentPlans
- [ ] Batch 3: Autres tables + indices + contraintes

### Documentation à mettre à jour:
- [ ] SCHOOL_YEAR_GUIDE.md - Énumérer toutes les tables liées
- [ ] MODULE_INTEGRATION_STATUS.md - Ajouter SchoolYear comme core
- [ ] Chaque module doit avoir sa section SchoolYear

## Résumé

**Total tables avec school_year_id: 25**
**Bridges nécessaires: 6**
**Migrations prioritaires: 10**
**État: À implémenter complètement**

Le SchoolYear doit être traité comme une **dépendance core** affectant presque tous les modules métier.
