# MyScholar - Modules Structure & Verification Guide

## 📊 Complete Module Status (Last Updated: 2026-06-28)

### Core Modules (Always Required)

| Module | Status | Migrations | ServiceProvider | Controllers | Models | Type |
|--------|--------|-----------|-----------------|-------------|--------|------|
| **Auth** | ✅ Complete | 11 | ✓ | 4 | 7 | Authentication/RBAC |
| **Config** | ✅ Complete | 10 | ✓ | 3 | 3 | Configuration |
| **Audit** | ✅ Complete | 2 | ✓ | 1 | 2 | Logging |
| **Notifications** | ✅ Complete | 5 | ✓ | 2 | 5 | Communication |
| **Reporting** | ✅ Complete | 0* | ✓ | 1 | 0* | Analytics |

*Reporting: No own tables (aggregates from other modules)

### Business Modules (Conditional Installation)

| Module | Status | Migrations | ServiceProvider | Controllers | Models | Type |
|--------|--------|-----------|-----------------|-------------|--------|------|
| **Students** | ✅ Complete | 8 | ✓ | 3 | 6 | Core Business |
| **Classes** | ✅ Complete | 5 | ✓ | 3 | 6 | Core Business |
| **Grades** | ✅ Complete | 6 | ✓ | 3 | 7 | Academic |
| **Attendance** | ✅ Complete | 6 | ✓ | 6 | 6 | Academic |
| **Billing** | ✅ Complete | 7 | ✓ | 4 | 7 | Financial |

### UI/Dashboard Modules

| Module | Status | Migrations | ServiceProvider | Controllers | Type |
|--------|--------|-----------|-----------------|-------------|------|
| **Dashboard** | ✅ Complete | 0 | ✓ | 0 | Admin Dashboard |

---

## 🔗 Bridge Migrations (11 Total)

### Configuration Bridges (Config Module Links) - 5

| Bridge # | File | Links | Dependencies | Status |
|----------|------|-------|--------------|--------|
| 800501 | `2024_01_01_800501_config_link_classes.php` | Config → Classes | Classes module required | ✅ |
| 800502 | `2024_01_01_800502_config_link_students.php` | Config → Students | Students module required | ✅ |
| 800503 | `2024_01_01_800503_config_link_grades.php` | Config → Grades | Grades module required | ✅ |
| 800504 | `2024_01_01_800504_config_link_attendance.php` | Config → Attendance | Attendance module required | ✅ |
| 800505 | `2024_01_01_800505_config_link_billing.php` | Config → Billing | Billing module required | ✅ |

### Business Module Bridges - 6

| Bridge # | File | Links | Dependencies | Status |
|----------|------|-------|--------------|--------|
| 900001 | `2024_01_01_900001_link_students_grades.php` | Students → Grades | Both modules required | ✅ |
| 900002 | `2024_01_01_900002_link_students_attendance.php` | Students → Attendance | Both modules required | ✅ |
| 900003 | `2024_01_01_900003_link_students_classes.php` | Students → Classes | Both modules required | ✅ |
| 900004 | `2024_01_01_900004_link_students_billing.php` | Students → Billing | Both modules required | ✅ |
| 900005 | `2024_01_01_900005_link_classes_grades.php` | Classes → Grades | Both modules required | ✅ |
| 900006 | `2024_01_01_900006_link_classes_attendance.php` | Classes → Attendance | Both modules required | ✅ |

---

## 📋 Migration Details

### Total Statistics
- **Total Migrations**: 60
- **Total Bridges**: 11
- **Total Database Changes**: 71
- **Total Tables Created**: 26 core + 10 bridge modifications

### Tables by Module

**Auth Module** (8 tables)
- users, roles, permissions, role_permissions, user_roles, password_histories, login_attempts, password_resets

**Config Module** (4 tables)
- school_info, system_settings, school_years, academic_periods

**Audit Module** (2 tables)
- audit_logs, deleted_records

**Notifications Module** (4 tables)
- notifications, notification_preferences, email_templates, sms_templates

**Students Module** (6 tables)
- students, student_contacts, student_enrollments, student_history, student_parents, ip_block_list

**Classes Module** (5 tables)
- classes, class_assignments, class_subjects, timetables, rooms

**Grades Module** (6 tables)
- subjects, grades, averages_cache, class_averages, grade_periods, grade_appeals

**Attendance Module** (6 tables)
- attendance_records, attendance_sessions, justifications, absence_counters, absence_alerts, ip_blocking_log

**Billing Module** (7 tables)
- fee_structures, invoices, payments, scholarships, payment_plans, payment_transactions, fee_waivers, installments

**Dashboard Module** (0 tables)
- No database tables

**Reporting Module** (0 tables)
- No database tables (aggregates data from other modules)

---

## ✅ Dependency Verification Checklist

### Before Installing Any Module, Verify:

1. **Module Configuration**
   - [ ] `module.json` exists and is valid
   - [ ] Dependencies are correctly listed
   - [ ] Version is specified
   - [ ] Routes prefix is defined

2. **Module Structure**
   - [ ] ServiceProvider exists
   - [ ] All required directories present (migrations, Models, Controllers, Routes)
   - [ ] Routes are properly registered

3. **Dependencies**
   - [ ] All required dependent modules are installed
   - [ ] Core modules (Auth, Config) are installed first
   - [ ] Bridge files exist for multi-module relationships

4. **Migrations**
   - [ ] No duplicate timestamps
   - [ ] Proper Schema::hasTable() checks
   - [ ] Foreign keys defined correctly
   - [ ] Defensive programming against missing tables

5. **API Endpoints**
   - [ ] Module is activated in config
   - [ ] Bridges are executed if needed
   - [ ] Dependencies are verified before queries

---

## 🔄 Installation Order (Recommended)

### Phase 1: Core Modules (Required)
1. **Config** - Must be first (provides school_years reference)
2. **Auth** - Required for user/role management
3. **Audit** - Required for logging
4. **Notifications** - Required for communications

### Phase 2: Business Modules (Optional but Recommended)
5. **Students** - Requires: Config, Auth
6. **Classes** - Requires: Config, Auth
7. **Grades** - Requires: Config, Auth, Students (optional), Classes (optional)
8. **Attendance** - Requires: Config, Auth, Students (optional)
9. **Billing** - Requires: Config, Auth, Students (optional)

### Phase 3: UI/Analytics
10. **Dashboard** - Requires: Auth, Config
11. **Reporting** - Requires: Any modules you want to report on

### Phase 4: Execute Bridges
- Run bridges only for installed module combinations
- Bridges automatically check for table existence
- Safe to run even if tables don't exist

---

## 🛡️ Safety Mechanisms in Place

### 1. Schema Verification
All bridges check table existence before modification:
```php
if (Schema::hasTable('grades') && Schema::hasTable('students')) {
    // Only execute if both tables exist
}
```

### 2. Column Existence Checks
All bridges check column existence before adding:
```php
if (!Schema::hasColumn('grades', 'student_id_fk')) {
    $table->unsignedBigInteger('student_id_fk')->nullable();
}
```

### 3. API Endpoint Guards
All endpoints verify:
- [ ] Module activation status
- [ ] Required dependencies
- [ ] Database table availability
- [ ] User authentication and authorization

### 4. Configuration Validation
Each module validates:
- [ ] Dependencies listed in module.json
- [ ] ServiceProvider registration
- [ ] Route loading

---

## 🔍 Module Activation Status

### How to Check Module Status:

```php
// Check if module exists
Schema::hasTable('table_name');

// Check if module is active
config('modules.installed.module_name');

// Check dependencies
$moduleDependencies = config('modules.dependencies.module_name');

// Verify bridge execution
Schema::hasColumn('table', 'foreign_key_column');
```

---

## 📝 Database Tables Reference

### Foreign Key Relationships

```
school_years (Config)
├── classes (bridge 800501)
├── student_enrollments (bridge 800502)
├── grade_periods (bridge 800503)
├── attendance_sessions (bridge 800504)
└── fee_structures (bridge 800505)

students (Students)
├── grades (bridge 900001)
├── attendance_records (bridge 900002)
├── class_assignments (bridge 900003)
└── invoices (bridge 900004)

classes (Classes)
├── class_averages (bridge 900005)
└── attendance_sessions (bridge 900006)
```

---

## ✨ Module API Endpoints

### Configuration Endpoints
- `GET /api/config/school` - Get school info
- `GET /api/config/system-settings` - Get system settings

### Authentication Endpoints
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/register` - User registration

### Student Endpoints
- `GET /api/students` - List students
- `GET /api/students/{id}` - Get student details
- `POST /api/students` - Create student

### Grade Endpoints
- `GET /api/grades` - List grades
- `POST /api/grades` - Create grade
- `GET /api/grades/{id}` - Get grade details

### Attendance Endpoints
- `GET /api/attendance/records` - List attendance records
- `POST /api/attendance/sessions` - Create session
- `POST /api/attendance/mark` - Mark attendance

### Billing Endpoints
- `GET /api/billing/invoices` - List invoices
- `POST /api/billing/payments` - Record payment
- `GET /api/billing/scholarships` - List scholarships

### Reporting Endpoints
- `GET /api/reporting/dashboard` - Dashboard analytics
- `GET /api/reporting/students/{id}/academic` - Student academic report
- `GET /api/reporting/trends` - Trend analysis
- `POST /api/reporting/export` - Export reports

---

## 🚀 Verification Commands

```bash
# Check total migrations
find modules -name "*.php" -path "*/migrations/*" | wc -l

# Check bridges
find bridges -name "2024*.php" | wc -l

# Verify module.json files
find modules -name "module.json" | xargs grep -l "name"

# Check ServiceProvider registration
grep -r "ServiceProvider" app/Providers/ModuleLoaderServiceProvider.php

# Verify routes are loaded
grep -r "loadRoutesFrom" modules/*/Providers/*.php | wc -l
```

---

## 📌 Important Notes

1. **Config Module Must Be First** - Provides school_years reference table
2. **Auth Module Must Be Present** - Required for all user operations
3. **Bridges Are Smart** - They check for table existence before executing
4. **API Should Verify Activation** - Check module status before database queries
5. **Dependencies Are Documented** - Always check module.json dependencies

---

## 🔗 Related Documents

- See `BRIDGES.md` for detailed bridge information
- See module `permissions.json` for role-based access
- See module `module.json` for module metadata
- See `app/Enums/RoleEnum.php` for role definitions
