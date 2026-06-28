# Parent Dashboard - Feature List & Architecture

## Overview

The Parent Dashboard is a dedicated interface for parents to monitor their children's academic performance, attendance, and financial obligations. It provides a comprehensive view of all children's data in a single, accessible interface with proper organization through sidebar navigation and responsive navbar.

---

## Complete Feature List

### 1. **Dashboard Overview** (default view)
- [x] Global statistics summary card:
  - Total number of children enrolled
  - Average academic performance across all children (in /20)
  - Total outstanding balance (in FCFA)
  - Total absences across all children
- [x] Active alerts display (unified across all children)
- [x] Children cards overview showing:
  - Child's full name and student ID
  - Current class/level
  - Enrollment status (active/inactive/suspended)
  - Avatar with child's first letter

**Associated Components:**
- Livewire: `ParentDashboardMain`
- Blade: `parent-dashboard-main.blade.php`

---

### 2. **Children Management**
- [x] View all children linked to parent account
- [x] Display per child:
  - Full name and student ID
  - Current class and level
  - Current enrollment status
  - Quick access to child-specific data
- [x] Child selection mechanism
- [x] Child switching between different data views

**Associated Components:**
- Livewire: `ParentChildrenSection`
- Blade: `parent-children-section.blade.php`
- Data Source: `ParentDashboardService::getChildren()`

---

### 3. **Academic Grades Section**
- [x] Display selected child's academic information:
  - Overall average grade (/20)
  - Recent grades table with:
    - Subject name
    - Score (/20)
    - Letter grade (A-F)
    - Date received
  - Performance by subject:
    - Subject name
    - Average score
    - Number of grades
    - Visual progress bar
- [x] Filter support for different academic periods (trimestres/semestres)
- [x] Grade status indicators (color-coded)

**Associated Components:**
- Livewire: `ParentGradesSection`
- Blade: `parent-grades-section.blade.php`
- Data Sources:
  - `ParentDashboardService::getChildRecentGrades($studentId, $limit)`
  - `ParentDashboardService::getChildAverage($studentId)`
  - `ParentDashboardService::getChildSubjectPerformance($studentId)`

---

### 4. **Attendance Tracking Section**
- [x] Display selected child's attendance summary:
  - Total present days
  - Total absent days
  - Total justified absences
  - Total late arrivals
  - Overall attendance rate (%)
  - Visual progress bar
- [x] Unjustified absences list:
  - Date of absence
  - Subject/class name
  - Status indicators
- [x] Absence notifications and tracking

**Associated Components:**
- Livewire: `ParentAttendanceSection`
- Blade: `parent-attendance-section.blade.php`
- Data Sources:
  - `ParentDashboardService::getChildAttendanceSummary($studentId)`
  - `ParentDashboardService::getChildUnjustifiedAbsences($studentId)`

---

### 5. **Billing & Financial Management**
- [x] Display selected child's financial summary:
  - Total outstanding balance (in FCFA)
  - Invoice list with:
    - Invoice number
    - Amount due
    - Due date
    - Current status
    - Overdue indicator
    - PDF download link
- [x] Recent payments history:
  - Payment amount
  - Payment method
  - Payment reference
  - Payment date
- [x] Outstanding invoices status tracking
- [x] Visual indicators for overdue invoices

**Associated Components:**
- Livewire: `ParentBillingSection`
- Blade: `parent-billing-section.blade.php`
- Data Sources:
  - `ParentDashboardService::getChildOutstandingInvoices($studentId)`
  - `ParentDashboardService::getChildRecentPayments($studentId)`
  - `ParentDashboardService::getChildOutstandingBalance($studentId)`

---

### 6. **Bulletins (Report Cards) Section**
- [x] Display available bulletins for selected child:
  - Academic period name (trimester/semester)
  - Period type
  - Period dates (start and end)
  - Current status (upcoming/current/completed)
- [x] Bulletin download functionality
- [x] Status-based availability control
- [x] Support for multiple academic periods

**Associated Components:**
- Livewire: `ParentBulletinSection`
- Blade: `parent-bulletin-section.blade.php`
- Data Sources:
  - `ParentDashboardService::getChildBulletins($studentId)`
  - `DocumentGenerationService::generateReportCardData()`

---

### 7. **Unified Alerts System**
- [x] Global alerts page showing all notifications for all children:
  - **Absence Alerts:** Unjustified absences in the past week
  - **Grade Alerts:** Low grades (score < 10) in the past week
  - **Payment Alerts:** Overdue invoices with late status
- [x] Alert severity levels:
  - Danger (red) - urgent payment issues
  - Warning (yellow) - attendance/grade concerns
  - Info (blue) - informational messages
- [x] Alert details:
  - Student name
  - Alert message
  - Alert type icon
  - Severity indicator
- [x] Refresh functionality
- [x] Alert dismissal (future enhancement)

**Associated Components:**
- Livewire: `ParentAlertsSection`
- Blade: `parent-alerts-section.blade.php`
- Data Source: `ParentDashboardService::getAlerts()`

---

### 8. **Navigation & UI Components**

#### **Sidebar Navigation**
- [x] Fixed left sidebar (dark theme)
- [x] Navigation sections:
  - **General:** Home/Overview
  - **My Children:** List of all children with quick selection
  - **Academic:** Notes, Attendance, Bulletins
  - **Financial:** Billing/Invoices
  - **Monitoring:** Unified Alerts
  - **Account:** Profile, Settings
- [x] Active state indicators
- [x] Responsive mobile toggle
- [x] Section grouping with visual hierarchy
- [x] Version indicator
- [x] Font Awesome icons throughout

**Associated Components:**
- Livewire: `ParentSidebar`
- Blade: `parent-sidebar.blade.php`

#### **Navbar**
- [x] Header with MyScholar branding
- [x] "Parent Portal" subtitle
- [x] Display number of children being tracked
- [x] Notifications bell with count indicator
- [x] Profile dropdown menu:
  - Parent name and email
  - Profile link
  - Settings link
  - Logout button
- [x] Responsive design (mobile menu toggle)
- [x] Font Awesome icons

**Associated Components:**
- Livewire: `ParentNavbar`
- Blade: `parent-navbar.blade.php`

---

## Data Flow Architecture

### Service Layer
**`ParentDashboardService`** - Core business logic for parent data access:

```php
// Children Management
getChildren(): array
  → Returns all children linked to parent's email/phone
  → Each child includes: id, name, student_id, class, status

// Academic Data
getChildRecentGrades(int $studentId, int $limit = 5): array
  → Returns recent grades with subject, score, grade letter, date
  
getChildAverage(int $studentId): float
  → Returns overall average (/20)

getChildSubjectPerformance(int $studentId): array
  → Returns per-subject averages with grade counts

// Attendance Data
getChildAttendanceSummary(int $studentId): array
  → Returns present, absent, justified, late, total, rate

getChildUnjustifiedAbsences(int $studentId): array
  → Returns recent unjustified absences with dates

// Billing Data
getChildOutstandingInvoices(int $studentId): array
  → Returns unpaid invoices with status and overdue flag

getChildRecentPayments(int $studentId, int $limit = 5): array
  → Returns recent payment history

getChildOutstandingBalance(int $studentId): float
  → Returns total unpaid amount (FCFA)

// Academic Periods
getChildBulletins(int $studentId): array
  → Returns available bulletins with period info

// Alerts & Monitoring
getAlerts(): array
  → Returns unified alerts across all children
  → Checks: absences (>0 in 7 days), low grades (<10 in 7 days), overdue invoices

// Global Statistics
getGlobalStats(): array
  → Returns aggregated stats: total_children, average_performance, total_balance, total_absences
```

### Livewire Components Hierarchy

```
ParentSidebar (fixed navigation)
  ├─ Child selection buttons
  └─ Tab navigation

ParentNavbar (fixed header)
  ├─ School branding
  ├─ Notifications
  └─ Profile dropdown

ParentDashboardMain (main container)
  ├─ Global stats cards
  ├─ Alerts display
  └─ Children overview

ParentChildrenSection (conditional)
  └─ Children cards grid

ParentGradesSection (conditional)
  ├─ Recent grades table
  └─ Subject performance grid

ParentAttendanceSection (conditional)
  ├─ Attendance stats
  └─ Unjustified absences list

ParentBillingSection (conditional)
  ├─ Outstanding invoices
  └─ Recent payments

ParentBulletinSection (conditional)
  └─ Bulletin cards with download

ParentAlertsSection (conditional)
  └─ Unified alerts list
```

---

## Database Dependencies

### Required Tables:
1. **users** - Parent user accounts with email/phone
2. **students** - Student records
3. **family_contacts** - Links students to parent email/phone
4. **grades** - Student grades by subject
5. **subjects** - Academic subjects
6. **classes** - School classes
7. **attendance_records** - Attendance tracking
8. **invoices** - Billing/invoice records
9. **payments** - Payment records
10. **student_enrollments** - Student enrollment data
11. **academic_periods** - Academic periods (terms/semesters)
12. **enrollment_academic_periods** - Links enrollments to periods

---

## Security & Authorization

- [x] `parent` middleware on all parent routes
- [x] Parent can only view their own children's data (via family_contacts email/phone matching)
- [x] No cross-parent data leakage
- [x] Role-based access control enforced at middleware level

---

## Responsive Design

- [x] Mobile-friendly sidebar (toggle-able)
- [x] Responsive grid layouts (1 col mobile → 2-4 cols desktop)
- [x] Touch-friendly navigation
- [x] Optimized table views for mobile
- [x] Tailwind CSS utility classes throughout

---

## Color Scheme & Styling

- **Primary:** Blue (#2563EB)
- **Success:** Green (#16A34A)
- **Warning:** Yellow/Orange (#EAB308/#EA580C)
- **Danger:** Red (#DC2626)
- **Sidebar:** Dark Gray (#111827)
- **Background:** Light Gray (#F3F4F6)
- **Cards:** White (#FFFFFF)
- **Text:** Dark Gray (#1F2937)

**Icons:** Font Awesome 6.4.0

---

## Implementation Status

| Feature | Component | Blade | Livewire | Routes | Status |
|---------|-----------|-------|----------|--------|--------|
| Dashboard Overview | ParentDashboardMain | ✓ | ✓ | ✓ | Complete |
| Children Management | ParentChildrenSection | ✓ | ✓ | ✓ | Complete |
| Grades Section | ParentGradesSection | ✓ | ✓ | ✓ | Complete |
| Attendance Section | ParentAttendanceSection | ✓ | ✓ | ✓ | Complete |
| Billing Section | ParentBillingSection | ✓ | ✓ | ✓ | Complete |
| Bulletins Section | ParentBulletinSection | ✓ | ✓ | ✓ | Complete |
| Alerts Section | ParentAlertsSection | ✓ | ✓ | ✓ | Complete |
| Sidebar Navigation | ParentSidebar | ✓ | ✓ | ✓ | Complete |
| Navbar Header | ParentNavbar | ✓ | ✓ | ✓ | Complete |
| Main Layout | parent-dashboard.blade.php | ✓ | - | ✓ | Complete |
| Controller | ParentDashboardController | - | - | ✓ | Complete |
| Service Layer | ParentDashboardService | - | - | - | Pre-existing |

---

## Usage Example

```php
// Access parent dashboard
GET /parent-dashboard → ParentDashboardController@dashboard

// Livewire components handle:
- Child selection (ParentSidebar)
- Real-time data loading (ParentGradesSection, ParentAttendanceSection, etc.)
- Event dispatching for child/tab changes
- Error handling and logging

// Database query flow:
1. Parent logs in → authenticated as 'parent' role
2. ParentDashboardService::getChildren() queries family_contacts
3. Parent selects child → event dispatch
4. Selected component loads child-specific data
5. Blade view renders with Tailwind styling and Font Awesome icons
```

---

## Future Enhancements

- [ ] Add messaging system with teachers
- [ ] Export reports to PDF (parent summary view)
- [ ] Email notifications for alerts
- [ ] SMS notifications option
- [ ] Payment reminder emails
- [ ] Customizable alert thresholds
- [ ] Parent-to-school communication portal
- [ ] Document uploads (justifications, etc.)
- [ ] Multi-language support
- [ ] Two-factor authentication
- [ ] Parent activity logs

---

## Performance Considerations

- Eager loading relationships (family_contacts, grades, invoices)
- Caching for frequently accessed data
- Lazy loading of bulletins
- Database indexes on foreign keys
- Pagination for large datasets (future)
- API rate limiting for parent endpoints

---

## Testing

All Livewire components should have:
- Unit tests for service methods
- Feature tests for component mounting
- Integration tests for child selection flows
- UI tests for navigation
- Authorization tests for role-based access

---

## File Structure Summary

```
modules/Dashboard/
├── Livewire/
│   └── ParentDashboard/
│       ├── ParentDashboardMain.php
│       ├── ParentSidebar.php
│       ├── ParentNavbar.php
│       ├── ParentChildrenSection.php
│       ├── ParentGradesSection.php
│       ├── ParentAttendanceSection.php
│       ├── ParentBillingSection.php
│       ├── ParentBulletinSection.php
│       └── ParentAlertsSection.php
├── Controllers/
│   └── ParentDashboardController.php
├── Services/
│   └── ParentDashboardService.php (pre-existing)
├── Routes/
│   └── web.php (updated)
└── resources/views/
    ├── parent-dashboard.blade.php
    └── livewire/parent-dashboard/
        ├── parent-dashboard-main.blade.php
        ├── parent-sidebar.blade.php
        ├── parent-navbar.blade.php
        ├── parent-children-section.blade.php
        ├── parent-grades-section.blade.php
        ├── parent-attendance-section.blade.php
        ├── parent-billing-section.blade.php
        ├── parent-bulletin-section.blade.php
        └── parent-alerts-section.blade.php
```

---

**Last Updated:** June 28, 2026
**Status:** Complete Implementation
**Version:** 1.0
