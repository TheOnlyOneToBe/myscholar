# Module Bridges and Dependencies

**Last Updated**: 2026-06-28
**Total Bridges**: 11
**Bridge Types**: 2 (Configuration Bridges: 5, Business Module Bridges: 6)

## Overview
Bridges connect different modules and establish relationships between their tables. Each bridge migration:
- Uses schema-safe operations with table/column existence checks
- Is numbered with explicit dependency ordering
- Can safely be run even if dependent modules aren't installed
- Modifies existing tables rather than creating new ones

## Safety Features
✅ All bridges use `Schema::hasTable()` checks
✅ All bridges use `Schema::hasColumn()` checks
✅ No bridge will fail if dependent module isn't installed
✅ Foreign keys use `nullable()` for flexibility
✅ Bridges can be run in any order (checks handle dependencies)

## Bridge Dependencies Map

### Core Connections (Config Module)
The Config module (core) establishes school year as a cross-cutting concern. All business modules depend on Config.

| Bridge | Dependencies | Purpose |
|--------|--------------|---------|
| `800501_config_link_classes` | Config → Classes | Links school years to classes, assignments, subjects, timetables |
| `800502_config_link_students` | Config → Students | Links school years to enrollments and student history |
| `800503_config_link_grades` | Config → Grades | Links school years to grade periods, grades, averages, appeals |
| `800504_config_link_attendance` | Config → Attendance | Links school years to sessions, records, justifications, absence tracking |
| `800505_config_link_billing` | Config → Billing | Links school years to fee structures, invoices, payments, scholarships |

### Business Module Connections

| Bridge | Dependencies | Purpose |
|--------|--------------|---------|
| `900001_link_students_grades` | Students → Grades | Foreign key from grades to students |
| `900002_link_students_attendance` | Students → Attendance | Foreign keys from attendance to students |
| `900003_link_students_classes` | Students → Classes | Foreign key from enrollments to classes |
| `900004_link_students_billing` | Students → Billing | Foreign keys from billing to students |
| `900005_link_classes_grades` | Classes → Grades | Foreign key from grades to class/subject |
| `900006_link_classes_attendance` | Classes → Attendance | Foreign key from attendance sessions to classes |

## Execution Order

Bridges must execute in dependency order:

1. **Config Module** (must be installed first)
   - Creates base tables: school_info, system_settings, school_years

2. **Core Module Bridges** (only if respective modules installed)
   - 800501: Classes + Config
   - 800502: Students + Config
   - 800503: Grades + Config
   - 800504: Attendance + Config
   - 800505: Billing + Config

3. **Business Module Bridges** (only if both modules installed)
   - 900001: Students + Grades
   - 900002: Students + Attendance
   - 900003: Students + Classes
   - 900004: Students + Billing
   - 900005: Classes + Grades
   - 900006: Classes + Attendance

## Module Independence

Each module can be installed independently:

- **Config (core)**: Always required. No dependencies.
- **Auth (core)**: Always required. No dependencies.
- **Students**: Requires Config. Optional: Grades, Classes, Attendance, Billing
- **Classes**: Requires Config. Optional: Students, Grades, Attendance
- **Grades**: Requires Config. Optional: Students, Classes
- **Attendance**: Requires Config. Optional: Students, Classes
- **Billing**: Requires Config. Optional: Students
- **Audit (core)**: No dependencies.
- **Notifications (core)**: No dependencies.
- **Reporting (core)**: Optional. Works better with other modules but not required.

## Adding a New Bridge

1. Create file: `2024_01_01_XXXXX_descriptive_name.php`
2. Document dependencies in the class docblock
3. Document in this BRIDGES.md file
4. Ensure unique timestamps for ordering
5. Add defensive checks: `if (!Schema::hasTable(...))` and `if (!Schema::hasColumn(...))`
