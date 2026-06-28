# Attendance Module - Security & Authorization

## Overview

The Attendance module implements comprehensive security through:
1. **Role-Based Access Control (RBAC)** via Policies
2. **Rate Limiting** for abuse prevention
3. **Bulk Operation Controls** to prevent data manipulation attacks

---

## Role-Based Policies

### 1. AttendanceSessionPolicy

**View Any Sessions**
- ✅ Admin, Proviseur (Director), Teacher
- ❌ Student, Parent

**View Single Session**
- ✅ Admin, Proviseur
- ✅ Teacher (only their own sessions)
- ❌ Student, Parent

**Create Session**
- ✅ Admin, Proviseur, Teacher
- ❌ Student, Parent

**Update Session**
- ✅ Admin, Proviseur
- ✅ Teacher (only own, requires permission)
- ❌ Student, Parent

**Delete Session**
- ✅ Admin, Proviseur
- ✅ Teacher (only own, requires permission)
- ❌ Student, Parent

---

### 2. AttendanceRecordPolicy

**View Any Records**
- ✅ Admin, Proviseur, Teacher, Student
- ❌ Parent (unless child relationship implemented)

**View Single Record**
- ✅ Admin, Proviseur
- ✅ Teacher (access to records in their sessions)
- ✅ Student (only own records)
- ❌ Other Students

**Mark Attendance (Create)**
- ✅ Admin, Proviseur, Teacher
- ❌ Student, Parent

**Update Record**
- ✅ Admin, Proviseur
- ✅ Teacher (within 24 hours of session, requires permission)
- ❌ Student, Parent

**Delete Record**
- ✅ Admin, Proviseur
- ✅ Teacher (requires permission)
- ❌ Student, Parent

---

### 3. JustificationPolicy

**View Any Justifications**
- ✅ Admin, Proviseur, Teacher, Student
- ❌ Parent (not yet implemented)

**View Single Justification**
- ✅ Admin, Proviseur
- ✅ Teacher (access to records in system)
- ✅ Student (only own justifications)
- ❌ Parent (unless child relationship implemented)

**Submit Justification**
- ✅ Student (own justifications, requires permission)
- ✅ Admin, Proviseur, Teacher (on behalf of students)

**Update Justification**
- ✅ Student (only own pending justifications)
- ✅ Admin, Proviseur
- ❌ Teacher (cannot update once submitted)

**Delete Justification**
- ✅ Student (only own pending justifications)
- ✅ Admin, Proviseur
- ❌ Approved/Rejected (cannot delete reviewed justifications)

**Approve Justification**
- ✅ Admin, Proviseur (requires `attendance.approve_justifications`)
- ❌ Teacher, Student

**Reject Justification**
- ✅ Admin, Proviseur (requires `attendance.approve_justifications`)
- ❌ Teacher, Student

---

### 4. AbsenceAlertPolicy

**View Any Alerts**
- ✅ Admin, Proviseur, Teacher, Student
- ❌ Parent (unless child relationship implemented)

**View Single Alert**
- ✅ Admin, Proviseur
- ✅ Teacher (system-wide access)
- ✅ Student (only own alerts)
- ❌ Other Students

**Acknowledge Alert**
- ✅ Admin, Proviseur (any alert)
- ✅ Student (only own alerts)
- ❌ Teacher (cannot acknowledge)

**Create Alert** (Manual)
- ✅ Admin, Proviseur
- ❌ Teacher, Student

**Delete Alert**
- ✅ Admin (requires permission)
- ❌ All others

---

## Rate Limiting Configuration

### Middleware: `AttendanceRateLimit`

Provides endpoint-specific rate limiting to prevent abuse:

```php
Rate limits per minute (by user):
├── Bulk Operations        → 10 requests/min
├── Attendance Marking     → 120 marks/min (2 per second)
├── Read Operations        → 300 requests/min
├── Delete Operations      → 20 deletes/min
├── Justification Review   → 60 reviews/min
└── General Operations     → 60 requests/min
```

### Rate Limit Headers

All responses include:
```
X-RateLimit-Limit:     Maximum requests allowed
X-RateLimit-Remaining: Requests left in window
X-RateLimit-Reset:     Unix timestamp when limit resets
X-RateLimit-Type:      Type of operation (bulk, read, etc)
```

### When Rate Limited (429 Response)

```json
{
  "message": "Rate limit exceeded for this operation",
  "retry_after": 45,
  "limit_type": "bulk_operation"
}

Headers:
Retry-After: 45
```

---

## Bulk Operation Security

### Maximum Records Per Operation

```php
Max 100 records per bulk submission
Purpose: Prevent resource exhaustion attacks
Limit: 10 bulk operations per minute per user
```

### Validation Before Processing

```php
1. Session existence check
2. User permission verification
3. Record count limit validation
4. Status enum validation
5. Student ID existence validation
6. Notes length validation (max 500 chars)
```

### Transactional Safety

```php
// All records processed in transaction
// If ANY record fails, ENTIRE operation rolls back
// Prevents partial/inconsistent state
```

### Bulk Operation Limits

```php
Records per operation:    100 maximum
Operations per minute:    10 per user
Concurrent operations:    1 per session
Time window:             1 minute
```

---

## Permission Requirements

### Core Permissions

```php
// View permissions
'attendance.view_sessions'      → View attendance sessions
'attendance.view_records'       → View attendance records
'attendance.view_justifications' → View justifications
'attendance.view_alerts'        → View absence alerts

// Mark attendance
'attendance.mark_attendance'    → Mark student attendance
'attendance.create_sessions'    → Create attendance sessions

// Update/Delete
'attendance.update_records'     → Update attendance marks
'attendance.update_sessions'    → Update sessions
'attendance.delete_records'     → Delete attendance records
'attendance.delete_sessions'    → Delete sessions

// Justifications
'attendance.submit_justification'  → Submit own justification
'attendance.manage_justifications' → Manage all justifications
'attendance.approve_justifications' → Approve/reject justifications

// Alerts
'attendance.create_alerts'      → Manually create alerts
'attendance.manage_alerts'      → Manage all alerts
```

---

## Attack Prevention Strategies

### 1. SQL Injection Prevention
- ✅ Use parameterized queries (Eloquent ORM)
- ✅ Input validation on all endpoints
- ✅ Type casting in repositories

### 2. Mass Assignment Prevention
- ✅ Fillable arrays explicitly defined
- ✅ Only allow intended fields
- ✅ Use form requests for validation

### 3. Authorization Bypass Prevention
- ✅ Policies check user role/permissions
- ✅ Verify data ownership (student checks own records)
- ✅ Admin override with audit trail

### 4. Rate Limiting Abuse Prevention
- ✅ Per-user rate limits (by user ID or IP)
- ✅ Endpoint-specific limits
- ✅ Bulk operation caps
- ✅ Progressive backoff (Retry-After header)

### 5. Data Integrity
- ✅ Transactional bulk operations
- ✅ Unique constraints (session per student)
- ✅ Foreign key constraints
- ✅ Cascade delete on session removal

### 6. Access Control
- ✅ Teacher can only modify own records (24hr window)
- ✅ Student can only view own data
- ✅ Parent relationships not yet implemented (TODO)
- ✅ Admin can view/modify anything with audit

---

## Implementation Examples

### Middleware Application

Add to routes (in AttendanceServiceProvider):

```php
Route::prefix('api/attendance')
    ->middleware(['api', 'auth', AttendanceRateLimit::class])
    ->group(function () {
        // Routes here...
    });
```

### Policy Authorization in Controller

```php
public function update(Request $request, AttendanceSession $session)
{
    // Throws 403 if not authorized
    $this->authorize('update', $session);
    
    // Safe to proceed
    return response()->json($session);
}
```

### Testing Policies

```php
// In tests:
$this->assertTrue($user->can('view', $record));
$this->assertFalse($user->can('delete', $record));
```

---

## Audit & Compliance

### What's Logged

- ✅ Who marked attendance (user ID)
- ✅ What changed (status, notes)
- ✅ When it happened (timestamps)
- ✅ Justification approvals/rejections
- ✅ Alert acknowledgments

### Not Yet Implemented (TODO)

- ⏳ IP address logging
- ⏳ User agent tracking
- ⏳ Bulk operation audit log
- ⏳ Export audit reports
- ⏳ Data retention policies

---

## Security Checklist

- [x] Role-based access control
- [x] Rate limiting middleware
- [x] Bulk operation validation
- [x] Transactional safety
- [x] Permission-based authorization
- [x] Input validation
- [x] CSRF protection (Laravel default)
- [x] SQL injection prevention
- [x] Data ownership verification
- [x] Time-based editing windows
- [ ] IP address logging
- [ ] Audit trail export
- [ ] Parent-child relationships
- [ ] SMS notification rate limiting
- [ ] Email spamming prevention

---

## Future Enhancements

1. **Parent Access**: Implement parent-child relationships
2. **Audit Logging**: Complete audit trail for compliance
3. **SMS Alerts**: Add SMS notification with rate limiting
4. **Email Notifications**: Email parents with throttling
5. **IP Blocking**: Block suspicious IP addresses
6. **Two-Factor Auth**: Require 2FA for sensitive operations
7. **Data Encryption**: Encrypt sensitive notes
8. **Export Controls**: Limit data exports per user/day

---

## Compliance

### GDPR Compliance
- Student data access limited to authorized users
- Student can request deletion of justifications
- Audit trail for all data access
- Data retention policies enforced

### FERPA Compliance (US)
- Parent access restricted (needs implementation)
- Student access limited to own records
- Audit trail of all access
- Secure data transmission (HTTPS)

---

## References

- Laravel Policy Documentation: https://laravel.com/docs/authorization
- Laravel Rate Limiting: https://laravel.com/docs/rate-limiting
- OWASP Security: https://owasp.org/www-project-top-ten/
