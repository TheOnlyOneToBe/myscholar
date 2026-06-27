# MyScholar - Next Modules Implementation Plan

## Overview
This document outlines the implementation of three critical modules for the MyScholar school management system:
- **Grades Module** - Academic performance tracking and grading
- **Attendance Module** - Student attendance and absence management
- **Billing Module** - Fee management and payment processing

---

## 1. GRADES MODULE

### 1.1 Database Design

#### Tables
```
subjects
├── id (BIGINT PK)
├── code (VARCHAR 50, UNIQUE)
├── name (VARCHAR 255)
├── description (TEXT)
├── credits (INT, default: 3)
├── coefficient (DECIMAL 5,2, default: 1.0)
├── is_active (BOOLEAN)
├── created_at, updated_at

grade_periods
├── id (BIGINT PK)
├── school_year_id (FK)
├── name (VARCHAR 100) -- "Trimestre 1", "Trimestre 2", etc.
├── start_date (DATE)
├── end_date (DATE)
├── is_active (BOOLEAN)
├── created_at, updated_at

grades
├── id (BIGINT PK)
├── student_id (FK)
├── subject_id (FK)
├── grade_period_id (FK)
├── school_year_id (FK)
├── teacher_id (FK -> users)
├── score (DECIMAL 5,2) -- 0-20
├── grade_type (ENUM: 'test', 'exam', 'homework', 'participation') 
├── weight (DECIMAL 3,2, default: 1.0)
├── comments (TEXT)
├── graded_at (TIMESTAMP)
├── created_at, updated_at

grade_averages
├── id (BIGINT PK)
├── student_id (FK)
├── subject_id (FK)
├── grade_period_id (FK)
├── school_year_id (FK)
├── average (DECIMAL 5,2) -- calculated average
├── rank (INT) -- class rank for this subject
├── is_passed (BOOLEAN) -- based on passing grade (10/20)
├── updated_at

class_averages
├── id (BIGINT PK)
├── class_id (FK)
├── subject_id (FK)
├── grade_period_id (FK)
├── school_year_id (FK)
├── average (DECIMAL 5,2) -- class average for subject
├── highest_score (DECIMAL 5,2)
├── lowest_score (DECIMAL 5,2)
├── pass_rate (DECIMAL 5,2) -- percentage of students who passed

grade_appeals
├── id (BIGINT PK)
├── student_id (FK)
├── grade_id (FK)
├── subject_id (FK)
├── reason (TEXT)
├── status (ENUM: 'pending', 'approved', 'rejected')
├── response (TEXT)
├── reviewed_by (FK -> users)
├── reviewed_at (TIMESTAMP)
├── created_at, updated_at
```

### 1.2 Models
- `Subject` - Academic subjects
- `GradePeriod` - Grading periods (trimesters, terms)
- `Grade` - Individual student grades
- `GradeAverage` - Cached averages per student/subject
- `ClassAverage` - Cached class statistics
- `GradeAppeal` - Student grade appeals

### 1.3 Controllers & Routes

#### GradeController
```
GET    /api/grades                    # List grades with filters
POST   /api/grades                    # Create grade (teacher only)
GET    /api/grades/{grade}            # Get single grade
PUT    /api/grades/{grade}            # Update grade (teacher only)
DELETE /api/grades/{grade}            # Delete grade
GET    /api/grades/student/{student}  # Get student's grades
GET    /api/grades/statistics         # Class statistics
POST   /api/grades/bulk-upload        # CSV import
GET    /api/grades/export             # Export to CSV/PDF
```

#### SubjectController
```
GET    /api/subjects                  # List subjects
POST   /api/subjects                  # Create subject
PUT    /api/subjects/{subject}        # Update subject
DELETE /api/subjects/{subject}        # Delete subject
```

#### GradeAppealController
```
GET    /api/grade-appeals             # List appeals
POST   /api/grade-appeals             # Create appeal
PUT    /api/grade-appeals/{appeal}    # Update appeal (admin)
GET    /api/grade-appeals/my          # Student's appeals
```

### 1.4 Livewire Components
- **GradeListComponent** - Teacher/admin grade management
- **StudentGradesComponent** - Student view of grades and averages
- **SubjectManagementComponent** - Subject CRUD
- **ClassStatisticsComponent** - Class analytics and rankings
- **GradeAppealComponent** - Appeal submission and review

### 1.5 Features
- ✅ Weighted grade calculation
- ✅ Automatic average computation
- ✅ Class ranking by subject
- ✅ Grade filtering by period, subject, student
- ✅ CSV import/export
- ✅ Grade appeals workflow
- ✅ Statistics dashboards
- ✅ Pass/fail indicators
- ✅ PDF report generation

### 1.6 Permissions
- `grades.view` - View grades
- `grades.create` - Create grades
- `grades.edit` - Edit grades (own only for teachers)
- `grades.delete` - Delete grades
- `grades.export` - Export grades
- `grade_appeals.view` - View appeals
- `grade_appeals.submit` - Submit appeals
- `grade_appeals.review` - Review appeals

---

## 2. ATTENDANCE MODULE

### 2.1 Database Design

#### Tables
```
attendance_records
├── id (BIGINT PK)
├── student_id (FK)
├── class_id (FK)
├── school_year_id (FK)
├── attendance_date (DATE)
├── status (ENUM: 'present', 'absent', 'excused', 'late')
├── marked_by (FK -> users)
├── notes (TEXT)
├── created_at, updated_at

attendance_sessions
├── id (BIGINT PK)
├── class_id (FK)
├── school_year_id (FK)
├── session_date (DATE)
├── session_time (ENUM: 'morning', 'afternoon')
├── teacher_id (FK -> users)
├── status (ENUM: 'pending', 'submitted', 'approved')
├── created_at, updated_at

justifications
├── id (BIGINT PK)
├── attendance_record_id (FK)
├── student_id (FK)
├── reason (TEXT)
├── document_path (VARCHAR)
├── submitted_by (FK -> users)
├── approved_by (FK -> users)
├── status (ENUM: 'pending', 'approved', 'rejected')
├── approved_at (TIMESTAMP)
├── created_at, updated_at

absence_counters
├── id (BIGINT PK)
├── student_id (FK)
├── school_year_id (FK)
├── total_absences (INT)
├── excused_absences (INT)
├── unexcused_absences (INT)
├── late_arrivals (INT)
├── last_updated_at (TIMESTAMP)

absence_alerts
├── id (BIGINT PK)
├── student_id (FK)
├── school_year_id (FK)
├── absence_count (INT) -- threshold triggered
├── alert_type (ENUM: 'warning', 'critical')
├── is_acknowledged (BOOLEAN)
├── acknowledged_by (FK -> users)
├── acknowledged_at (TIMESTAMP)
├── created_at, updated_at
```

### 2.2 Models
- `AttendanceRecord` - Daily attendance entries
- `AttendanceSession` - Class attendance session
- `Justification` - Absence justification documents
- `AbsenceCounter` - Attendance summary per student
- `AbsenceAlert` - Alerts for excessive absences

### 2.3 Controllers & Routes

#### AttendanceController
```
GET    /api/attendance                     # List attendance
POST   /api/attendance/session             # Create session
GET    /api/attendance/session/{id}        # Get session
PUT    /api/attendance/session/{id}        # Update attendance
POST   /api/attendance/session/{id}/submit # Submit session

GET    /api/attendance/student/{student}   # Student attendance
GET    /api/attendance/class/{class}       # Class attendance
GET    /api/attendance/statistics          # Attendance stats
POST   /api/attendance/export              # Export report
```

#### JustificationController
```
GET    /api/justifications                 # List justifications
POST   /api/justifications                 # Submit justification
PUT    /api/justifications/{id}            # Update justification
GET    /api/justifications/pending         # Pending justifications (admin)
PUT    /api/justifications/{id}/approve    # Approve justification
```

### 2.4 Livewire Components
- **AttendanceMarkerComponent** - Real-time attendance marking
- **StudentAttendanceComponent** - Student attendance view
- **AttendanceStatisticsComponent** - Attendance analytics
- **JustificationComponent** - Manage justifications
- **AttendanceAlertComponent** - Alert management

### 2.5 Features
- ✅ Real-time attendance marking (per class/session)
- ✅ Bulk attendance import (CSV)
- ✅ Justification document uploads
- ✅ Automatic absence alerting (configurable thresholds)
- ✅ Attendance reports and analytics
- ✅ Parent notifications on high absences
- ✅ Attendance history and trends
- ✅ Excuse management workflow

### 2.6 Permissions
- `attendance.mark` - Mark attendance
- `attendance.view` - View attendance
- `attendance.edit` - Edit attendance
- `attendance.submit` - Submit session
- `justifications.view` - View justifications
- `justifications.submit` - Submit justification
- `justifications.approve` - Approve justifications
- `attendance.export` - Export attendance

---

## 3. BILLING MODULE

### 3.1 Database Design

#### Tables
```
fee_structures
├── id (BIGINT PK)
├── school_year_id (FK)
├── class_id (FK, nullable) -- if applies to specific class
├── name (VARCHAR 255)
├── description (TEXT)
├── amount (DECIMAL 12,2)
├── type (ENUM: 'tuition', 'registration', 'exam', 'uniforms')
├── is_required (BOOLEAN)
├── due_date (DATE)
├── is_active (BOOLEAN)
├── created_at, updated_at

invoices
├── id (BIGINT PK)
├── invoice_number (VARCHAR 50, UNIQUE)
├── student_id (FK)
├── school_year_id (FK)
├── issued_date (DATE)
├── due_date (DATE)
├── amount_due (DECIMAL 12,2)
├── amount_paid (DECIMAL 12,2, default: 0)
├── discount_amount (DECIMAL 12,2, default: 0)
├── status (ENUM: 'draft', 'issued', 'partial', 'paid', 'overdue', 'cancelled')
├── notes (TEXT)
├── issued_by (FK -> users)
├── created_at, updated_at

payments
├── id (BIGINT PK)
├── invoice_id (FK)
├── student_id (FK)
├── amount (DECIMAL 12,2)
├── payment_date (DATE)
├── payment_method (ENUM: 'cash', 'check', 'bank_transfer', 'online')
├── reference_number (VARCHAR 100)
├── receipt_number (VARCHAR 50)
├── recorded_by (FK -> users)
├── notes (TEXT)
├── created_at, updated_at

payment_plans
├── id (BIGINT PK)
├── student_id (FK)
├── school_year_id (FK)
├── total_amount (DECIMAL 12,2)
├── installments (INT)
├── installment_amount (DECIMAL 12,2)
├── start_date (DATE)
├── status (ENUM: 'active', 'completed', 'cancelled')
├── approved_by (FK -> users)
├── created_at, updated_at

payment_installments
├── id (BIGINT PK)
├── payment_plan_id (FK)
├── installment_number (INT)
├── due_date (DATE)
├── amount (DECIMAL 12,2)
├── amount_paid (DECIMAL 12,2, default: 0)
├── status (ENUM: 'pending', 'paid', 'overdue')
├── created_at, updated_at

fee_waivers
├── id (BIGINT PK)
├── student_id (FK)
├── school_year_id (FK)
├── fee_type (VARCHAR 100)
├── amount (DECIMAL 12,2)
├── reason (TEXT)
├── approved_by (FK -> users)
├── status (ENUM: 'pending', 'approved', 'rejected')
├── approved_at (TIMESTAMP)
├── created_at, updated_at

payment_transactions
├── id (BIGINT PK)
├── payment_id (FK)
├── transaction_id (VARCHAR 100, UNIQUE)
├── status (ENUM: 'pending', 'success', 'failed')
├── gateway (VARCHAR 100) -- 'stripe', 'paypal', etc.
├── response_data (JSON)
├── created_at, updated_at
```

### 3.2 Models
- `FeeStructure` - Fee types and amounts
- `Invoice` - Student invoices
- `Payment` - Payment records
- `PaymentPlan` - Installment plans
- `PaymentInstallment` - Individual installments
- `FeeWaiver` - Fee reductions/waivers
- `PaymentTransaction` - Online payment gateway transactions

### 3.3 Controllers & Routes

#### InvoiceController
```
GET    /api/invoices                    # List invoices
POST   /api/invoices                    # Create invoice
GET    /api/invoices/{invoice}          # Get invoice
PUT    /api/invoices/{invoice}          # Update invoice
DELETE /api/invoices/{invoice}          # Delete invoice

GET    /api/invoices/student/{student}  # Student invoices
GET    /api/invoices/pdf/{invoice}      # Download PDF
POST   /api/invoices/send/{invoice}     # Send email
```

#### PaymentController
```
POST   /api/payments                    # Record payment
GET    /api/payments                    # List payments
GET    /api/payments/{payment}          # Get payment
GET    /api/payments/receipt/{payment}  # Receipt
POST   /api/payments/refund/{payment}   # Refund payment
```

#### FeeStructureController
```
GET    /api/fee-structures              # List fee structures
POST   /api/fee-structures              # Create fee structure
PUT    /api/fee-structures/{id}         # Update
DELETE /api/fee-structures/{id}         # Delete
```

#### PaymentPlanController
```
GET    /api/payment-plans               # List plans
POST   /api/payment-plans               # Create plan
PUT    /api/payment-plans/{id}          # Update plan
GET    /api/payment-plans/{id}/status   # Plan status
```

#### FeeWaiverController
```
GET    /api/fee-waivers                 # List waivers
POST   /api/fee-waivers                 # Request waiver
PUT    /api/fee-waivers/{id}/approve    # Approve waiver
PUT    /api/fee-waivers/{id}/reject     # Reject waiver
```

### 3.4 Livewire Components
- **InvoiceListComponent** - Invoice management
- **PaymentRecorderComponent** - Record payments
- **StudentBillingComponent** - Student billing view
- **PaymentPlanComponent** - Plan management
- **FeeStructureComponent** - Fee configuration
- **BillingDashboardComponent** - Financial overview
- **FeeWaiverComponent** - Waiver requests

### 3.5 Features
- ✅ Automated invoice generation
- ✅ Payment recording (multiple methods)
- ✅ Flexible payment plans/installments
- ✅ Fee waivers and discounts
- ✅ Invoice PDF generation
- ✅ Email notifications
- ✅ Online payment gateway integration (stripe ready)
- ✅ Receipt generation
- ✅ Financial reports and analytics
- ✅ Payment reminders
- ✅ Refund management
- ✅ Overpayment credits

### 3.6 Permissions
- `billing.view` - View billing information
- `invoices.create` - Create invoices
- `invoices.edit` - Edit invoices
- `invoices.delete` - Delete invoices
- `payments.record` - Record payments
- `payments.refund` - Issue refunds
- `payment_plans.create` - Create payment plans
- `fee_waivers.request` - Request waivers
- `fee_waivers.approve` - Approve waivers
- `billing.export` - Export billing data

---

## 4. IMPLEMENTATION ROADMAP

### Phase 1: Grades Module (Week 1-2)
- [ ] Database migrations
- [ ] Models and relationships
- [ ] API endpoints
- [ ] Permission system
- [ ] Livewire components
- [ ] Views and UI
- [ ] Tests (50+ test cases)
- [ ] CSV import/export

### Phase 2: Attendance Module (Week 2-3)
- [ ] Database migrations
- [ ] Models and relationships
- [ ] Real-time attendance marking
- [ ] Justification workflow
- [ ] Alert system
- [ ] API endpoints
- [ ] Livewire components
- [ ] Views and UI
- [ ] Tests (40+ test cases)

### Phase 3: Billing Module (Week 3-4)
- [ ] Database migrations
- [ ] Models and relationships
- [ ] Invoice generation
- [ ] Payment recording
- [ ] Payment plan workflow
- [ ] API endpoints
- [ ] Livewire components
- [ ] PDF generation
- [ ] Email notifications
- [ ] Views and UI
- [ ] Tests (50+ test cases)

### Phase 4: Integration & Polish (Week 4-5)
- [ ] Cross-module integration
- [ ] Rate limiting application
- [ ] Comprehensive testing
- [ ] Documentation
- [ ] Performance optimization
- [ ] Security audit
- [ ] Merge to main

---

## 5. TESTING STRATEGY

### Unit Tests
- Model methods and relationships
- Service layer logic
- Helper functions

### Feature Tests
- API endpoints
- Authorization/permissions
- Data validation
- Business logic

### Integration Tests
- Cross-module interactions
- Livewire component interactions
- Payment processing workflows

### Test Coverage Target
- **Minimum: 80%** code coverage
- **Critical paths: 100%** coverage

---

## 6. KEY FEATURES SUMMARY

| Feature | Grades | Attendance | Billing |
|---------|--------|-----------|---------|
| CRUD Operations | ✅ | ✅ | ✅ |
| Real-time Updates | ✅ | ✅ | ✅ |
| Bulk Import/Export | ✅ | ✅ | ✅ |
| PDF Generation | ✅ | ✅ | ✅ |
| Notifications | ✅ | ✅ | ✅ |
| Analytics Dashboard | ✅ | ✅ | ✅ |
| Reporting | ✅ | ✅ | ✅ |
| Archive/History | ✅ | ✅ | ✅ |
| Role-based Access | ✅ | ✅ | ✅ |
| Audit Logging | ✅ | ✅ | ✅ |

---

## 7. TECHNOLOGY STACK

- **Backend**: Laravel 11, PHP 8.4
- **Frontend**: Livewire 4, Tailwind CSS
- **Database**: SQLite (dev), PostgreSQL/MySQL (prod)
- **Testing**: PHPUnit 12, Pest
- **PDF**: DomPDF/MPDF
- **Payments**: Stripe (optional integration)
- **Notifications**: Email, SMS (via Twilio)

---

## 8. SUCCESS CRITERIA

- ✅ All 3 modules fully implemented
- ✅ 80%+ code coverage
- ✅ All feature tests passing
- ✅ Zero security vulnerabilities
- ✅ Performance: API response < 500ms
- ✅ Comprehensive documentation
- ✅ User-friendly UI/UX
- ✅ Rate limiting active
- ✅ Pagination optimized

---

## 9. BRANCH INFORMATION

**Feature Branch**: `claude/grades-attendance-billing`

All work for these three modules will be done on this branch before merging to `claude/multi-client-branding-h1erae`.

---

## 10. NEXT STEPS

1. ✅ Branch created: `claude/grades-attendance-billing`
2. ⏳ Implement Grades Module
3. ⏳ Implement Attendance Module
4. ⏳ Implement Billing Module
5. ⏳ Integration testing
6. ⏳ Merge to main branch
7. ⏳ Create documentation

---

**Created**: June 27, 2026  
**Status**: Ready for Development
**Estimated Duration**: 4-5 weeks
