# Audit Module Documentation

## Overview

The Audit module provides comprehensive logging and monitoring capabilities for the MyScholar system. It tracks all user actions, system events, errors, and HTTP requests, providing administrators with detailed insights into system usage and security.

**Module Type:** Core  
**Version:** 1.0.0  
**Status:** Production Ready  
**Last Updated:** 2026-06-27

---

## Features

### 1. Comprehensive Logging
- ✅ User action tracking (create, read, update, delete, etc.)
- ✅ System event logging (login, logout, permission changes)
- ✅ HTTP request/response logging
- ✅ Error and exception tracking
- ✅ Application crash logging

### 2. Advanced Filtering
- ✅ Filter by user
- ✅ Filter by action type
- ✅ Filter by severity level (info, warning, error, critical)
- ✅ Filter by entity type
- ✅ Date range filtering
- ✅ Full-text search across logs

### 3. Dashboard & Analytics
- ✅ Real-time statistics dashboard
- ✅ Error tracking and analysis
- ✅ Most active users report
- ✅ Most accessed routes report
- ✅ Error rate calculation
- ✅ Top errors listing

### 4. Data Management
- ✅ Log viewing and detail inspection
- ✅ Export logs as CSV
- ✅ Log deletion (admin only)
- ✅ Pagination support

### 5. Security Features
- ✅ Permission-based access control
- ✅ IP address tracking
- ✅ User agent logging
- ✅ Change tracking (old vs. new values)
- ✅ Stack trace capture for errors

---

## Database Schema

### AuditLog Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Primary key |
| action | VARCHAR(50) | Action type (create, update, delete, etc.) |
| entity_type | VARCHAR(100) | Type of entity affected |
| entity_id | BIGINT | ID of affected entity |
| user_id | BIGINT FK | User who performed action |
| changes | JSON | Before/after values for updates |
| ip_address | VARCHAR(50) | Client IP address |
| user_agent | TEXT | Client user agent |
| method | VARCHAR(10) | HTTP method (GET, POST, etc.) |
| url | TEXT | Request URL |
| http_status | INT | HTTP response status |
| error_message | TEXT | Error/exception message |
| stack_trace | TEXT | Full stack trace for errors |
| severity | ENUM | Log severity (info, warning, error, critical) |
| metadata | JSON | Additional contextual data |
| created_at | TIMESTAMP | When action occurred |
| updated_at | TIMESTAMP | When log was last updated |

### DeletedRecord Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Primary key |
| entity_type | VARCHAR(100) | Type of deleted entity |
| entity_id | BIGINT | ID of deleted entity |
| user_id | BIGINT FK | User who deleted |
| deleted_data | JSON | Complete data before deletion |
| restored | BOOLEAN | Whether record was restored |
| created_at | TIMESTAMP | Deletion timestamp |

---

## Models

### AuditLog Model

```php
use Modules\Audit\Models\AuditLog;

// Find a log
$log = AuditLog::find($id);

// Get logs by user
$logs = AuditLog::byUser(1)->get();

// Get logs by action
$logs = AuditLog::byAction('update')->get();

// Get error logs
$errors = AuditLog::errors()->get();

// Get logs by date range
$logs = AuditLog::dateRange($from, $to)->get();

// Get recent logs (last 24 hours)
$logs = AuditLog::recent(24)->get();

// Get HTTP errors
$errors = AuditLog::httpErrors()->get();

// Check if log is error
if ($log->isError()) { ... }

// Check if log is critical
if ($log->isCritical()) { ... }

// Get changed fields with old/new values
$changes = $log->getChangedFields();
```

### DeletedRecord Model

```php
use Modules\Audit\Models\DeletedRecord;

// Get all deleted records of a type
$records = DeletedRecord::byEntityType('user')->get();

// Get records deleted by user
$records = DeletedRecord::byUser(1)->get();

// Check if record was restored
if ($record->restored) { ... }
```

---

## Livewire Components

### AuditLogComponent

Comprehensive audit log viewer with filtering, searching, and exporting.

**Location:** `modules/Audit/Livewire/AuditLogComponent.php`  
**View:** `modules/Audit/Resources/views/livewire/audit-log-component.blade.php`

**Features:**
- Table view with pagination (25 per page)
- Multiple filter options
- Full-text search
- Sort by timestamp, action, severity
- Quick filters (today, this week, this month, errors only)
- Detail view modal
- CSV export
- Delete logs (admin only)

**Usage:**

```blade
<livewire:audit.audit-log-component />
```

**Properties:**
- `filterAction` - Filter by action type
- `filterUser` - Filter by user ID
- `filterSeverity` - Filter by severity
- `filterFromDate` - Start date for range
- `filterToDate` - End date for range
- `filterEntityType` - Filter by entity type
- `searchQuery` - Full-text search

**Methods:**
- `resetFilters()` - Clear all filters
- `viewDetail(logId)` - Show log details
- `closeDetail()` - Close detail view
- `sortBy(column)` - Sort by column
- `filterToday()` - Quick filter
- `filterThisWeek()` - Quick filter
- `filterThisMonth()` - Quick filter
- `filterErrorsOnly()` - Quick filter
- `filterCriticalOnly()` - Quick filter
- `deleteLog(logId)` - Delete a log
- `exportLogs()` - Export as CSV

### AuditDashboardWidget

Dashboard widget showing key metrics and statistics.

**Location:** `modules/Audit/Livewire/AuditDashboardWidget.php`  
**View:** `modules/Audit/Resources/views/livewire/audit-dashboard-widget.blade.php`

**Features:**
- Total logs count
- Today's logs count
- Recent errors (24h)
- Critical errors count
- Failed requests count
- Error rate percentage
- Top 5 errors
- Top 5 most active users
- Top 5 most accessed routes
- Auto-refresh capability

**Usage:**

```blade
<livewire:audit.audit-dashboard-widget />
```

**Methods:**
- `refresh()` - Reload dashboard data

---

## Routes

### Web Routes

```php
// View audit logs
GET /audit/logs                    → AuditLogComponent
  Middleware: auth, can:audit.view
```

### API Routes

```php
// Get audit logs
GET /api/audit/logs                → AuditLogController@index
  Parameters: action, user_id, severity, from_date, to_date, entity_type

// Get single log
GET /api/audit/logs/{id}          → AuditLogController@show

// Delete logs
DELETE /api/audit/logs/{id}       → AuditLogController@destroy
  Requires: can:audit.delete

// Export logs
GET /api/audit/logs/export        → AuditLogController@export
  Requires: can:audit.export
```

---

## Permissions

All audit permissions are defined in `permissions.json`:

| Permission | Description | Default Roles |
|------------|-------------|---|
| `audit.view` | View audit logs | admin, proviseur |
| `audit.view_errors` | View errors and crashes | admin, proviseur |
| `audit.view_stats` | View audit statistics | admin, proviseur |
| `audit.export` | Export audit logs | admin |
| `audit.delete` | Delete audit logs | admin |
| `audit.monitoring` | Real-time monitoring | admin |

**Usage in Routes:**

```php
Route::middleware('can:audit.view')->get('/audit/logs', ...);
Route::middleware('can:audit.delete')->delete('/audit/logs/{id}', ...);
```

**Usage in Controllers:**

```php
public function destroy(AuditLog $log)
{
    $this->authorize('audit.delete', $log);
    $log->delete();
}
```

---

## Services

### AuditService

Handles all audit logging operations.

**Location:** `modules/Audit/Services/AuditService.php`

**Methods:**

```php
use Modules\Audit\Services\AuditService;

$auditService = app(AuditService::class);

// Log user action
$auditService->logAction(
    action: 'update',
    entityType: 'user',
    entityId: 1,
    userId: 2,
    changes: [
        'old_values' => ['email' => 'old@example.com'],
        'new_values' => ['email' => 'new@example.com']
    ]
);

// Log HTTP request
$auditService->logHttpRequest(
    method: 'POST',
    url: '/api/users',
    status: 201,
    userId: 1
);

// Log error
$auditService->logError(
    message: 'Database connection failed',
    severity: 'critical',
    stackTrace: $exception->getTraceAsString()
);

// Log system event
$auditService->logSystemEvent(
    action: 'login',
    entityType: 'auth',
    userId: 1
);
```

---

## Translations

### Available Keys

**audit.labels:**
- audit_logs, user_activity, statistics, crashes
- action, entity_type, severity, timestamp
- ip_address, method, url, status_code
- error_message, changes, old_values, new_values

**audit.actions:**
- create, update, delete, login, logout
- login_failed, crash, http_request, error
- permission_denied

**audit.severity_levels:**
- info, warning, error, critical

**audit.entity_types:**
- user, system, auth, route, school_info
- school_year, student, grade, attendance, class, invoice

**audit.dashboard:**
- total_logs, today_logs, recent_errors
- crash_count, failed_requests, error_rate

**audit.filters:**
- by_action, by_user, by_severity, by_date_range
- by_status, by_entity_type, errors_only, http_errors_only

### Usage

```blade
{{ __('audit.labels.audit_logs') }}
{{ __('audit.actions.create') }}
{{ __('audit.severity_levels.critical') }}
{{ __('audit.dashboard.total_logs') }}
```

---

## Example Workflows

### 1. View Audit Logs

Navigate to `/audit/logs` to access the comprehensive audit log viewer:

1. Enter search terms to find specific logs
2. Use filters to narrow down results
3. Click quick filter buttons (Today, This Week, etc.)
4. Click a log row to see detailed information
5. Export filtered logs as CSV for reporting

### 2. Monitor System Health

Use the audit dashboard widget to:

1. Track total and daily log counts
2. Monitor error and critical issues
3. Identify most active users
4. Find problematic routes with high error rates
5. Investigate top errors

### 3. Investigate User Activity

To find what a specific user did:

1. Filter logs by user
2. Optionally filter by date range
3. Click on any log for full details
4. View changed fields to see what was modified

### 4. Track Data Changes

To see what changed in a record:

1. Filter logs by entity type (e.g., "Student")
2. Filter by action "update"
3. Click detail to view old vs. new values
4. Export for compliance/audit purposes

### 5. Troubleshoot Errors

To find and fix application issues:

1. Use "Show Errors Only" quick filter
2. Look at error messages and stack traces
3. Group by route to find problematic endpoints
4. Check timestamp to correlate with issues

---

## Security Considerations

### What Gets Logged

✅ All user actions (CRUD operations)  
✅ Authentication events (login, logout, failed attempts)  
✅ Authorization failures (permission denied)  
✅ System errors and crashes  
✅ HTTP requests and responses  
✅ Data changes (before/after values)  
✅ User IP addresses and user agents  

### What Should NOT Be Logged

❌ User passwords (never logged)  
❌ Credit card numbers  
❌ API tokens/secrets  
❌ Personal identification numbers  

**Implementation:** Sensitive fields should be excluded using model accessors.

### Data Retention

Audit logs should be retained for:
- **Normal logs:** 90 days
- **Error logs:** 1 year
- **Critical logs:** Indefinitely

Implement log archival/deletion policies based on your compliance requirements.

---

## Performance Optimization

### Indexing

Create indexes on frequently queried columns:

```sql
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_audit_logs_severity ON audit_logs(severity);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);
CREATE INDEX idx_audit_logs_entity_type ON audit_logs(entity_type);
```

### Pagination

The audit log component uses pagination with 25 logs per page to avoid loading too much data at once.

### Archival Strategy

For large deployments:
1. Archive old logs to separate table monthly
2. Create views to query both active and archived logs
3. Implement automatic cleanup policies

---

## Testing

### Test Audit Logging

```php
use Modules\Audit\Models\AuditLog;

public function test_user_action_is_logged()
{
    // Perform action
    $user = User::create([...]);

    // Verify log exists
    $this->assertDatabaseHas('audit_logs', [
        'action' => 'create',
        'entity_type' => 'user',
        'entity_id' => $user->id,
    ]);
}
```

### Test Filtering

```php
public function test_filter_logs_by_user()
{
    $logs = AuditLog::byUser(1)->get();
    $this->assertTrue($logs->every(fn($log) => $log->user_id === 1));
}
```

---

## Troubleshooting

### Q: Not seeing audit logs in database?
**A:** Check if the audit middleware is registered and that logging is enabled in the service provider.

### Q: Audit logs are too large?
**A:** Implement retention policies and archive old logs. Consider excluding certain high-volume entity types.

### Q: Performance is slow?
**A:** Add database indexes and use pagination. Archive old logs to separate table.

### Q: Want to exclude certain actions?
**A:** Modify the AuditService to skip logging for specific entity types or actions.

---

## Related Modules

- **Auth Module** - Provides user tracking for audit logs
- **Config Module** - System-wide settings for audit retention
- **Notifications Module** - Alert on critical audit events

---

## API Reference

See `API_REFERENCE.md` for detailed endpoint documentation.

---

## Future Enhancements

- [ ] Real-time event streaming via WebSocket
- [ ] Advanced analytics and reports
- [ ] Log retention policies UI
- [ ] Automated alerts for suspicious activity
- [ ] Audit log visualization (charts, graphs)
- [ ] Compliance report generation (GDPR, HIPAA)
- [ ] Log encryption for sensitive data

---

## Support & Contributing

For issues or feature requests, please contact the MyScholar development team.

---

**Last Updated:** 2026-06-27  
**Version:** 1.0.0  
**Author:** MyScholar Development Team
