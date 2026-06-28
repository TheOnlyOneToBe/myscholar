# Student + Chef de Classe - Combined Role Features

**Last Updated**: 2026-06-28
**Role Combination**: Student (Élève) + Chef de Classe (Class Leader)

---

## 📋 Overview

When a student also holds the **chef_classe** (class leader) role, they gain additional permissions beyond the basic student role. This document shows:
1. What a plain **Student** can do
2. What a **Chef de Classe** can do
3. What additional features are available when **both roles are combined**

---

## 🎓 Role Comparison Table

| Permission | Plain Student | Chef de Classe | Student + Chef de Classe |
|------------|:---:|:---:|:---:|
| **Auth** |
| auth.change_password | ✅ | ✅ | ✅ |
| **Students** |
| students.view_own | ✅ | ✅ | ✅ |
| students.view_by_class | ❌ | ✅ | ✅ |
| **Grades** |
| grades.view_own | ✅ | ✅ | ✅ |
| grades.view_by_class | ❌ | ✅ | ✅ |
| grades.view_statistics | ❌ | ✅ | ✅ |
| **Attendance** |
| attendance.view_own | ✅ | ❌ | ✅ |
| attendance.view_by_class | ❌ | ✅ | ✅ |
| attendance.record | ❌ | ✅ | ✅ |
| attendance.view_justifications | ❌ | ✅ | ✅ |
| **Classes** |
| classes.view | ❌ | ✅ | ✅ |
| **Notifications** |
| notifications.send_email | ❌ | ✅ | ✅ |

---

## 📊 Feature Breakdown by Module

### 1️⃣ **Students Module**

#### Plain Student Can:
- ✅ View own student information
  - Full name
  - Student ID/Matricule
  - Date of birth
  - Gender
  - Current class
  - Contact information

#### Chef de Classe ADDS:
- ✅ **View all students in their class**
  - Classmate names
  - Roll numbers
  - Student IDs
  - Basic contact information (scope: by_class)
- ✅ **View emergency contacts** (for class members)
- ✅ **View parent/guardian information** (for class members)

---

### 2️⃣ **Grades Module**

#### Plain Student Can:
- ✅ View own grades
  - Subject name
  - Score/Mark
  - Grade (A, B, C, etc.)
  - Date graded
  - Teacher's feedback

#### Chef de Classe ADDS:
- ✅ **View all grades in their class**
  - All student grades for all subjects
  - Filter by student, subject, period
- ✅ **View class statistics**
  - Class average
  - Best performing student
  - Weakest performing student
  - Grade distribution
  - Subject performance comparison (class-level)
- ✅ **View class averages**
  - Average by subject
  - Overall class average
  - Period-over-period comparison
- ✅ **Generate class reports**
  - Academic performance reports for the class
  - Export grades for class members

---

### 3️⃣ **Attendance Module**

#### Plain Student Can:
- ✅ View own attendance records
  - Date, Status (Present/Absent/Late/Excused)
  - By date, by period, by subject
- ✅ View personal attendance statistics
  - Overall attendance percentage
  - Monthly breakdown
  - Total present/absent/late/excused

#### Chef de Classe ADDS:
- ✅ **View class attendance records**
  - Attendance for all class members
  - Filter by student, date, period
  - View attendance history
- ✅ **Record attendance for the class**
  - Mark attendance during class sessions
  - Bulk attendance recording
  - Mark as present, absent, late, or excused
- ✅ **View class absence justifications**
  - All justifications submitted by class members
  - Justification status (pending/approved/rejected)
  - Admin notes/responses
  - View justification documents

---

### 4️⃣ **Classes Module**

#### Plain Student Can:
- ✅ View current class details (basic)
  - Class name/level
  - Class size
  - Form tutor

#### Chef de Classe ADDS:
- ✅ **Full class information access**
  - Class name/level
  - Class code
  - Number of students
  - **Class representative (chef_classe info)**
  - Form tutor/Principal teacher
- ✅ **View class timetable**
  - Complete weekly schedule
  - Subject names
  - Teacher names
  - Room locations
- ✅ **View exam schedule**
  - Exam dates and times
  - Exam subjects
  - Exam venues
- ✅ **View important dates**
  - School holidays
  - Term dates
  - Exam periods

---

### 5️⃣ **Notifications Module**

#### Plain Student Can:
- ❌ Cannot send notifications

#### Chef de Classe ADDS:
- ✅ **Send email notifications to class members**
  - Notify about attendance
  - Notify about grades
  - Notify about schedule changes
  - Notify about important announcements

---

## 🎯 Summary: What's New for Student + Chef de Classe

### NEW CAPABILITIES

**Students Management (by_class scope)**
- Access to all 7 students records in class (vs. own only)
- View emergency and parent contact information

**Grades Management (by_class scope)**
- View all class grades (not just own)
- View class statistics and averages
- Generate class academic reports

**Attendance Management (by_class scope)**
- View class attendance history
- **Record attendance** (new action type)
- View and approve justifications (new action type)

**Classes Management**
- Full access to timetables, exam schedules, and dates

**Notifications**
- Can send emails to class (new capability)

### TOTAL FEATURE COUNT

| Action Type | Plain Student | Chef de Classe Only | Combined |
|---------|:---:|:---:|:---:|
| **Can VIEW** | 12 | 18 | 25 |
| **Can RECORD/SUBMIT** | 0 | 1 (attendance) | 1 |
| **Can SEND** | 0 | 1 (email) | 1 |
| **TOTAL** | **12** | **20** | **27** |

**Additional capabilities gained: +15 features**

---

## 🔐 Authorization Rules

When a user has both **student** and **chef_classe** roles, the API must:

1. **Data Filtering**
   - For `view_own` permissions: Always filter by authenticated user ID
   - For `view_by_class` permissions: Filter by class ID of the authenticated student/chef
   - For `record` permissions: Only allow recording for the authenticated chef's class
   - For `send` permissions: Only allow sending to class members

2. **Class Verification**
   ```php
   // Before allowing access to class_id data:
   if (chef_classe_user.class_id !== requested_class_id) {
       return 403 Forbidden; // Not their class
   }
   ```

3. **Scope Limitations**
   - chef_classe can ONLY access data for their ONE assigned class
   - Cannot view other classes' data (even if they have view_by_class permission)
   - Cannot record attendance for other classes
   - Cannot send emails to other classes

---

## 📝 Database Schema Implications

### Key Tables for Chef de Classe Features

**class_assignments** (Student-Class bridge)
```
student_id → students.id
class_id → classes.id
is_chef_classe → BOOLEAN (identifies the class leader)
academic_year_id → academic_years.id
```

**attendance_records** (Chef can record)
```
student_id → students.id
class_id → classes.id
recorded_by_id → users.id (chef's user_id)
status → ENUM(present, absent, late, excused)
```

**grade_appeals** (Chef can view stats)
```
student_id → students.id
class_id → classes.id
status → ENUM(pending, approved, rejected)
```

---

## 📱 Dashboard Layout for Student + Chef de Classe

The dashboard should have TWO different views/tabs:

### Tab 1: "My Student Dashboard" (Student view)
- Welcome message with student name
- My grades (own only)
- My attendance (own only)
- My profile
- Settings

### Tab 2: "Class Management" (Chef de Classe view)
- Class overview
  - Number of students
  - Class leader badge
  - Class timetable
- Class grades statistics
  - Class average
  - Grade distribution
  - Subject performance
- Class attendance management
  - Today's attendance
  - Record attendance button
  - View absent students
  - View justifications to approve
- Class communications
  - Send email to class
  - View announcements

---

## 🔒 What Student + Chef de Classe CANNOT Do

- ❌ Edit student information (any student)
- ❌ Create or modify grades
- ❌ Modify attendance records (can only record, not edit past records)
- ❌ Manage class structure (create/delete classes)
- ❌ Assign subjects to classes
- ❌ Manage timetables (view only)
- ❌ Access other classes' data
- ❌ View other students' personal information (parents, addresses)
- ❌ Access billing/financial information
- ❌ Create or manage invoices
- ❌ Access audit logs
- ❌ Manage system configuration
- ❌ Create or delete users

---

## ✅ Implementation Checklist

When building the Student + Chef de Classe dashboard:

- [ ] Create StudentDashboardController with dual-role awareness
- [ ] Implement role detection middleware that checks for both student AND chef_classe roles
- [ ] Create separate views/components for student vs. chef_classe sections
- [ ] Implement class verification before granting access to by_class data
- [ ] Add attendance recording UI in chef_classe section
- [ ] Add email notification UI in chef_classe section
- [ ] Implement statistics generation service for class grades/attendance
- [ ] Add grade appeals review UI (if needed)
- [ ] Implement Livewire components for interactive features
- [ ] Add comprehensive tests for multi-role permission combinations
- [ ] Document API rate limiting for attendance recording
- [ ] Add audit logging for attendance and email actions

---

## 📚 Related Documentation

- See `STUDENT_DASHBOARD_FEATURES.md` for plain student features
- See `PermissionsSeeder.php` for complete permission list
- See `MODULE_VERIFICATION_GUIDE.md` for implementation patterns
- See `MODULES_STRUCTURE.md` for module relationships

