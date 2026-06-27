# Configuration Module (CONFIG)

Complete guide to the MyScholar Configuration Module, which handles school branding, system settings, and school year management.

## Overview

The Config module is a core module that manages:
- **School Information** - School name, logo, address, contact details, and administrative info
- **System Settings** - Global configuration parameters (timezone, currency, date format, etc.)
- **School Years** - Academic year management with activation and session switching
- **Permissions** - Fine-grained access control for configuration operations

## Database Schema

### school_info Table

Stores core information about the school (typically 1 record per installation).

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | |
| name | VARCHAR(255) | School name |
| acronym | VARCHAR(50) NULL | School acronym/abbreviation |
| motto | VARCHAR(255) NULL | School motto/slogan |
| logo_path | VARCHAR(500) NULL | Path to logo file |
| school_type | ENUM | Type: public, prive, confessionnel |
| address | VARCHAR(255) NULL | Street address |
| city | VARCHAR(100) NULL | City name |
| region | VARCHAR(100) NULL | Region/State |
| phone | VARCHAR(30) NULL | Contact phone |
| email | VARCHAR(255) NULL | Contact email |
| website | VARCHAR(255) NULL | School website URL |
| po_box | VARCHAR(50) NULL | Postal box number |
| approval_number | VARCHAR(100) NULL | Official approval/license number |
| creation_decree | VARCHAR(255) NULL | Founding decree |
| founder_name | VARCHAR(255) NULL | Founder name |
| director_name | VARCHAR(255) NULL | Current director name |
| foundation_year | YEAR NULL | Year school was founded |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### system_settings Table

Key-value store for system-wide configuration parameters.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | |
| key | VARCHAR(255) UNIQUE | Setting key |
| value | TEXT NULL | Setting value (JSON serializable) |
| type | ENUM | Type: string, integer, boolean, json |
| group | VARCHAR(100) DEFAULT 'general' | Logical grouping |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Default Settings:**
- `timezone` = "Africa/Douala"
- `currency` = "FCFA"
- `date_format` = "d/m/Y"
- `language` = "fr"
- `max_students_per_class` = 45

### school_years Table

Manages academic years with activation and session tracking.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | |
| name | VARCHAR(255) | Display name (e.g., "2024-2025") |
| start_year | INT | Start year number |
| end_year | INT | End year number |
| start_date | DATE | First day of academic year |
| end_date | DATE | Last day of academic year |
| description | TEXT NULL | Optional description |
| is_active | BOOLEAN | Currently active academic year |
| is_locked | BOOLEAN | Locked for modifications |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Business Rules:**
- Only one school year can be active (`is_active = true`) at a time
- Active year cannot be deleted
- Locked years cannot be modified
- Cannot delete years in the future

## Permission System

### Defined Permissions

| Permission ID | Default Roles | Description |
|---------------|---------------|-------------|
| `config.view` | admin, directeur, censeur | View configuration info |
| `config.edit` | admin, directeur | Modify configuration |
| `config.school_info.edit` | admin, directeur | Edit school info |
| `config.school_info.logo` | admin, directeur | Upload/change logo |
| `config.settings.view` | admin, directeur, censeur | View system settings |
| `config.settings.edit` | admin | Edit system settings |
| `config.school_year.view` | admin, directeur, censeur | View school years |
| `config.school_year.create` | admin | Create new school year |
| `config.school_year.edit` | admin, directeur | Modify school year |
| `config.school_year.delete` | admin | Delete school year |
| `config.school_year.switch` | admin, directeur | Change session year |

## User Interfaces

### Web Routes

| Route | Component | Permission | Description |
|-------|-----------|-----------|-------------|
| `/config` | DetailComponent | config.view | Main configuration interface |
| `/config/school-years` | SchoolYearComponent | config.school_year.view | School year management |

### API Endpoints

All API endpoints require authentication (`auth` middleware).

#### School Information

```
GET    /api/config/school              → config.view
PUT    /api/config/school              → config.school_info.edit
POST   /api/config/school/logo         → config.school_info.logo
```

#### System Settings

```
GET    /api/config/settings            → config.view
PUT    /api/config/settings            → config.settings.edit
GET    /api/config/system-settings     → config.settings.view
GET    /api/config/system-settings/{key} → config.settings.view
POST   /api/config/system-settings     → config.settings.edit
PUT    /api/config/system-settings/{id} → config.settings.edit
DELETE /api/config/system-settings/{id} → config.settings.edit
PUT    /api/config/system-settings/bulk/update → config.settings.edit
```

#### School Years

```
GET    /api/config/school-years        → config.school_year.view
GET    /api/config/school-years/{id}   → config.school_year.view
GET    /api/config/school-years/current → config.school_year.view
POST   /api/config/school-years        → config.school_year.create
PUT    /api/config/school-years/{id}   → config.school_year.edit
POST   /api/config/school-years/{id}/activate → config.school_year.edit
DELETE /api/config/school-years/{id}   → config.school_year.delete
```


## Components

### DetailComponent

**Location:** `modules/Config/Livewire/DetailComponent.php`

Main configuration interface for editing school information and viewing system settings.

**Features:**
- Display and edit school information
- View system settings
- Toggle edit mode
- Logo management
- Responsive design with dark mode support

**Key Methods:**
- `mount()` - Load school info and settings
- `toggleEditMode()` - Switch between view/edit modes
- `updateSchoolInfo()` - Save school info changes
- `getSystemSetting(key, default)` - Get setting value

### SchoolYearComponent

**Location:** `modules/Config/Livewire/SchoolYearComponent.php`

Complete school year management interface.

**Features:**
- Display current active year
- Display session year (currently selected for operations)
- CRUD operations for school years
- Activate/switch year
- Form validation
- Permission-based button visibility

**Key Methods:**
- `mount()` - Initialize years and session
- `toggleForm()` - Show/hide form
- `createYear()` - Create new year
- `updateYear()` - Modify existing year
- `deleteYear()` - Remove year (if not active)
- `activateYear()` - Set as active
- `switchSession()` - Change session year

### FooterComponent

**Location:** `modules/Config/Livewire/FooterComponent.php`

Displays footer information and school details.

## Controllers

### SchoolInfoController

Handles school information API operations.

**Methods:**
- `show()` - Get school info
- `update()` - Update school info
- `uploadLogo()` - Upload school logo
- `settings()` - Get system settings
- `updateSettings()` - Update multiple settings

### SystemSettingController

Manages system settings CRUD operations.

**Methods:**
- `index()` - List all settings
- `getByGroup(group)` - Get settings by group
- `getSetting(key)` - Get single setting
- `store()` - Create setting
- `update()` - Modify setting
- `destroy()` - Delete setting
- `bulkUpdate()` - Update multiple settings

### SchoolYearController

Manages school year operations.

**Methods:**
- `index()` - List all years
- `show(SchoolYear)` - Get year details
- `store()` - Create year
- `update(SchoolYear)` - Modify year
- `destroy(SchoolYear)` - Delete year
- `activate(SchoolYear)` - Set as active

## Models

### SchoolInfo

Eloquent model for school information.

**Key Methods:**
- `current()` - Get school record (scoped to first)
- `hasLogo()` - Check if logo exists
- `getFullAddress()` - Format address fields

**Casting:**
- `school_type` cast as enum

### SystemSetting

Key-value setting model with helpers.

**Key Methods:**
- `get(key, default)` - Get setting value
- `set(key, value, type, group)` - Create/update setting
- `getByGroup(group)` - Get settings by group

### SchoolYear

Academic year model with status tracking.

**Scopes:**
- `active()` - Get currently active year
- `notLocked()` - Exclude locked years

**Methods:**
- Business rule validation in model events

## Services

### SchoolYearSessionService

Manages school year session state (which year is currently selected for operations).

**Key Methods:**
- `initializeSession()` - Set up session if needed
- `getActiveYear()` - Get current session year
- `setActiveYear(SchoolYear)` - Change session year
- `canModifyYear(SchoolYear)` - Check if year can be modified

**State Storage:**
Session-based storage for which year is selected for current operations.

## Translations

Translations located in `modules/Config/translations/{lang}/config.json`

### Available Languages
- English (en)
- French (fr)

### Translation Sections

**labels** - UI labels and buttons
**messages** - Informational messages
**errors** - Error messages
**alerts** - Toast notification messages
**validation** - Form validation messages
**permissions** - Permission descriptions

## Validation

### SchoolInfo Validation

- `name` - Required, string, max 255
- `acronym` - Nullable, string, max 50
- `school_type` - Required, enum (public/prive/confessionnel)
- `email` - Nullable, valid email
- `website` - Nullable, valid URL
- `foundation_year` - Nullable, integer, 1900-2100

### SchoolYear Validation

- `name` - Required, string, max 255, unique
- `start_year` - Required, integer, 1900-2100
- `end_year` - Required, integer, 1900-2100
- `start_date` - Required, date
- `end_date` - Required, date, after start_date
- `description` - Nullable, string

### SystemSetting Validation

- `key` - Required, string, unique
- `value` - Nullable
- `type` - Required, in (string/integer/boolean/json)
- `group` - Required, string

## Events & Alerts

### Alert Types

| Code | Type | Message |
|------|------|---------|
| `SCHOOL_YEAR_CREATED` | success | Year created |
| `SCHOOL_YEAR_UPDATED` | success | Year modified |
| `SCHOOL_YEAR_DELETED` | success | Year deleted |
| `SCHOOL_YEAR_ACTIVATED` | success | Year activated |
| `SESSION_SWITCHED` | success | Session changed |
| `CANNOT_DELETE_ACTIVE` | error | Cannot delete active year |
| `CREATE_ERROR` | error | Creation failed |
| `UPDATE_ERROR` | error | Modification failed |
| `DELETE_ERROR` | error | Deletion failed |
| `PERMISSION_DENIED` | error | Unauthorized |

### Livewire Events

- `school-year-changed` - Dispatched when active/session year changes

## File Organization

```
modules/Config/
├── Controllers/
│   ├── SchoolInfoController.php
│   ├── SystemSettingController.php
│   └── SchoolYearController.php
├── Livewire/
│   ├── DetailComponent.php
│   ├── SchoolYearComponent.php
│   └── FooterComponent.php
├── Models/
│   ├── SchoolInfo.php
│   ├── SystemSetting.php
│   └── SchoolYear.php
├── Services/
│   └── SchoolYearSessionService.php
├── Requests/
│   └── UpdateSchoolInfoRequest.php
├── Routes/
│   ├── web.php
│   └── api.php
├── migrations/
│   ├── 2024_01_01_000001_create_school_info_table.php
│   ├── 2024_01_01_000002_create_system_settings_table.php
│   └── 2024_01_01_000005_create_school_years_table.php
├── Resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── livewire/
│       │   ├── detail.blade.php
│       │   ├── school-year-component.blade.php
│       │   └── footer.blade.php
│       └── pages/
│           └── school-years.blade.php
├── translations/
│   ├── en/
│   │   └── config.json
│   └── fr/
│       └── config.json
├── permissions.json
└── CONFIG_MODULE.md (this file)
```

## Security Considerations

1. **Authentication** - All routes require `auth` middleware
2. **Authorization** - Granular permission checks at route and controller levels
3. **Defense in Depth** - Permission checks in both middleware and controller methods
4. **Input Validation** - Server-side validation in controllers and model validation rules
5. **File Upload** - Logo uploads validated for type and size
6. **Rate Limiting** - Should be configured in production
7. **Audit Logging** - Consider adding audit log entries for sensitive operations

## Common Tasks

### Creating a New School Year

```php
$year = SchoolYear::create([
    'name' => '2025-2026',
    'start_year' => 2025,
    'end_year' => 2026,
    'start_date' => '2025-09-01',
    'end_date' => '2026-06-30',
    'description' => 'Academic year 2025-2026',
]);
```

### Activating a School Year

```php
// Only one can be active
SchoolYear::where('is_active', true)->update(['is_active' => false]);
$year->update(['is_active' => true]);
```

### Getting Current Settings

```php
$timezone = SystemSetting::get('timezone', 'Africa/Douala');
$currency = SystemSetting::get('currency');
```

### Updating Multiple Settings

```php
SystemSetting::set('currency', 'XAF', 'string', 'general');
SystemSetting::set('timezone', 'Africa/Lagos', 'string', 'general');
```

## Testing

Run tests for Config module:

```bash
php artisan test --filter ConfigTest
```

Key test areas:
- Permission checks
- School year business rules
- CRUD operations
- Session management
- Translation completeness

## Troubleshooting

### Permission Denied Errors

1. Verify user has correct role
2. Run `php artisan permissions:sync` to update permissions
3. Check that role is assigned the permission in `role_permissions` table

### Translation Keys Missing

1. Check file exists in `modules/Config/translations/{lang}/config.json`
2. Verify key format with dots for nesting
3. Use translation fallback in English if not found

### School Year Issues

- Cannot delete active year? → Activate a different year first
- Cannot create duplicate name? → Year name must be unique
- Invalid dates? → End date must be after start date

## Future Enhancements

- [ ] Batch operations for multiple years
- [ ] School year templates
- [ ] Recurring year patterns
- [ ] Advanced audit logging
- [ ] Configuration export/import
- [ ] Multi-tenant support
- [ ] Logo size optimization
- [ ] Webhook notifications for year changes

---

**Last Updated:** 2024
**Module Type:** Core (always installed)
**Version:** 1.0.0
