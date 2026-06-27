# Module Structure and Migration Guide

## Overview

MyScholar uses a modular architecture where each module is independent and can be installed selectively. School year (Config module) is a core concern that connects to all business modules through bridge migrations.

## Module Organization

```
modules/
├── Auth/              (100xxx)    [CORE] Authentication, users, roles, permissions
├── Audit/             (200xxx)    [CORE] Audit logs, deleted records
├── Notifications/     (300xxx)    [CORE] Email, SMS, notifications
├── Students/          (400xxx)    Students, contacts, enrollments, history
├── Grades/            (500xxx)    Subjects, grades, periods, appeals
├── Attendance/        (600xxx)    Attendance sessions, records, justifications
├── Classes/           (700xxx)    Classes, assignments, subjects, timetables
├── Reporting/         (none)      [CORE] Reporting and analytics (no tables)
├── Config/            (core)      [CORE] School info, settings, school years
└── Billing/           (950xxx)    Fee structures, invoices, payments, scholarships
```

## Migration Numbering Scheme

Each module has a unique number range for its migrations:

| Range | Module | Type | Purpose |
|-------|--------|------|---------|
| 100xxx | Auth | Module | Core authentication tables |
| 200xxx | Audit | Module | Audit and tracking tables |
| 300xxx | Notifications | Module | Notification and template tables |
| 400xxx | Students | Module | Student information and tracking |
| 500xxx | Grades | Module | Academic grades and assessment |
| 600xxx | Attendance | Module | Attendance and absence tracking |
| 700xxx | Classes | Module | Class organization and scheduling |
| 800xxx | Bridges | Bridge | Cross-module relationships (Config) |
| 900xxx | Bridges | Bridge | Cross-module relationships (Business) |
| 950xxx | Billing | Module | Financial and billing records |

## Bridge Migrations (800xxx-900xxx)

Bridges connect two modules and should be placed in the `bridges/` folder:

### Config Bridges (800501-800505)
These are core bridges that link Config (school year) to other modules:

```
800501: Config ↔ Classes     (school_years → classes, assignments, subjects, timetables)
800502: Config ↔ Students    (school_years → enrollments, history)
800503: Config ↔ Grades      (school_years → periods, grades, averages, appeals)
800504: Config ↔ Attendance  (school_years → sessions, records, justifications, counters)
800505: Config ↔ Billing     (school_years → structures, invoices, payments, scholarships)
```

### Business Bridges (900001-900006)
These connect business modules to each other:

```
900001: Students ↔ Grades         (grades.student_id → students.id)
900002: Students ↔ Attendance     (attendance.student_id → students.id)
900003: Students ↔ Classes        (enrollments.class_id → classes.id)
900004: Students ↔ Billing        (invoices/scholarships.student_id → students.id)
900005: Classes ↔ Grades          (grades.class_id/subject_id → classes/subjects)
900006: Classes ↔ Attendance      (sessions.class_id → classes.id)
```

## Migration Execution Order

Migrations execute in timestamp order. The numbering ensures correct dependency ordering:

1. **Phase 1 (1xxxxx)**: Core module tables (Auth, Audit, Notifications)
2. **Phase 2 (3xxxxx-4xxxxx)**: Core module + Student module tables
3. **Phase 3 (5xxxxx-7xxxxx)**: Business module tables (Grades, Attendance, Classes)
4. **Phase 4 (8xxxxx)**: Config bridges linking school years
5. **Phase 5 (9xxxxx)**: Business module bridges
6. **Phase 6 (95xxxx)**: Billing module tables

## Module Structure per Module

```
Module/
├── migrations/          # Module-only table migrations (XXXXX prefix)
├── Models/              # Eloquent models
├── Controllers/         # API controllers
├── Requests/            # Form request validation
├── Services/            # Business logic
├── Enums/               # BackedEnums for type-safe values
├── ValueObjects/        # Domain value objects (optional)
├── Providers/           # Service providers
├── translations/        # i18n files (FR/EN)
├── Routes/
│   └── api.php          # API route definitions
└── Traits/              # Reusable logic traits
```

## Dependencies and Installation

### Core Modules (always required)
- **Auth**: No dependencies
- **Config**: No dependencies
- **Audit**: No dependencies
- **Notifications**: No dependencies
- **Reporting**: No dependencies

### Business Modules (optional, require Config)
- **Students**: Requires Config only. Optionally uses Grades, Classes, Attendance, Billing
- **Classes**: Requires Config. Optionally uses Students, Grades, Attendance
- **Grades**: Requires Config. Optionally uses Students, Classes
- **Attendance**: Requires Config. Optionally uses Students, Classes
- **Billing**: Requires Config. Optionally uses Students

### Module Installation Example

To install MyScholar with Grades and Attendance:
```bash
php artisan modules:install "config,auth,students,classes,grades,attendance"
```

This will:
1. Create Config tables
2. Create Auth tables
3. Create Student tables
4. Create Classes tables
5. Create Grades tables
6. Create Attendance tables
7. Execute Config bridges (800501-800505)
8. Execute Business bridges (900001-900006)

## School Year as Core Concern

School year is a cross-cutting concern that spans all business modules:

- **Stored in**: `Config/Models/SchoolYear.php`
- **Access**: `currentSchoolYear()` helper or `SchoolYearSessionService`
- **Filtering**: Use `.sessionYear()` scope on models
- **Protection**: `ProtectsPastSchoolYearData` trait prevents modifications without `scholarity.modify_past_years` permission

All tables that are school-year dependent have:
- `school_year_id` foreign key (nullable for historical data)
- Index on `school_year_id`
- Cascade delete on `school_year_id`

## Creating a New Module

1. Create folder: `modules/YourModule/`
2. Create subfolders: migrations, Models, Controllers, Services, Routes, translations
3. Create migration with appropriate number range
4. If module depends on another module, create bridge in `bridges/`
5. Create `module.json` manifest
6. Create `Providers/YourModuleServiceProvider.php`
7. Add routes in `Routes/api.php`
8. Add translations in `translations/{fr,en}/`

## Creating a New Bridge

1. Create file in `bridges/`: `2024_01_01_XXXXX_descriptive_name.php`
2. Use appropriate range:
   - 800501-800505: Config bridges
   - 900001-900999: Business bridges
3. Add class docblock documenting dependencies:
   ```php
   /**
    * Bridge: Module1 ↔ Module2
    * Description of what gets linked
    * Dependencies: Module1, Module2
    */
   ```
4. Use defensive checks in migrations
5. Document in `bridges/BRIDGES.md`

## Migration Best Practices

### Do
- ✅ Use unique timestamps for migrations
- ✅ Add defensive checks: `if (!Schema::hasColumn(...))`
- ✅ Add indices on foreign keys and frequently queried columns
- ✅ Add meaningful comments explaining relationships
- ✅ Use cascade deletes for related records
- ✅ Document dependencies in docblocks

### Don't
- ❌ Modify tables from other modules in your module's migrations
- ❌ Create duplicate bridges for the same pair of modules
- ❌ Use conflicting migration numbers
- ❌ Forget to add indices on foreign keys
- ❌ Use the same table/column names across different modules

## Current Schema Summary

**Tables by Module:**

| Module | Tables | Count |
|--------|--------|-------|
| Auth | users, roles, permissions, role_permissions, user_roles | 5 |
| Audit | audit_logs, deleted_records | 2 |
| Notifications | notifications, notification_preferences, email_templates, sms_templates | 4 |
| Students | students, student_contacts, student_enrollments, student_history, family_contacts | 5 |
| Grades | subjects, grade_periods, grades, averages_cache, class_averages, appeals | 6 |
| Attendance | attendance_sessions, attendance_records, justifications, absence_counters, absence_alerts | 5 |
| Classes | classes, class_assignments, class_subjects, rooms, timetables | 5 |
| Config | school_info, system_settings, school_years | 3 |
| Billing | fee_structures, invoices, payments, installments, payment_plans, scholarships, fee_waivers | 7 |

**Total: 42 tables**

All business tables (except Students and Classes base tables) include `school_year_id` for multi-year data isolation.
