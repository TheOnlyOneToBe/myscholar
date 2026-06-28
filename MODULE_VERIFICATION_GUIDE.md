# Module Verification and Activation Guide

## 🔍 System Overview

This guide explains how to verify module activation and check dependencies before performing API requests and database operations.

---

## 📋 Table of Contents

1. [ModuleManager Service](#modulemanager-service)
2. [VerifiesModuleAccess Trait](#verifies-module-access-trait)
3. [VerifyModuleActivation Middleware](#verifymoduleactivation-middleware)
4. [API Endpoints](#api-endpoints)
5. [Usage Examples](#usage-examples)
6. [Error Handling](#error-handling)

---

## ModuleManager Service

The `ModuleManager` service provides comprehensive module verification capabilities.

### Location
`app/Services/ModuleManager.php`

### Key Methods

#### 1. Check if Module is Active
```php
$moduleManager = app(ModuleManager::class);

// Returns: bool
$active = $moduleManager->isModuleActive('Students');
```

#### 2. Verify All Dependencies Met
```php
// Returns: bool
$hasDeps = $moduleManager->hasDependencies('Billing');

// Get missing dependencies
$missing = $moduleManager->getMissingDependencies('Billing');
// Returns: ['Students', 'Config']
```

#### 3. Check Database Tables Exist
```php
// Returns: bool
$tablesExist = $moduleManager->moduleTablesExist('Grades');

// Get missing tables
$missingTables = $moduleManager->getMissingTables('Grades');
// Returns: ['grades', 'subjects']
```

#### 4. Get Complete Module Status
```php
// Returns: array
$status = $moduleManager->getModuleStatus('Attendance');

// Result:
[
    'name' => 'Attendance',
    'installed' => true,
    'active' => true,
    'version' => '1.0.0',
    'type' => 'business',
    'dependencies_met' => true,
    'missing_dependencies' => [],
    'tables_exist' => true,
    'missing_tables' => [],
    'service_provider' => true,
    'routes_registered' => true,
    'total_tables' => 6,
    'description' => '...',
]
```

#### 5. Verify Module Can Be Used
```php
// Single method to check everything
if ($moduleManager->canUseModule('Billing')) {
    // Safe to use Billing module
} else {
    $error = $moduleManager->getModuleError('Billing');
    // Returns: "Module 'Billing' is missing dependencies: Students, Config"
}
```

#### 6. Get Dependency Tree
```php
// See all dependencies recursively
$tree = $moduleManager->getDependencyTree('Billing');

// Result:
[
    'module' => 'Billing',
    'version' => '1.0.0',
    'dependencies' => [
        'Students' => [
            'module' => 'Students',
            'version' => '1.0.0',
            'dependencies' => [
                'Config' => [...],
            ]
        ],
        'Config' => [
            'module' => 'Config',
            'version' => '1.0.0',
            'dependencies' => []
        ]
    ]
]
```

---

## VerifiesModuleAccess Trait

Use this trait in your controllers to automatically verify module access before operations.

### Location
`app/Traits/VerifiesModuleAccess.php`

### How to Use

#### In Your Controller
```php
namespace Modules\Billing\Controllers;

use App\Traits\VerifiesModuleAccess;
use Illuminate\Http\JsonResponse;

class InvoiceController
{
    use VerifiesModuleAccess;

    public function index(): JsonResponse
    {
        // Verify module before processing
        $moduleError = $this->verifyModuleAccess('Billing');
        if ($moduleError) {
            return $moduleError; // Returns 503 error response
        }

        // Safe to query database
        $invoices = Invoice::all();

        return response()->json(['data' => $invoices]);
    }
}
```

### Available Methods

#### Verify Module Access
```php
// Checks: Installation, Dependencies, Database Tables
$error = $this->verifyModuleAccess('Students');
if ($error) return $error; // JsonResponse
```

#### Verify Table Exists
```php
// Checks if single table exists
$error = $this->verifyTableExists('invoices');
if ($error) return $error;
```

#### Verify Multiple Tables Exist
```php
// Checks if multiple tables exist
$error = $this->verifyTablesExist(['invoices', 'payments', 'students']);
if ($error) return $error;
```

#### Verify Column Exists
```php
// Checks if column exists in table (bridge verification)
$error = $this->verifyColumnExists('invoices', 'student_id');
if ($error) return $error;
```

#### Verify Bridge Link
```php
// Specialized check for bridge foreign key columns
$error = $this->verifyBridgeLink('invoices', 'student_id');
if ($error) return $error; // Returns 503 if bridge not installed
```

---

## VerifyModuleActivation Middleware

Automatically checks module activation on API requests.

### Location
`app/Http/Middleware/VerifyModuleActivation.php`

### How It Works

The middleware automatically:
1. Extracts module name from URL (e.g., `/api/students/...` → `Students`)
2. Verifies module is active and ready
3. Returns 503 error if module unavailable
4. Stores module info in `$request->moduleInfo`

### Register in Kernel

To use the middleware, register it in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... other middleware
    'verify.module' => \App\Http\Middleware\VerifyModuleActivation::class,
];
```

Then apply to routes:

```php
Route::middleware(['auth:sanctum', 'verify.module'])
    ->prefix('api/students')
    ->group(function () {
        // Routes automatically verified
    });
```

---

## API Endpoints

### System Status Endpoints

#### 1. List All Modules Status
```
GET /api/system/modules
```

Response:
```json
{
    "data": {
        "Auth": {
            "name": "Auth",
            "installed": true,
            "active": true,
            "dependencies_met": true,
            "tables_exist": true
        },
        "Billing": {
            "name": "Billing",
            "installed": true,
            "active": true,
            "dependencies_met": true,
            "tables_exist": true
        }
    },
    "total_modules": 11,
    "active_modules": 11,
    "fully_operational": 11
}
```

#### 2. Get Module Status
```
GET /api/system/modules/{module}/status
```

Response:
```json
{
    "data": {
        "name": "Billing",
        "installed": true,
        "active": true,
        "version": "1.0.0",
        "type": "business",
        "dependencies_met": true,
        "missing_dependencies": [],
        "tables_exist": true,
        "missing_tables": [],
        "service_provider": true,
        "routes_registered": true,
        "total_tables": 7,
        "description": "..."
    }
}
```

#### 3. Get Module Dependencies
```
GET /api/system/modules/{module}/dependencies
```

Response:
```json
{
    "data": {
        "module": "Billing",
        "version": "1.0.0",
        "dependencies": {
            "Students": {
                "module": "Students",
                "version": "1.0.0",
                "dependencies": {
                    "Config": {...}
                }
            },
            "Config": {
                "module": "Config",
                "version": "1.0.0",
                "dependencies": {}
            }
        }
    }
}
```

#### 4. Verify Module Ready
```
GET /api/system/modules/{module}/verify
```

Response (200 OK):
```json
{
    "module": "Billing",
    "can_use": true,
    "error": null,
    "status": {...}
}
```

Response (503 Service Unavailable):
```json
{
    "module": "Billing",
    "can_use": false,
    "error": "Module 'Billing' is missing dependencies: Students",
    "status": {...}
}
```

#### 5. System Health Check
```
GET /api/system/modules/health
```

Response (200 OK):
```json
{
    "data": {
        "healthy": true,
        "modules_installed": 11,
        "modules_active": 11,
        "modules_fully_operational": 11,
        "issues": []
    }
}
```

Response (503 Service Unavailable):
```json
{
    "data": {
        "healthy": false,
        "modules_installed": 11,
        "modules_active": 10,
        "modules_fully_operational": 9,
        "issues": [
            {
                "module": "Billing",
                "type": "missing_dependencies",
                "missing": ["Students"]
            }
        ]
    }
}
```

---

## Usage Examples

### Example 1: Billing Controller with Verification

```php
namespace Modules\Billing\Controllers;

use App\Traits\VerifiesModuleAccess;
use Illuminate\Http\JsonResponse;
use Modules\Billing\Models\Invoice;

class InvoiceController
{
    use VerifiesModuleAccess;

    public function index(): JsonResponse
    {
        // Method 1: Verify entire module
        if ($error = $this->verifyModuleAccess('Billing')) {
            return $error;
        }

        // Method 2: Verify specific tables
        if ($error = $this->verifyTablesExist(['invoices', 'students'])) {
            return $error;
        }

        // Method 3: Verify bridge link
        if ($error = $this->verifyBridgeLink('invoices', 'student_id')) {
            return $error;
        }

        // All verifications passed, safe to query
        $invoices = Invoice::with('student')->paginate();

        return response()->json([
            'data' => $invoices->items(),
            'pagination' => $invoices->paginate(),
        ]);
    }
}
```

### Example 2: Service Layer with Verification

```php
namespace Modules\Billing\Services;

use App\Services\ModuleManager;

class BillingService
{
    public function __construct(
        private ModuleManager $moduleManager
    ) {}

    public function createInvoice(array $data)
    {
        // Verify module before operations
        if (!$this->moduleManager->canUseModule('Billing')) {
            throw new \Exception(
                $this->moduleManager->getModuleError('Billing')
            );
        }

        // Safe to proceed
        return Invoice::create($data);
    }
}
```

### Example 3: Route with Middleware

```php
// In module routes file
Route::middleware(['auth:sanctum', 'verify.module'])
    ->prefix('api/billing')
    ->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::post('/invoices', [InvoiceController::class, 'store']);
        // ... more routes
    });
```

---

## Error Handling

### Error Response Codes

| Code | Meaning | Cause |
|------|---------|-------|
| 200 | OK | Module fully operational |
| 404 | Not Found | Module not installed |
| 503 | Service Unavailable | Module not ready (missing deps/tables) |

### Error Response Format

```json
{
    "error": "Error type",
    "message": "Human readable message",
    "module": "Module name",
    "missing_dependencies": ["Dep1", "Dep2"],
    "missing_tables": ["table1", "table2"],
    "missing_bridge_column": "foreign_key_column"
}
```

### Common Errors

#### Missing Dependencies
```json
{
    "error": "Missing dependencies",
    "message": "Module 'Billing' requires: Students, Config",
    "missing_dependencies": ["Students", "Config"]
}
```

#### Missing Database Tables
```json
{
    "error": "Database tables not found",
    "message": "Module 'Billing' is missing tables: invoices, payments",
    "missing_tables": ["invoices", "payments"]
}
```

#### Bridge Not Installed
```json
{
    "error": "Module bridge not installed",
    "message": "Bridge linking is not properly installed for 'invoices' module",
    "table": "invoices",
    "missing_bridge_column": "student_id"
}
```

---

## 🚀 Best Practices

1. **Always Verify Before Queries**
   ```php
   // ✅ Good
   if ($error = $this->verifyModuleAccess('Billing')) {
       return $error;
   }
   $invoices = Invoice::all();

   // ❌ Bad
   $invoices = Invoice::all(); // May fail if module not ready
   ```

2. **Use Trait in All Controllers**
   ```php
   // ✅ Good - Use trait for consistency
   use VerifiesModuleAccess;

   // ❌ Bad - Repeat code in every controller
   $moduleManager = app(ModuleManager::class);
   ```

3. **Cache Module Status**
   - ModuleManager automatically caches status for 1 hour
   - Call `$moduleManager->clearCache()` after installing new modules

4. **Check Specific Requirements**
   ```php
   // ✅ Good - Check what you actually need
   if (!$this->verifyBridgeLink('invoices', 'student_id')) {
       // Only students linked invoices

   // ❌ Bad - Check everything when not needed
   if ($error = $this->verifyModuleAccess('Grades')) {
       // Don't need Grades for this operation
   }
   ```

5. **Provide User-Friendly Errors**
   ```php
   // ✅ Good - Clear message
   $error = $this->moduleManager->getModuleError('Billing');
   // "Module 'Billing' requires: Students, Config"

   // ❌ Bad - Cryptic error
   // Database error: Unknown column 'student_id'
   ```

---

## 🔧 Administration Commands

### Check Module Status (CLI)

Create an artisan command:

```bash
php artisan module:status

# Output:
# Module             Status      Dependencies    Tables
# ───────────────────────────────────────────────────────
# Auth               ✅ Active   ✓ Met          ✓ Present
# Billing            ✅ Active   ✓ Met          ✓ Present
# Students           ✅ Active   ✓ Met          ✓ Present
```

### Check System Health

```bash
curl http://localhost/api/system/modules/health

# Returns 200 if healthy, 503 if issues
```

### Verify Specific Module

```bash
curl http://localhost/api/system/modules/Billing/verify

# Returns 200 if ready, 503 with error message if not
```

---

## 📚 Related Documentation

- See `MODULES_STRUCTURE.md` for complete module structure
- See `BRIDGES.md` for bridge dependency details
- See `module.json` in each module for module configuration
