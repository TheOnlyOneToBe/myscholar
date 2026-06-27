# Config Module API Reference

Complete API endpoint documentation for the MyScholar Configuration Module.

## Base URL

All endpoints are prefixed with `/api/config` and require authentication.

```
Base URL: /api/config
Auth: Bearer token or session cookie
```

## School Information Endpoints

### Get School Information

```http
GET /api/config/school
```

**Permission:** `config.view`

**Response:** 200 OK
```json
{
  "data": {
    "id": 1,
    "name": "Lycée Bilingue de Yaoundé",
    "acronym": "LBY",
    "motto": "Savoir, Discipline, Excellence",
    "logo_path": "logos/school-logo.png",
    "school_type": "public",
    "address": "Avenue du 20 Mai",
    "city": "Yaoundé",
    "region": "Centre",
    "phone": "+237 222 XXX XXX",
    "email": "contact@lby.cm",
    "website": null,
    "po_box": null,
    "approval_number": null,
    "creation_decree": null,
    "founder_name": null,
    "director_name": "M. DEMO Directeur",
    "foundation_year": 1985,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

**Error Response:** 404 Not Found
```json
{
  "message": "Aucune information du lycée configurée. Exécutez: php artisan school:setup",
  "data": null
}
```

### Update School Information

```http
PUT /api/config/school
```

**Permission:** `config.school_info.edit`

**Request Body:**
```json
{
  "name": "Lycée Bilingue de Yaoundé",
  "acronym": "LBY",
  "motto": "Savoir, Discipline, Excellence",
  "school_type": "public",
  "address": "Avenue du 20 Mai",
  "city": "Yaoundé",
  "region": "Centre",
  "phone": "+237 222 XXX XXX",
  "email": "contact@lby.cm",
  "website": null,
  "po_box": null,
  "approval_number": null,
  "creation_decree": null,
  "founder_name": null,
  "director_name": "M. DEMO Directeur",
  "foundation_year": 1985
}
```

**Response:** 200 OK
```json
{
  "message": "Informations du lycée mises à jour.",
  "data": { /* updated school info */ }
}
```

### Upload School Logo

```http
POST /api/config/school/logo
```

**Permission:** `config.school_info.logo`

**Request:** multipart/form-data
```
logo: <image file (JPEG/PNG, max 2MB)>
```

**Response:** 200 OK
```json
{
  "message": "Logo mis à jour.",
  "data": {
    "logo_path": "logos/school-logo-hash.png"
  }
}
```

**Validation Errors:** 422 Unprocessable Entity
```json
{
  "message": "...",
  "errors": {
    "logo": ["Le fichier logo est obligatoire.", "Le logo doit être au format JPEG ou PNG."]
  }
}
```

## System Settings Endpoints

### Get All System Settings

```http
GET /api/config/system-settings
```

**Permission:** `config.settings.view`

**Response:** 200 OK
```json
{
  "data": {
    "general": [
      {
        "id": 1,
        "key": "timezone",
        "value": "Africa/Douala",
        "type": "string",
        "group": "general"
      },
      {
        "id": 2,
        "key": "currency",
        "value": "FCFA",
        "type": "string",
        "group": "general"
      }
    ],
    "academic": [
      {
        "id": 5,
        "key": "max_students_per_class",
        "value": "45",
        "type": "integer",
        "group": "academic"
      }
    ]
  }
}
```

### Get Settings by Group

```http
GET /api/config/system-settings/group/{group}
```

**Permission:** `config.settings.view`

**Parameters:**
- `group` (required) - Setting group name (e.g., "general", "academic")

**Response:** 200 OK
```json
{
  "data": [
    {
      "id": 1,
      "key": "timezone",
      "value": "Africa/Douala",
      "type": "string"
    }
  ]
}
```

### Get Single Setting

```http
GET /api/config/system-settings/{setting}
```

**Permission:** `config.settings.view`

**Parameters:**
- `setting` (required) - Setting ID

**Response:** 200 OK
```json
{
  "data": {
    "id": 1,
    "key": "timezone",
    "value": "Africa/Douala",
    "type": "string",
    "group": "general"
  }
}
```

### Create Setting

```http
POST /api/config/system-settings
```

**Permission:** `config.settings.edit`

**Request Body:**
```json
{
  "key": "api_key",
  "value": "secret-value",
  "type": "string",
  "group": "general"
}
```

**Response:** 201 Created
```json
{
  "message": "Paramètre créé avec succès.",
  "data": { /* created setting */ }
}
```

### Update Setting

```http
PUT /api/config/system-settings/{setting}
```

**Permission:** `config.settings.edit`

**Parameters:**
- `setting` (required) - Setting ID

**Request Body:**
```json
{
  "value": "new-value",
  "type": "string"
}
```

**Response:** 200 OK
```json
{
  "message": "Paramètre mis à jour avec succès.",
  "data": { /* updated setting */ }
}
```

### Delete Setting

```http
DELETE /api/config/system-settings/{setting}
```

**Permission:** `config.settings.edit`

**Parameters:**
- `setting` (required) - Setting ID

**Response:** 200 OK
```json
{
  "message": "Paramètre supprimé avec succès."
}
```

### Bulk Update Settings

```http
PUT /api/config/system-settings/bulk/update
```

**Permission:** `config.settings.edit`

**Request Body:**
```json
{
  "settings": [
    {
      "key": "timezone",
      "value": "Africa/Lagos",
      "type": "string",
      "group": "general"
    },
    {
      "key": "currency",
      "value": "NGN",
      "type": "string",
      "group": "general"
    }
  ]
}
```

**Response:** 200 OK
```json
{
  "message": "Paramètres mis à jour avec succès.",
  "data": [ /* updated settings */ ]
}
```

## School Year Endpoints

### List All School Years

```http
GET /api/config/school-years
```

**Permission:** `config.school_year.view`

**Query Parameters:**
- `sort` (optional) - Sort field (default: "start_year")
- `order` (optional) - Sort order: "asc" or "desc"

**Response:** 200 OK
```json
{
  "data": [
    {
      "id": 1,
      "name": "2024-2025",
      "start_year": 2024,
      "end_year": 2025,
      "start_date": "2024-09-01",
      "end_date": "2025-08-31",
      "is_active": true,
      "is_locked": false,
      "description": "Academic year 2024-2025",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

### Get Current Active Year

```http
GET /api/config/school-years/current
```

**Permission:** `config.school_year.view`

**Response:** 200 OK
```json
{
  "data": {
    "id": 1,
    "name": "2024-2025",
    "start_year": 2024,
    "end_year": 2025,
    "start_date": "2024-09-01",
    "end_date": "2025-08-31",
    "is_active": true,
    "is_locked": false
  }
}
```

### Get School Year Details

```http
GET /api/config/school-years/{schoolYear}
```

**Permission:** `config.school_year.view`

**Parameters:**
- `schoolYear` (required) - School year ID

**Response:** 200 OK
```json
{
  "data": { /* school year object */ }
}
```

### Create School Year

```http
POST /api/config/school-years
```

**Permission:** `config.school_year.create`

**Request Body:**
```json
{
  "name": "2025-2026",
  "start_year": 2025,
  "end_year": 2026,
  "start_date": "2025-09-01",
  "end_date": "2026-08-31",
  "description": "Academic year 2025-2026"
}
```

**Response:** 201 Created
```json
{
  "message": "Année scolaire créée avec succès.",
  "data": { /* created year */ }
}
```

**Validation Errors:** 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name has already been taken."],
    "end_date": ["The end date must be a date after start_date."]
  }
}
```

### Update School Year

```http
PUT /api/config/school-years/{schoolYear}
```

**Permission:** `config.school_year.edit`

**Parameters:**
- `schoolYear` (required) - School year ID

**Request Body:**
```json
{
  "name": "2025-2026",
  "start_year": 2025,
  "end_year": 2026,
  "start_date": "2025-09-01",
  "end_date": "2026-08-31",
  "description": "Updated description"
}
```

**Response:** 200 OK
```json
{
  "message": "Année scolaire modifiée avec succès.",
  "data": { /* updated year */ }
}
```

### Activate School Year

```http
POST /api/config/school-years/{schoolYear}/activate
```

**Permission:** `config.school_year.edit`

**Parameters:**
- `schoolYear` (required) - School year ID

**Response:** 200 OK
```json
{
  "message": "Année scolaire {name} activée avec succès.",
  "data": { /* activated year */ }
}
```

**Note:** Activating a year deactivates all other years automatically.

### Delete School Year

```http
DELETE /api/config/school-years/{schoolYear}
```

**Permission:** `config.school_year.delete`

**Parameters:**
- `schoolYear` (required) - School year ID

**Response:** 200 OK
```json
{
  "message": "Année scolaire supprimée avec succès."
}
```

**Error Responses:**

**422 Unprocessable Entity** - Cannot delete active year
```json
{
  "message": "Impossible de supprimer l'année scolaire active."
}
```

### Get Year List (Simplified)

```http
GET /api/config/school-years/list
```

**Permission:** `config.school_year.view`

**Response:** 200 OK
```json
{
  "data": [
    {
      "id": 1,
      "name": "2024-2025",
      "is_active": true
    }
  ]
}
```

## General Settings (SchoolInfo)

### Get Settings

```http
GET /api/config/settings
```

**Permission:** `config.view`

**Response:** 200 OK
```json
{
  "data": {
    "general": {
      "timezone": "Africa/Douala",
      "currency": "FCFA",
      "language": "fr",
      "date_format": "d/m/Y"
    },
    "academic": {
      "max_students_per_class": "45",
      "current_academic_year": null
    }
  }
}
```

### Update Settings

```http
PUT /api/config/settings
```

**Permission:** `config.settings.edit`

**Request Body:**
```json
{
  "settings": [
    {
      "key": "timezone",
      "value": "Africa/Lagos",
      "type": "string",
      "group": "general"
    }
  ]
}
```

**Response:** 200 OK
```json
{
  "message": "Paramètres mis à jour."
}
```

## Error Handling

### Standard Error Response

```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Field error message"]
  }
}
```

### HTTP Status Codes

| Status | Meaning |
|--------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not Found |
| 422 | Unprocessable Entity (validation error) |
| 500 | Server Error |

## Authentication

All endpoints require authentication. Include one of:
- `Authorization: Bearer {token}` header
- Valid session cookie

## Rate Limiting

No global rate limiting is configured. Consider implementing for production:
```php
Route::middleware('throttle:60,1')->group(function () {
    // API routes
});
```

## Usage Examples

### Example 1: Create and Activate a School Year

```bash
# Create year
curl -X POST http://localhost/api/config/school-years \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "2025-2026",
    "start_year": 2025,
    "end_year": 2026,
    "start_date": "2025-09-01",
    "end_date": "2026-08-31"
  }'

# Activate year
curl -X POST http://localhost/api/config/school-years/2/activate \
  -H "Authorization: Bearer token"
```

### Example 2: Update School Settings

```bash
curl -X PUT http://localhost/api/config/settings \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "settings": [
      {
        "key": "currency",
        "value": "XAF",
        "type": "string",
        "group": "general"
      }
    ]
  }'
```

### Example 3: Upload Logo

```bash
curl -X POST http://localhost/api/config/school/logo \
  -H "Authorization: Bearer token" \
  -F "logo=@/path/to/logo.png"
```

## WebSocket Events

When school year configuration changes, a Livewire event is dispatched:

```javascript
document.addEventListener('school-year-changed', (event) => {
  console.log('School year changed:', event.detail.schoolYearId);
  // Refresh related data
});
```

---

**Last Updated:** 2024
**Version:** 1.0.0
**Module:** Config (Core)
