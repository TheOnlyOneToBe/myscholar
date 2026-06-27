# MyScholar Module Dependencies Guide

## Overview

MyScholar uses 10 modules with a clear dependency graph. When you select a module for installation, all required dependencies are automatically included with clear warnings.

---

## Module Dependency Map

### Core Modules (No Dependencies)
These modules can be installed independently:

```
Config
├── No dependencies
└── Required by: All other modules

Auth
├── No dependencies
└── Required by: All other modules

Audit
├── No dependencies
└── Required by: Nothing (optional)

Notifications
├── No dependencies
└── Required by: Nothing (optional)

Reporting
├── No dependencies
└── Required by: Nothing (optional)
```

### Business Modules (Have Dependencies)

```
Students
├── Requires: [Config, Auth]
├── Required by: [Classes, Grades, Attendance, Billing]
└── Tables: students, student_contacts, student_enrollments, family_contacts, student_history

Classes
├── Requires: [Config, Auth]
├── Required by: [Grades, Attendance]
└── Tables: classes, class_assignments, class_subjects, rooms, timetables

Grades
├── Requires: [Config, Auth, Students, Classes]
├── Required by: Nothing
└── Tables: subjects, grade_periods, grades, averages_cache, class_averages, appeals

Attendance
├── Requires: [Config, Auth, Students, Classes]
├── Required by: Nothing
└── Tables: attendance_sessions, attendance_records, justifications, absence_counters, absence_alerts

Billing
├── Requires: [Config, Auth, Students]
├── Required by: Nothing
└── Tables: fee_structures, invoices, payments, installments, payment_plans, scholarships, fee_waivers
```

---

## Dependency Decision Tree

### If you select: **Config or Auth**
```
✓ Installs: Config, Auth
✓ Total: 2 modules
⚠️ Warning: None (core modules)
```

### If you select: **Audit, Notifications, or Reporting**
```
✓ Installs: Config, Auth, [selected]
✓ Total: 3 modules
⚠️ Warning: None (no dependencies)
```

### If you select: **Students**
```
Requested: Students
Auto-added: (none - Config & Auth always included)
✓ Installs: Config, Auth, Students
✓ Total: 3 modules
✓ Permissions: 7 (config 3 + auth 1 + students 4)
```

### If you select: **Classes**
```
Requested: Classes
Auto-added: (none - Config & Auth always included)
✓ Installs: Config, Auth, Classes
✓ Total: 3 modules
✓ Permissions: 8 (config 3 + auth 1 + classes 4)
```

### If you select: **Students, Classes**
```
Requested: Students, Classes
Auto-added: (none)
✓ Installs: Config, Auth, Students, Classes
✓ Total: 4 modules
✓ Permissions: 11 (config 3 + auth 1 + students 4 + classes 4)
```

### If you select: **Grades** ⚠️
```
Requested: Grades

⚠️  DEPENDENCY WARNING
The following modules were automatically added because they are required:
  • Students (required by: Grades)
  • Classes (required by: Grades)

✓ Final: Config, Auth, Students, Classes, Grades
✓ Total: 5 modules
✓ Permissions: 15 (config 3 + auth 1 + students 4 + classes 4 + grades 4)
```

### If you select: **Attendance** ⚠️
```
Requested: Attendance

⚠️  DEPENDENCY WARNING
The following modules were automatically added because they are required:
  • Students (required by: Attendance)
  • Classes (required by: Attendance)

✓ Final: Config, Auth, Students, Classes, Attendance
✓ Total: 5 modules
✓ Permissions: 14 (config 3 + auth 1 + students 4 + classes 4 + attendance 3)
```

### If you select: **Grades, Attendance** ⚠️
```
Requested: Grades, Attendance

⚠️  DEPENDENCY WARNING
The following modules were automatically added because they are required:
  • Students (required by: Grades, Attendance)
  • Classes (required by: Grades, Attendance)

✓ Final: Config, Auth, Students, Classes, Grades, Attendance
✓ Total: 6 modules
✓ Permissions: 18
```

### If you select: **Billing** ⚠️
```
Requested: Billing

⚠️  DEPENDENCY WARNING
The following modules were automatically added because they are required:
  • Students (required by: Billing)

✓ Final: Config, Auth, Students, Billing
✓ Total: 4 modules
✓ Permissions: 11 (config 3 + auth 1 + students 4 + billing 3)
```

### If you select: **All** (--all flag)
```
Requested: All 10 modules

No warnings (all dependencies already included)

✓ Final: Config, Auth, Audit, Notifications, Reporting, Students, Classes, Grades, Attendance, Billing
✓ Total: 10 modules
✓ Permissions: 27 (all)
```

---

## Real-World Installation Scenarios

### Scenario 1: Small Private School (Academics Only)
```bash
php artisan client:initialize --modules=Students,Classes,Grades
```

**Auto-inclusion:**
```
Requested: Students, Classes, Grades
Auto-added: (none)
Final: Config, Auth, Students, Classes, Grades
Total modules: 5
Permissions created: 15
```

**Why this works:**
- Students and Classes don't have additional dependencies
- Grades needs Students and Classes (already selected)
- Config and Auth always included

---

### Scenario 2: Billing-Focused School
```bash
php artisan client:initialize --modules=Billing
```

**Auto-inclusion:**
```
Requested: Billing

⚠️  DEPENDENCY WARNING
  • Students (required by: Billing)

Final: Config, Auth, Students, Billing
Total modules: 4
Permissions created: 11
```

**Why Students is added:**
- Billing needs to track which student owes what
- Without Students module, billing has no meaning

---

### Scenario 3: Attendance-Heavy System
```bash
php artisan client:initialize --modules=Attendance
```

**Auto-inclusion:**
```
Requested: Attendance

⚠️  DEPENDENCY WARNING
  • Students (required by: Attendance)
  • Classes (required by: Attendance)

Final: Config, Auth, Students, Classes, Attendance
Total modules: 5
Permissions created: 14
```

**Why Students and Classes are added:**
- Attendance tracks which student attended which class
- Both are essential for attendance functionality

---

### Scenario 4: Audit-Only Installation
```bash
php artisan client:initialize --modules=Audit
```

**Auto-inclusion:**
```
Requested: Audit

No warnings (Audit has no dependencies)

Final: Config, Auth, Audit
Total modules: 3
Permissions created: 4
```

**Why no warnings:**
- Audit module is standalone
- Works without other business modules
- Just logs all system activities

---

### Scenario 5: Full Installation
```bash
php artisan client:initialize --all
```

**Auto-inclusion:**
```
No warnings (all modules requested)

Final: Config, Auth, Audit, Notifications, Reporting, Students, Classes, Grades, Attendance, Billing
Total modules: 10
Permissions created: 27
```

**Everything available:**
- Full feature set for comprehensive school management
- All bridges and relationships active
- Maximum functionality and reporting capabilities

---

## Dependency Chain Examples

### Longest Dependency Chain
```
Grades
  ↓ (requires Students)
  ↓ (requires Classes)
  ↓ (requires Config, Auth)
Final: Config, Auth, Students, Classes, Grades (5 modules)
```

### Complex Multi-Dependency
```
Grades + Attendance
  ↓ (both require Students)
  ↓ (both require Classes)
  ↓ (all require Config, Auth)
Final: Config, Auth, Students, Classes, Grades, Attendance (6 modules)
```

### Simple Dependency
```
Students
  ↓ (requires Config, Auth)
Final: Config, Auth, Students (3 modules)
```

---

## Permission Count by Module Combination

| Selection | Modules | Permissions | Notes |
|-----------|---------|-------------|-------|
| Config, Auth | 2 | 4 | Minimal - config only |
| + Students | 3 | 8 | Basic student management |
| + Classes | 4 | 11 | Classes + Students management |
| + Grades | 5 (auto +Students, Classes) | 15 | Academic grading |
| + Attendance | 5 (auto +Students, Classes) | 14 | Attendance tracking |
| + Billing | 4 (auto +Students) | 11 | Financial management |
| All 10 modules | 10 | 27 | Complete system |

---

## Commands and Their Effects

### Command 1: Install All
```bash
php artisan client:initialize --all
```
- ✓ No warnings
- ✓ 10 modules installed
- ✓ 27 permissions created
- ✓ All bridges active

### Command 2: Install Specific with Auto-Inclusion
```bash
php artisan client:initialize --modules=Grades,Billing
```
- ⚠️ Warnings show Students, Classes auto-added
- ✓ 6 modules installed (Config, Auth, Students, Classes, Grades, Billing)
- ✓ 18 permissions created
- ✓ Only relevant bridges active

### Command 3: Interactive with Dependency Info
```bash
php artisan client:initialize
```
Shows:
```
📌 Core modules (required):
  ✓ Config
  ✓ Auth

📦 Optional modules (dependencies shown):
  • Audit (no extra dependencies)
  • Notifications (no extra dependencies)
  • Reporting (no extra dependencies)
  • Students (requires: Config, Auth)
  • Classes (requires: Config, Auth)
  • Grades (requires: Config, Auth, Students, Classes)
  • Attendance (requires: Config, Auth, Students, Classes)
  • Billing (requires: Config, Auth, Students)
```

---

## Troubleshooting Module Dependencies

### Issue: Module not installing even though selected

**Check:** Does the selected module have unmet dependencies?

```bash
# Example: Selected Grades but Students not available
php artisan client:initialize --modules=Grades
# Result: Students is auto-added with warning
```

**Solution:** The system automatically includes dependencies. Check warnings for what was added.

### Issue: Too many modules installed

**Check:** Did dependencies auto-add modules you didn't expect?

```bash
# Example: Only wanted Attendance
php artisan client:initialize --modules=Attendance
# Result: Students, Classes auto-added (required by Attendance)
```

**Solution:** If you only want specific modules, start with Config + Auth only, then add independent modules (Audit, Notifications, Reporting).

### Issue: Permissions not appearing for module

**Check:** Was the module's dependencies met?

```bash
# Example: Grades selected but Classes not included
# Result: Grades permissions won't be created
```

**Solution:** Ensure all dependencies are in final modules. Check warnings during initialization.

---

## Best Practices

✅ **DO:**
- Read the dependency warnings carefully
- Start with core modules only if unsure
- Use `--all` for full-featured installations
- Check `config/modules.json` after initialization

❌ **DON'T:**
- Try to manually edit `config/modules.json` (re-run command instead)
- Expect modules to work without their dependencies
- Ignore dependency warnings
- Try to install Grades without Students and Classes

---

## Quick Reference Table

| Module | Dependencies | Can Use Without | Best With |
|--------|--------------|-----------------|-----------|
| **Config** | None | Any | All modules |
| **Auth** | None | Any | All modules |
| **Audit** | None | Anything | Any module |
| **Notifications** | None | Anything | All modules |
| **Reporting** | None | Anything | Other modules |
| **Students** | Config, Auth | Classes, Grades, Attendance | Billing |
| **Classes** | Config, Auth | Grades, Attendance | Students |
| **Grades** | Config, Auth, Students, Classes | Nothing (requires both) | Attendance |
| **Attendance** | Config, Auth, Students, Classes | Nothing (requires both) | Grades |
| **Billing** | Config, Auth, Students | Nothing (requires Students) | Grades |

---

## Summary

✅ **Automatic handling:** All dependencies are automatically included
✅ **Clear warnings:** System warns you which modules were auto-added and why
✅ **Interactive help:** Shows dependencies when selecting modules
✅ **Validation:** Prevents invalid combinations
✅ **Configurable:** Choose all modules or only what you need

**You never have to manually manage dependencies—the system does it for you!**
