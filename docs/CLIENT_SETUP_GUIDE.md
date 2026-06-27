# MyScholar Client Setup Guide

## Overview

This guide covers the complete setup process for deploying MyScholar to a new client (school). The setup process is automated through the `php artisan client:initialize` command.

## Prerequisites

Before running the client initialization, ensure:

1. **Database prepared**
   ```bash
   php artisan migrate
   ```

2. **Laravel environment configured**
   - `.env` file created and configured
   - Database connection working
   - APP_KEY generated

3. **System requirements met**
   - PHP 8.1+
   - SQLite or MySQL database
   - Sufficient disk space for files/uploads

## Quick Start (5 minutes)

The fastest way to set up a new client:

```bash
php artisan client:initialize
```

This interactive command will:
1. Ask for school information (name, address, contact)
2. Create 6 roles (admin, directeur, enseignant, surveillant, parent, student)
3. Create 27 permissions across all modules
4. Assign permissions to roles
5. Configure admin user
6. Initialize system settings
7. Verify school years

## Detailed Setup Steps

### Step 1: Run Initial Migration

```bash
php artisan migrate
```

This creates all database tables for installed modules.

**Output should show:**
```
Migrating: ...
...all migrations complete
```

### Step 2: Run Client Initialization

```bash
php artisan client:initialize
```

#### Interactive Prompts

The command will ask you to provide:

**School Information:**
```
School Name: [Default: My School]
  → Enter your school's official name

School Acronym (e.g., MS): [Default: MS]
  → Short abbreviation (e.g., "LBY" for Lycée Bawock)

School Motto (optional): []
  → School's motto/motto if any

School Type:
  [0] public
  [1] prive        ← Select for private schools
  [2] confessionnel ← Select for religious schools

Street Address: [Default: 123 Main Street]
  → Full street address

City: [Default: Douala]
  → City where school is located

Region/Province: [Default: Littoral]
  → Administrative region

Contact Phone: [Default: +237612345678]
  → School's phone number (with country code)

Contact Email: [Default: contact@myschool.edu]
  → Official school email

Website (optional): []
  → School website URL if available

P.O. Box (optional): []
  → Postal box if applicable

Approval/License Number (optional): []
  → Government approval number

Creation Decree (optional): []
  → Official decree number

Founder Name (optional): []
  → Name of school founder

Director Name (optional): []
  → Current director's name

Foundation Year (optional): []
  → Year school was founded
```

### Step 3: Verify Setup

After initialization completes, verify everything was set up correctly:

```bash
php artisan tinker
```

Then check:

```php
# Check school information
$school = SchoolInfo::first();
echo $school->name; // Should show your school name

# Check roles created
Role::all()->pluck('name'); // Should show 6 roles

# Check permissions created
Permission::count(); // Should show 27

# Check admin user
$admin = User::first();
echo $admin->roles->first()->name; // Should show 'admin'
```

## Command Options

### Skip School Information Setup

If you want to set up school info later:

```bash
php artisan client:initialize --skip-school
```

### Skip Roles and Permissions Setup

If you already have roles/permissions configured:

```bash
php artisan client:initialize --skip-roles
```

### Both Options

```bash
php artisan client:initialize --skip-school --skip-roles
```

## Created Roles

The following 6 roles are created with predefined permissions:

### 1. Admin (Administrator)
- **Permissions**: All 27 permissions
- **Use case**: Full system access
- **Assigned to**: At least one staff member for system administration

### 2. Directeur (Director)
- **Permissions**:
  - config.view, config.edit, config.manage_years
  - students.view, students.create, students.edit
  - classes.view, classes.create, classes.edit
  - grades.view
  - attendance.view
  - scholarity.view, scholarity.manage
  - users.view, users.create, users.edit
  - audit.view
- **Use case**: Director/Principal of school
- **Typical assignments**: 1-2 administrators

### 3. Enseignant (Teacher)
- **Permissions**:
  - students.view
  - classes.view
  - grades.view, grades.create, grades.edit
  - attendance.view, attendance.record, attendance.edit
- **Use case**: Classroom teachers
- **Typical assignments**: All teaching staff

### 4. Surveillant (Monitor/Supervisor)
- **Permissions**:
  - students.view
  - classes.view
  - attendance.view, attendance.record
- **Use case**: Attendance monitoring, discipline
- **Typical assignments**: Discipline committee members

### 5. Parent (Parent/Guardian)
- **Permissions**:
  - students.view
  - grades.view
  - attendance.view
- **Use case**: View child's progress and attendance
- **Typical assignments**: Parent accounts

### 6. Student
- **Permissions**:
  - grades.view
  - attendance.view
- **Use case**: View own grades and attendance
- **Typical assignments**: All registered students

## Created Permissions (27 Total)

### Config Module (3)
- `config.view` - View system configuration
- `config.edit` - Edit system settings
- `config.manage_years` - Manage school years

### Students Module (4)
- `students.view` - View student records
- `students.create` - Create new students
- `students.edit` - Edit student information
- `students.delete` - Delete student records

### Classes Module (4)
- `classes.view` - View classes
- `classes.create` - Create new classes
- `classes.edit` - Edit class information
- `classes.delete` - Delete classes

### Grades Module (4)
- `grades.view` - View grades
- `grades.create` - Record new grades
- `grades.edit` - Edit recorded grades
- `grades.delete` - Delete grades

### Attendance Module (3)
- `attendance.view` - View attendance records
- `attendance.record` - Record attendance
- `attendance.edit` - Edit attendance records

### Billing/Scholarity Module (3)
- `scholarity.view` - View billing information
- `scholarity.manage` - Manage billing and payments
- **scholarity.modify_past_years** - Modify past school year data (special permission)

### User Management (5)
- `users.view` - View user accounts
- `users.create` - Create new users
- `users.edit` - Edit user information
- `users.delete` - Delete user accounts
- `users.manage_roles` - Assign roles to users

### Audit Module (1)
- `audit.view` - View audit logs

## Special Permission: scholarity.modify_past_years

This permission is critical for data integrity:

- **Default**: Only admin has this permission
- **Purpose**: Prevents accidental modification of past school year records
- **Effect**: Teachers cannot edit grades from previous years without this permission
- **Use case**: Exceptional cases where historical data correction is needed

To grant this permission to a user:

```bash
php artisan tinker
```

```php
$director = User::where('email', 'director@school.edu')->first();
$permission = Permission::where('permission_id', 'scholarity.modify_past_years')->first();
$director->givePermission($permission);
```

## System Settings Initialized

The following default system settings are created:

```php
[
    'timezone' => 'Africa/Douala',
    'currency' => 'FCFA',
    'date_format' => 'd/m/Y',
    'language' => 'fr',
    'max_students_per_class' => 45,
]
```

To modify these later:

```bash
php artisan tinker
```

```php
SystemSetting::where('key', 'timezone')->update(['value' => 'Africa/Lagos']);
SystemSetting::where('key', 'max_students_per_class')->update(['value' => '50']);
```

## School Years

Four default school years are created:

- **2022-2023**: Locked (historical data)
- **2023-2024**: Locked (previous year)
- **2024-2025**: ACTIVE (current year)
- **2025-2026**: Future year

The 2024-2025 year is automatically set as active. To change the active year:

```bash
php artisan tinker
```

```php
SchoolYear::where('name', '2024-2025')->update(['is_active' => false]);
SchoolYear::where('name', '2025-2026')->update(['is_active' => true]);
```

## Creating Additional Users

After initialization, create users for staff and students:

### Create a Teacher Account

```bash
php artisan tinker
```

```php
$teacher = User::create([
    'username' => 'jdoe',
    'email' => 'john.doe@school.edu',
    'password' => bcrypt('SecurePassword123!'),
    'full_name' => 'John Doe',
    'is_active' => true,
]);

// Assign teacher role
$teacher->assignRole('enseignant');

// Or assign multiple roles
$teacher->assignRole(['enseignant', 'surveillant']);
```

### Create a Parent Account

```php
$parent = User::create([
    'username' => 'parent_123',
    'email' => 'parent@example.com',
    'password' => bcrypt('SecurePassword123!'),
    'full_name' => 'Parent Name',
    'is_active' => true,
]);

$parent->assignRole('parent');
```

## Post-Setup Tasks

After running `client:initialize`, complete these tasks:

### 1. Verify Admin Access

```bash
php artisan serve
```

- Navigate to `/api/config/school`
- Login with admin credentials
- Verify you can view and edit school information

### 2. Configure School Logo

Upload school logo via:
- API endpoint: `POST /api/config/school/logo`
- Or manually via database

### 3. Create First Students

Create initial student records to test the system:

```bash
php artisan tinker
```

```php
$student = Student::create([
    'student_id_number' => 'EST-2024-0001',
    'first_name' => 'Test',
    'last_name' => 'Student',
    'date_of_birth' => '2006-01-15',
    'sex' => 'M',
    'email' => 'test.student@school.edu',
    'phone_number' => '+33612345678',
]);

// Create enrollment for current school year
$enrollment = StudentEnrollment::create([
    'student_id' => $student->id,
    'school_year_id' => SchoolYear::where('is_active', true)->first()->id,
    'enrollment_status' => EnrollmentStatus::ACTIVE,
]);
```

### 4. Assign Teacher to Classes

Once classes are created, assign teachers:

```php
$teacher = User::where('email', 'john.doe@school.edu')->first();
$class = Classes::where('name', '1st Year A')->first();

// Add teacher to class (depends on your model structure)
// Specific implementation varies based on your Classes bridge
```

### 5. Test School Year Switching

Verify session-based school year filtering works:

```bash
php artisan tinker
```

```php
// Set current session year
app(\Modules\Config\Services\SchoolYearSessionService::class)
    ->setActiveYear(SchoolYear::where('name', '2023-2024')->first());

// Verify data filters by session year
Student::sessionYear()->count(); // Should return students from 2023-2024
```

## Troubleshooting

### Issue: "Roles already exist"

If you get an error that roles already exist, run with `--skip-roles`:

```bash
php artisan client:initialize --skip-roles
```

### Issue: "School info already exists"

If you need to update school info, run with `--skip-school`:

```bash
php artisan client:initialize --skip-school
```

Then manually update in the database:

```bash
php artisan tinker
```

```php
$school = SchoolInfo::first();
$school->update(['name' => 'New School Name']);
```

### Issue: Permissions not assigned to roles

Check if role-permission relationships are correct:

```php
$admin = Role::where('name', 'admin')->first();
echo $admin->permissions->count(); // Should show 27 for admin
```

If missing, re-run:

```bash
php artisan client:initialize --skip-school --skip-roles
```

Wait for the "Permissions assigned to roles" message to appear.

### Issue: Admin user doesn't have role

Manually assign:

```php
$admin = User::first();
$adminRole = Role::where('name', 'admin')->first();
$admin->roles()->attach($adminRole);
```

## Next Steps

After successful initialization:

1. **Document your credentials**: Save admin username/password securely
2. **Create staff accounts**: Add teachers, monitors, director
3. **Create class structure**: Set up classes for the current year
4. **Import student data**: Bulk import or manually create students
5. **Assign teachers to classes**: Link teachers to their classes
6. **Test core workflows**: Grade entry, attendance, billing
7. **Train staff**: Conduct user training on the system

## Support

For issues or questions:

1. Check logs: `storage/logs/laravel.log`
2. Verify database: `php artisan db:seed`
3. Review permissions: `php artisan permissions:sync`
4. Check migrations: `php artisan migrate:status`

## Related Documentation

- [Module Structure](./MODULE_STRUCTURE.md)
- [Bridges and Dependencies](../bridges/BRIDGES.md)
- [School Year Management](./SCHOOL_YEAR_GUIDE.md)
- [Database Optimization](./DATABASE_OPTIMIZATION.md)
