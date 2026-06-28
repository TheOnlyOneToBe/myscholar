# Student Dashboard - Sidebar, Navbar & Multi-Role Policies

**Last Updated**: 2026-06-28  
**Status**: ✅ Complete - Sidebar, Navbar & Comprehensive Policies Implemented

---

## 📋 Overview

Complete implementation of the student dashboard UI components (sidebar and navbar) with comprehensive role-based access control policies that enforce read-only restrictions for chef de classe (class leader) access to classmates' data.

---

## 🎯 Components Implemented

### 1. Navbar Component

**File**: `modules/Dashboard/Livewire/StudentDashboard/StudentNavbar.php`

**Features**:
- School logo and branding area
- Student name and current class display
- Notification bell with counter
- Profile dropdown with settings and logout
- Chef de classe role badge (shows when applicable)
- Mobile-responsive design

**Properties**:
- `$studentName` - Current student's full name
- `$studentMatricule` - Student ID/matricule
- `$currentClass` - Current class assignment
- `$notificationCount` - Unread notifications count
- `$isChefClasse` - Whether user has chef_classe role
- `$showProfileDropdown` - Profile menu visibility
- `$showNotifications` - Notifications panel visibility

**Methods**:
- `mount()` - Load student information on component initialization
- `toggleProfileDropdown()` - Toggle profile menu
- `toggleNotifications()` - Toggle notifications panel
- `logout()` - Handle user logout
- `navigateTo(route)` - Navigate to different routes

**Template**: `resources/views/livewire/student-dashboard/student-navbar.blade.php`

---

### 2. Sidebar Component

**File**: `modules/Dashboard/Livewire/StudentDashboard/StudentSidebar.php`

**Features**:
- Main navigation with tabs and sections
- Modular layout by functionality:
  - General: Dashboard home
  - Academic: Grades, Attendance
  - Financial: Billing
  - Class & Reports: Class information
- Chef de classe section (conditional):
  - View class students
  - Analyze class grades
  - Manage class attendance
  - Review pending justifications
- Account section: Profile, Settings, Help
- Responsive design with toggle capability

**Properties**:
- `$isChefClasse` - Whether user has chef_classe role
- `$availableModules` - List of active modules for student
- `$activeTab` - Currently selected tab
- `$sidebarOpen` - Sidebar visibility state

**Methods**:
- `mount()` - Load sidebar data
- `updateActiveTab(tab)` - Update active tab (listener)
- `toggleSidebar()` - Toggle sidebar visibility
- `selectTab(tab)` - Select navigation tab

**Template**: `resources/views/livewire/student-dashboard/student-sidebar.blade.php`

**Navigation Structure**:
```
├── General
│   └── Accueil (Home/Dashboard)
├── Académique
│   ├── Mes Notes (My Grades) - if Grades module active
│   └── Mes Absences (My Attendance) - if Attendance module active
├── Finances
│   └── Facturation (Billing) - if Billing module active
├── Classe & Rapports
│   └── Ma Classe (My Class) - if Classes module active
├── Chef de Classe (conditional, if user is chef_classe)
│   ├── Élèves de la Classe (Class Students)
│   ├── Analyse des Notes (Grades Analysis)
│   ├── Gestion des Absences (Attendance Management)
│   └── Justifications en Attente (Pending Justifications)
└── Compte
    ├── Mon Profil (My Profile)
    ├── Paramètres (Settings)
    └── Aide (Help)
```

---

## 🔐 Security Policies

### Overview

Three-tier authorization system:
1. **Module Verification** - Check if module is installed and active
2. **Policy Authorization** - Enforce role-based access rules
3. **Ownership Verification** - Ensure users can only access their own data

### Enhanced Policies

#### 1. GradePolicy

**File**: `modules/Grades/Policies/GradePolicy.php`

**Enhanced Methods**:

| Method | Permission | Description |
|--------|-----------|-------------|
| `view()` | Enhanced | Students see own, chef_classe calls viewByClass |
| `viewByClass()` | NEW | Chef de classe can view classmates' grades (read-only) |
| `modifyByClass()` | NEW | Returns FALSE - Chef de classe cannot modify |
| `appealByClass()` | NEW | Returns FALSE - Chef de classe cannot appeal classmates' grades |
| `appeal()` | NEW | Students can only appeal their own grades |

**Access Matrix**:
```
                    | Own Grades | Classmates | Modify | Appeal | Download
--------------------|------------|-----------|--------|--------|----------
Student             | ✅ VIEW    | ❌ NO     | ❌ NO  | ✅ OWN | ✅ OWN
Chef de Classe      | ✅ VIEW    | ✅ VIEW   | ❌ NO  | ❌ NO  | ❌ NO
Teacher/Enseignant  | ✅ MANAGE  | ✅ MANAGE | ✅ YES | N/A    | N/A
Admin/Proviseur     | ✅ MANAGE  | ✅ MANAGE | ✅ YES | N/A    | N/A
```

---

#### 2. AttendanceRecordPolicy

**File**: `modules/Attendance/Policies/AttendanceRecordPolicy.php`

**Enhanced Methods**:

| Method | Permission | Description |
|--------|-----------|-------------|
| `view()` | Enhanced | Students see own, chef_classe calls viewByClass |
| `viewByClass()` | NEW | Chef de classe can view classmates' records (read-only) |
| `modifyByClass()` | NEW | Returns FALSE - No modifications allowed |
| `markByClass()` | NEW | Returns FALSE - Cannot mark classmates |
| `recordJustificationByClass()` | NEW | Returns FALSE - Cannot record for classmates |

**Access Matrix**:
```
                    | Own Records | Classmates | Mark | Record Just. | Modify
--------------------|-------------|-----------|------|--------------|--------
Student             | ✅ VIEW     | ❌ NO     | ❌   | ✅ OWN       | ❌ NO
Chef de Classe      | ✅ VIEW     | ✅ VIEW   | ❌   | ❌ NO        | ❌ NO
Teacher/Enseignant  | ✅ MANAGE   | ✅ MANAGE | ✅   | ✅ YES       | ✅ YES
Admin/Proviseur     | ✅ MANAGE   | ✅ MANAGE | ✅   | ✅ YES       | ✅ YES
```

---

#### 3. JustificationPolicy

**File**: `modules/Attendance/Policies/JustificationPolicy.php`

**Enhanced Methods**:

| Method | Permission | Description |
|--------|-----------|-------------|
| `view()` | Enhanced | Students see own, chef_classe calls viewByClass |
| `viewByClass()` | NEW | Chef de classe can view classmates' justifications (read-only) |
| `modifyByClass()` | NEW | Returns FALSE - No modifications |
| `submitForClassmate()` | NEW | Returns FALSE - Cannot submit for classmates |
| `approveByClass()` | NEW | Returns FALSE - Cannot approve classmates |
| `manageByClass()` | NEW | Returns FALSE - Cannot manage classmates |

---

#### 4. StudentPolicy

**File**: `modules/Students/Policies/StudentPolicy.php`

**Enhanced Methods**:

| Method | Permission | Description |
|--------|-----------|-------------|
| `view()` | Enhanced | Own profile, chef_classe calls viewByClass |
| `viewByClass()` | NEW | Chef de classe can view classmates' profiles (read-only) |
| `modifyByClass()` | NEW | Returns FALSE - No modifications |
| `deleteByClass()` | NEW | Returns FALSE - No deletions |
| `manageByClass()` | NEW | Returns FALSE - No management |

**Scope Definitions**:
- **Own Data**: Student can always view and manage their own profile
- **Classmates (Chef de Classe Only)**: Read-only view of students in same class
- **Class Leader**: Can view class statistics and pending actions

---

#### 5. InvoicePolicy

**File**: `modules/Billing/Policies/InvoicePolicy.php`

**Enhanced Methods**:

| Method | Permission | Description |
|--------|-----------|-------------|
| `view()` | Enhanced | Own invoices, chef_classe calls viewByClass |
| `viewByClass()` | NEW | Chef de classe can view classmates' invoices (read-only) |
| `downloadByClass()` | NEW | Returns FALSE - Cannot download classmates' |
| `modifyByClass()` | NEW | Returns FALSE - No modifications |
| `manageByClass()` | NEW | Returns FALSE - No management |

**Invoice Access Control**:
```
                    | Own Invoices | Classmates | Download | Modify | Pay
--------------------|--------------|-----------|----------|--------|-----
Student             | ✅ VIEW      | ❌ NO     | ✅ OWN   | ❌ NO  | ✅ OWN
Chef de Classe      | ✅ VIEW      | ✅ VIEW   | ❌ NO    | ❌ NO  | ❌ NO
Accountant          | ✅ MANAGE    | ✅ MANAGE | N/A      | ✅ YES | ✅ YES
Admin               | ✅ MANAGE    | ✅ MANAGE | N/A      | ✅ YES | ✅ YES
```

---

#### 6. DocumentPolicy (NEW)

**File**: `modules/Dashboard/Policies/DocumentPolicy.php`

**Purpose**: Enforce document download restrictions at the dashboard level

**Methods** (for each document type):
- `downloadSchoolCertificate($user, $academicYearId, $student)`
- `downloadReportCard($user, $academicYearId, $student)`
- `downloadTranscript($user, $student)`
- `downloadEnrollmentSummary($user, $student)`
- `downloadInvoice($user, $invoiceId, $student)`

**Read-Only Methods** (always return false):
- `downloadSchoolCertificateByClass()`
- `downloadReportCardByClass()`
- `downloadTranscriptByClass()`
- `downloadEnrollmentSummaryByClass()`
- `downloadInvoiceByClass()`

**Document Download Matrix**:
```
                    | Own Cert | Own Report | Own Trans | Own Summary | Own Invoice
--------------------|----------|-----------|-----------|-------------|-------------
Student             | ✅ DL    | ✅ DL     | ✅ DL     | ✅ DL       | ✅ DL
Chef de Classe      | ❌ NO    | ❌ NO     | ❌ NO     | ❌ NO       | ❌ NO
(on classmates)     |          |           |           |             |
```

---

## 📁 File Structure

```
modules/
├── Dashboard/
│   ├── Livewire/StudentDashboard/
│   │   ├── StudentNavbar.php                    ✅ NEW
│   │   ├── StudentSidebar.php                   ✅ NEW
│   │   ├── StudentDashboardMain.php             (existing)
│   │   ├── StudentGradesSection.php             (existing)
│   │   ├── StudentAttendanceSection.php         (existing)
│   │   ├── StudentBillingSection.php            (existing)
│   │   ├── StudentClassSection.php              (existing)
│   │   ├── ChefClasseSection.php                (existing)
│   │   └── StudentProfileSection.php            (existing)
│   ├── Policies/
│   │   └── DocumentPolicy.php                   ✅ NEW
│   ├── Controllers/
│   │   └── DocumentDownloadController.php       ✅ UPDATED
│   ├── Providers/
│   │   └── DashboardServiceProvider.php         ✅ UPDATED
│   └── resources/views/livewire/student-dashboard/
│       ├── student-navbar.blade.php             ✅ NEW
│       ├── student-sidebar.blade.php            ✅ NEW
│       └── (other section views)                (existing)
│
├── Grades/
│   └── Policies/
│       └── GradePolicy.php                      ✅ UPDATED
│
├── Attendance/
│   └── Policies/
│       ├── AttendanceRecordPolicy.php           ✅ UPDATED
│       └── JustificationPolicy.php              ✅ UPDATED
│
├── Students/
│   └── Policies/
│       └── StudentPolicy.php                    ✅ UPDATED
│
└── Billing/
    └── Policies/
        └── InvoicePolicy.php                    ✅ UPDATED
```

---

## 🔒 Authorization Flow

### Student Viewing Own Data

```
Request (Student views own grades)
    ↓
Module Verification ✅
    └─ Students module active?
    ↓
Policy Authorization ✅
    └─ GradePolicy::view($user, $grade)
       └─ Check: $grade->student_id === $user->student->id
    ↓
✅ Access Granted → Show Data
```

### Chef de Classe Viewing Classmate Data (Read-Only)

```
Request (Chef de classe views classmate's grades)
    ↓
Module Verification ✅
    └─ Grades module active?
    ↓
Policy Authorization ✅
    └─ GradePolicy::view($user, $grade)
       └─ Check: User has chef_classe role
       └─ Call: GradePolicy::viewByClass($user, $grade)
          └─ Verify: Same class
    ↓
✅ Access Granted (Read-Only) → Show Data
    ↓
Modification Actions BLOCKED ❌
    ├─ GradePolicy::modifyByClass() → FALSE
    ├─ GradePolicy::appealByClass() → FALSE
    └─ DocumentPolicy::downloadReportCardByClass() → FALSE
```

### Chef de Classe Attempting to Modify

```
Request (Chef de classe tries to modify classmate's attendance)
    ↓
Module Verification ✅
    ↓
Policy Authorization ✅
    └─ AttendanceRecordPolicy::markByClass($user, $record)
       └─ Returns FALSE
    ↓
❌ Access Denied (403 Forbidden)
```

---

## 🚀 Integration with DocumentDownloadController

**Updated Methods**:

```php
// All methods now verify ownership using DocumentPolicy
public function schoolCertificate(int $academicYearId): Response
{
    // Only own certificates can be downloaded
    if (!Gate::allows('downloadSchoolCertificate', [$academicYearId, $student])) {
        abort(403, 'You can only download your own certificates');
    }
    // ... generate PDF
}

public function reportCard(int $academicYearId): Response
{
    if (!Gate::allows('downloadReportCard', [$academicYearId, $student])) {
        abort(403, 'You can only download your own report cards');
    }
    // ... generate PDF
}

// Similar for: transcript(), enrollmentSummary(), invoice()
```

---

## 📊 Chef de Classe Feature Access Matrix

| Feature | Access Level | Can View | Can Modify | Can Download |
|---------|--------------|----------|-----------|--------------|
| Classmates Grades | Class | ✅ Yes | ❌ No | ❌ No |
| Classmates Attendance | Class | ✅ Yes | ❌ No | ❌ No |
| Classmates Invoices | Class | ✅ Yes | ❌ No | ❌ No |
| Classmates Justifications | Class | ✅ Yes | ❌ No | ❌ No |
| Classmates Profiles | Class | ✅ Yes | ❌ No | ❌ No |
| Class Statistics | Class | ✅ Yes | ❌ No | ❌ No |
| Pending Justifications | Class | ✅ Yes | ❌ No | N/A |

---

## 🛡️ Security Enforcement

### Method-Level Protection

```php
// Example: Can chef de classe download classmates' invoices?
public function downloadByClass(User $user, Invoice $invoice): bool
{
    return false; // Always false - read-only enforcement
}
```

### Controller-Level Protection

```php
// DocumentDownloadController
if (!Gate::allows('downloadInvoice', [$invoiceId, $student])) {
    abort(403, 'Unauthorized - You can only download your own invoices');
}
```

### Ownership Verification

```php
// Each policy verifies class membership for chef_classe
$userStudent->current_class_id === $recordStudent->current_class_id
```

---

## 📋 Implementation Checklist

- [x] Create StudentNavbar component
- [x] Create StudentSidebar component
- [x] Create navbar Blade template
- [x] Create sidebar Blade template
- [x] Create DocumentPolicy
- [x] Enhance GradePolicy with chef_classe methods
- [x] Enhance AttendanceRecordPolicy with chef_classe methods
- [x] Enhance JustificationPolicy with chef_classe methods
- [x] Enhance StudentPolicy with chef_classe methods
- [x] Enhance InvoicePolicy with chef_classe methods
- [x] Update DocumentDownloadController with policy checks
- [x] Register policies in DashboardServiceProvider
- [x] Register Livewire components
- [x] Update authentication gates for document downloads

---

## 🧪 Testing Scenarios

### Test Case 1: Student Downloads Own Certificate
```
User: student_1 (matricule: STU001)
Action: Download school certificate
Expected: ✅ Success - PDF generated
Authorization: view($user, $student) → TRUE
Document: downloadSchoolCertificate() → TRUE
```

### Test Case 2: Chef de Classe Views Classmate Grades
```
User: student_2 (matricule: STU002, role: chef_classe)
Target: student_3 (matricule: STU003, same class)
Action: View classmate grades
Expected: ✅ Success - Read-only view
Authorization: viewByClass($user, $grade) → TRUE
Modification: modifyByClass() → FALSE
```

### Test Case 3: Chef de Classe Attempts to Download Classmate Invoice
```
User: student_2 (matricule: STU002, role: chef_classe)
Target: student_3's invoice
Action: Download invoice
Expected: ❌ Forbidden (403)
Authorization: downloadByClass() → FALSE
```

### Test Case 4: Chef de Classe from Different Class
```
User: student_4 (matricule: STU004, role: chef_classe, class: 6A)
Target: student_5 (matricule: STU005, class: 6B)
Action: View grades
Expected: ❌ Forbidden - Different class
Authorization: viewByClass() → FALSE (different class_id)
```

---

## 📝 Notes

### Read-Only Implementation

All chef_classe access to classmates' data is strictly read-only:
- View methods return TRUE
- Modify/Delete/Download methods return FALSE
- No exceptions for any operations
- Consistent across all modules

### Module Activation

All policies require the appropriate module to be active:
- Grades policy requires Grades module
- Attendance policies require Attendance module
- Student/Invoice policies require their respective modules

### Error Messages

Clear error messages for authorization failures:
- "You can only download your own certificates"
- "You can only download your own report cards"
- "You can only download your own invoices"
- "Unauthorized - Permission denied"

---

## 🔄 Policy Inheritance Hierarchy

```
User Role Hierarchy:
    super_administrator (full access)
    ├── proviseur (all except super admin actions)
    ├── prof_principal (department level)
    ├── chef_classe (class level, read-only classmates)
    ├── enseignant (class management)
    ├── student (own data + classmates if chef_classe)
    └── parent (child data only)

Chef de Classe Permissions (Additive):
    base student permissions +
    read-only view of classmates' data (same class only)
    - Cannot modify classmates
    - Cannot delete classmates' data
    - Cannot download classmates' documents
    - Cannot approve/reject classmates' actions
```

---

## 🎯 Next Steps

1. **Test all scenarios** in development environment
2. **Audit logging** for data access by chef_classe
3. **Email notifications** when documents are generated
4. **PDF generation** via mPDF or DomPDF library integration
5. **Performance optimization** for large class data queries
6. **Caching strategy** for frequently accessed class data
7. **Mobile UI testing** for sidebar/navbar responsiveness

---

## 📞 Summary

A comprehensive, secure, multi-role authorization system has been implemented for the student dashboard with:
- **✅ Complete UI components** (navbar and sidebar)
- **✅ 6 enhanced policies** with chef_classe read-only access
- **✅ Document download restrictions** at controller level
- **✅ Ownership verification** for all sensitive data
- **✅ Clear error messages** for failed authorizations
- **✅ Consistent read-only enforcement** across all modules

All chef_classe access to classmates' data is strictly read-only with no modifications, deletions, or downloads allowed.
