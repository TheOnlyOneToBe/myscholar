# Bridge Migrations Documentation

## Overview

Bridge migrations are responsible for linking inter-module dependencies and ensuring data consistency across the modular architecture. They handle foreign key constraints between modules while maintaining safety and flexibility.

## Architecture Pattern

Each bridge migration follows this pattern:

### 1. Safe Schema Alterations
All bridge migrations use `if (Schema::hasTable())` checks before attempting to alter tables. This ensures:
- **Concurrent Module Loading**: Multiple modules can be loaded in any order
- **Partial Installations**: Only required tables are modified
- **Safety**: No errors if tables don't exist in the current installation

### 2. Column Existence Checks
Before adding columns, we also check if they already exist using `if (!Schema::hasColumn())`. This provides:
- **Idempotency**: Safe to run multiple times
- **Compatibility**: Works with existing databases
- **Flexibility**: Supports different schema versions

### 3. Foreign Key Constraints
Bridge migrations establish relationships between modules while maintaining referential integrity:
```php
$table->foreign('school_year_id')
    ->references('id')
    ->on('school_years')
    ->onDelete('cascade');
```

## Current Bridges

### 1. Config ↔ Grades (2024_01_01_800503)
**Purpose**: Link school years to grades and academic records

**Tables Modified**:
- `grade_periods` - Adds school_year_id
- `grades` - Adds school_year_id
- `grade_averages` - Adds school_year_id (Grades module table)
- `class_averages` - Adds school_year_id
- `grade_appeals` - Adds school_year_id (Grades module table)

**Backwards Compatibility**: Also handles old table names (`averages_cache`, `appeals`)

### 2. Config ↔ Attendance (2024_01_01_800504)
**Purpose**: Link school years to attendance records

**Tables Modified**:
- `attendance_sessions` - Adds school_year_id
- `attendance_records` - Adds school_year_id
- `justifications` - Adds school_year_id
- `absence_counters` - Adds school_year_id
- `absence_alerts` - Adds school_year_id

### 3. Config ↔ Billing (2024_01_01_800505)
**Purpose**: Link school years to billing and payment records

**Tables Modified**:
- `fee_structures` - Adds school_year_id
- `invoices` - Adds school_year_id
- `payments` - Adds school_year_id
- `payment_plans` - Adds school_year_id
- `payment_installments` - Adds school_year_id
- `fee_waivers` - Adds school_year_id
- `payment_transactions` - Adds school_year_id

## Concurrent Module Loading

The bridge pattern ensures safe concurrent module loading through:

1. **Defensive Checks**: Each alteration is wrapped in table/column existence checks
2. **Order Independence**: Bridges can execute in any order
3. **Graceful Degradation**: Missing tables don't cause failures
4. **Idempotent Operations**: Safe to run migrations multiple times

### Example Safe Execution Patterns

**Pattern 1: All modules loaded together**
```
1. Config module migrations (creates school_years table)
2. Grades module migrations (creates grades, grade_periods, etc.)
3. Attendance module migrations (creates attendance_records, etc.)
4. Bridge migrations (links all with school_year_id)
   - Grades bridge ✓
   - Attendance bridge ✓
   - Billing bridge ✓
```

**Pattern 2: Selective module loading**
```
1. Config module migrations
2. Grades module migrations
3. Grades bridge only ✓
   (Attendance/Billing bridges skip gracefully)
```

**Pattern 3: Mixed loading order**
```
1. Config module
2. Attendance module
3. Grades module
4. All bridges execute safely (order independent) ✓
```

## Implementation Guidelines

When creating new bridges:

1. **Always use table existence checks**
```php
if (Schema::hasTable('table_name')) {
    Schema::table('table_name', function (Blueprint $table) {
        // Alterations here
    });
}
```

2. **Always use column existence checks**
```php
if (!Schema::hasColumn('table_name', 'column_name')) {
    // Add column
}
```

3. **Use descriptive timestamps**
- Core module bridges: `2024_01_01_800501` to `2024_01_01_800502`
- Feature module bridges: `2024_01_01_800503` to `2024_01_01_800510`
- Additional bridges: `2024_01_01_800511` onwards

4. **Include comprehensive documentation**
```php
/**
 * Bridge: Module1 ↔ Module2
 * Description of what this bridge links
 * Dependencies: List modules that must exist
 */
```

## Testing Bridges

### Test Fresh Installation
```bash
php artisan migrate:fresh --seed
```

### Test Partial Installation
```bash
# Disable some modules in config/modules.json
php artisan migrate:fresh
```

### Test Multiple Runs
```bash
php artisan migrate:rollback
php artisan migrate:refresh
php artisan migrate
```

## Best Practices

1. ✅ **Always wrap in table checks** - Defensive programming
2. ✅ **Test with partial module sets** - Ensure flexibility
3. ✅ **Document dependencies** - Clear module relationships
4. ✅ **Use consistent naming** - Predictable bridge naming
5. ✅ **Keep migrations simple** - Foreign keys only in bridges
6. ✅ **Never force table creation** - Rely on module migrations
7. ✅ **Test concurrent loading** - Multiple modules at once

## Troubleshooting

### Bridge Altering Non-existent Table
**Cause**: Module migration failed or wasn't loaded
**Solution**: Check module migrations are created and `config/modules.json` lists the module

### Circular Dependency Issues
**Cause**: Module A depends on B, B depends on A
**Solution**: Use bridges to break circular dependencies at the schema level

### Foreign Key Constraint Violations
**Cause**: Data was inserted before foreign key created
**Solution**: Ensure bridge migrations run before seeding data

## Future Extensions

As new modules are added, follow this bridge creation pattern:

- **Module Creation**: Register in `config/modules.json`
- **Module Implementation**: Create migrations for core tables
- **Bridge Creation**: Create `2024_01_01_800XXX_config_link_modulename.php`
- **Documentation**: Update this file with new bridge details
- **Testing**: Verify with concurrent module loading tests
