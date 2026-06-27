# Student Creation Guide - Complete Enrollment with Parents

## Overview

The enhanced student creation endpoint allows you to create a complete student record with enrollment information and parent/family contact details in a single API call. All data is created within a single database transaction for data integrity.

---

## API Endpoint

```
POST /api/students
```

**Authentication:** Required (auth:sanctum)  
**Authorization:** Requires `students.create` permission  
**Content-Type:** application/json

---

## Request Body Structure

### Basic Student Information (Required)

```json
{
  "student_id_number": "SCI-2024-0001",
  "first_name": "Jean",
  "last_name": "Dupont",
  "date_of_birth": "2008-05-15",
  "sex": "M",
  "email": "jean.dupont@example.com",
  "phone_number": "+237691234567"
}
```

| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| `student_id_number` | string | required, unique, max:100 | Student's unique identification number |
| `first_name` | string | required, max:100 | Student's first name |
| `last_name` | string | required, max:100 | Student's last name |
| `date_of_birth` | date | required, before:today | Student's date of birth (YYYY-MM-DD) |
| `sex` | string | required, in:M,F | Gender (M=Male, F=Female) |
| `email` | string | required, email, unique | Student's email address |
| `phone_number` | string | required, regex | Phone number (Cameroon format) |

### Optional Student Information

```json
{
  "place_of_birth": "Yaoundé",
  "id_number": "ID-123456",
  "photo_url": "https://example.com/photos/student.jpg",
  "enrollment_status": "active"
}
```

| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| `place_of_birth` | string | nullable, max:255 | Where the student was born |
| `id_number` | string | nullable, unique, max:100 | National ID number |
| `photo_url` | string | nullable, url, max:500 | URL to student photo |
| `enrollment_status` | string | nullable, in:active,suspended,withdrawn,graduated | Initial enrollment status |

### Enrollment Information (Optional)

```json
{
  "enrollment": {
    "school_year_id": 1,
    "class_id": 5,
    "filiere": "Science",
    "level": "Form 4",
    "enrollment_date": "2024-09-10",
    "status": "active",
    "notes": "Regular enrollment"
  }
}
```

| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| `enrollment.school_year_id` | integer | nullable, exists:school_years,id | Reference to school year |
| `enrollment.class_id` | integer | nullable, exists:classes,id | Reference to class |
| `enrollment.filiere` | string | nullable, max:100 | Academic stream/specialization |
| `enrollment.level` | string | nullable, max:50 | Grade/form level |
| `enrollment.enrollment_date` | date | nullable, date | When student was enrolled |
| `enrollment.status` | string | nullable, max:50 | Enrollment status (active, deferred, etc.) |
| `enrollment.notes` | string | nullable, max:500 | Additional notes about enrollment |

### Family Contacts / Parents (Optional)

```json
{
  "parents": [
    {
      "relationship": "father",
      "first_name": "Pierre",
      "last_name": "Dupont",
      "sex": "M",
      "email": "pierre.dupont@example.com",
      "phone_number": "+237690000001",
      "occupation": "Engineer",
      "address": "123 Rue de la Paix",
      "city": "Yaoundé",
      "postal_code": "1000",
      "is_primary_contact": true,
      "is_emergency_contact": false
    },
    {
      "relationship": "mother",
      "first_name": "Marie",
      "last_name": "Dupont",
      "sex": "F",
      "email": "marie.dupont@example.com",
      "phone_number": "+237690000002",
      "occupation": "Doctor",
      "address": "123 Rue de la Paix",
      "city": "Yaoundé",
      "postal_code": "1000",
      "is_primary_contact": false,
      "is_emergency_contact": true
    }
  ]
}
```

| Field | Type | Rules | Description |
|-------|------|-------|-------------|
| `parents` | array | nullable, array, min:1 | Array of parent/family contact records |
| `parents.*.relationship` | string | required, in:father,mother,guardian,emergency_contact,sibling,grandparent,uncle,aunt,cousin,other | Relationship to student |
| `parents.*.first_name` | string | required, max:100 | Parent's first name |
| `parents.*.last_name` | string | required, max:100 | Parent's last name |
| `parents.*.sex` | string | nullable, in:M,F | Parent's gender |
| `parents.*.email` | string | nullable, email | Parent's email |
| `parents.*.phone_number` | string | nullable, regex | Parent's phone number |
| `parents.*.occupation` | string | nullable, max:100 | Parent's job/occupation |
| `parents.*.address` | string | nullable, max:255 | Parent's address |
| `parents.*.city` | string | nullable, max:100 | Parent's city |
| `parents.*.postal_code` | string | nullable, max:20 | Parent's postal code |
| `parents.*.is_primary_contact` | boolean | nullable | Mark as primary contact |
| `parents.*.is_emergency_contact` | boolean | nullable | Mark as emergency contact |

---

## Complete Example Request

```bash
curl -X POST http://localhost:8000/api/students \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id_number": "SCI-2024-0001",
    "first_name": "Jean",
    "last_name": "Dupont",
    "date_of_birth": "2008-05-15",
    "sex": "M",
    "email": "jean.dupont@example.com",
    "phone_number": "+237691234567",
    "place_of_birth": "Yaoundé",
    "id_number": "ID-123456",
    "enrollment_status": "active",
    "enrollment": {
      "school_year_id": 1,
      "class_id": 5,
      "filiere": "Science",
      "level": "Form 4",
      "enrollment_date": "2024-09-10",
      "status": "active",
      "notes": "Regular enrollment"
    },
    "parents": [
      {
        "relationship": "father",
        "first_name": "Pierre",
        "last_name": "Dupont",
        "sex": "M",
        "email": "pierre.dupont@example.com",
        "phone_number": "+237690000001",
        "occupation": "Engineer",
        "address": "123 Rue de la Paix",
        "city": "Yaoundé",
        "postal_code": "1000",
        "is_primary_contact": true,
        "is_emergency_contact": false
      },
      {
        "relationship": "mother",
        "first_name": "Marie",
        "last_name": "Dupont",
        "sex": "F",
        "email": "marie.dupont@example.com",
        "phone_number": "+237690000002",
        "occupation": "Doctor",
        "is_emergency_contact": true
      }
    ]
  }'
```

---

## Success Response (201 Created)

```json
{
  "message": "Student has been created successfully",
  "data": {
    "id": 1,
    "student_id_number": "SCI-2024-0001",
    "first_name": "Jean",
    "last_name": "Dupont",
    "date_of_birth": "2008-05-15",
    "sex": "M",
    "email": "jean.dupont@example.com",
    "phone_number": "+237691234567",
    "place_of_birth": "Yaoundé",
    "id_number": "ID-123456",
    "photo_url": null,
    "current_class_id": 5,
    "current_filiere": "Science",
    "enrollment_status": "active",
    "created_at": "2024-09-01T10:30:00Z",
    "updated_at": "2024-09-01T10:30:00Z",
    "enrollments": [
      {
        "id": 1,
        "student_id": 1,
        "school_year_id": 1,
        "class_id": 5,
        "filiere": "Science",
        "level": "Form 4",
        "enrollment_date": "2024-09-10",
        "status": "active",
        "notes": "Regular enrollment",
        "created_at": "2024-09-01T10:30:00Z",
        "updated_at": "2024-09-01T10:30:00Z"
      }
    ],
    "family_contacts": [
      {
        "id": 1,
        "student_id": 1,
        "relationship": "father",
        "first_name": "Pierre",
        "last_name": "Dupont",
        "sex": "M",
        "email": "pierre.dupont@example.com",
        "phone_number": "+237690000001",
        "occupation": "Engineer",
        "address": "123 Rue de la Paix",
        "city": "Yaoundé",
        "postal_code": "1000",
        "is_primary_contact": true,
        "is_emergency_contact": false,
        "created_at": "2024-09-01T10:30:00Z",
        "updated_at": "2024-09-01T10:30:00Z"
      },
      {
        "id": 2,
        "student_id": 1,
        "relationship": "mother",
        "first_name": "Marie",
        "last_name": "Dupont",
        "sex": "F",
        "email": "marie.dupont@example.com",
        "phone_number": "+237690000002",
        "occupation": "Doctor",
        "address": null,
        "city": null,
        "postal_code": null,
        "is_primary_contact": false,
        "is_emergency_contact": true,
        "created_at": "2024-09-01T10:30:00Z",
        "updated_at": "2024-09-01T10:30:00Z"
      }
    ],
    "contacts": []
  }
}
```

---

## Error Response Examples

### Validation Error (422)

```json
{
  "error": "Failed to create student",
  "message": "The given data was invalid.",
  "errors": {
    "email": ["This email is already in use"],
    "student_id_number": ["This student number is already in use"],
    "parents.0.phone_number": ["Phone number format is invalid"]
  }
}
```

### Authorization Error (403)

```json
{
  "message": "This action is unauthorized."
}
```

### Server Error (422)

```json
{
  "error": "Failed to create student",
  "message": "Student could not be created: Database connection error"
}
```

---

## Important Notes

### Transaction Safety
- All data (student, enrollment, parents) is created within a single database transaction
- If any part fails, the entire operation is rolled back
- No partial records are created

### Unique Constraints
- `student_id_number` must be unique across all students
- `email` must be unique across all students
- `id_number` must be unique (if provided)

### Phone Number Format (Cameroon)
- Accepted formats:
  - `+237691234567` (with country code)
  - `691234567` (without country code)
  - `+237 69 1234567` (with spaces/dashes)
- Must start with country code (+237) or local digits (6, 7, 8, 9)

### Primary Contact Logic
- Only one primary contact per student (automatically enforced)
- If marking a parent as primary, other parents are unmarked
- Can have multiple emergency contacts

### Field Lengths
- Names (first, last): max 100 characters
- Email: max 255 characters
- Addresses: max 255 characters
- Notes/occupations: max 100-500 characters

---

## Usage Scenarios

### Scenario 1: Minimal Student (Required fields only)

```json
{
  "student_id_number": "SCI-2024-0001",
  "first_name": "Jean",
  "last_name": "Dupont",
  "date_of_birth": "2008-05-15",
  "sex": "M",
  "email": "jean@example.com",
  "phone_number": "+237691234567"
}
```

### Scenario 2: Student with Class Enrollment

```json
{
  "student_id_number": "SCI-2024-0002",
  "first_name": "Marie",
  "last_name": "Dubois",
  "date_of_birth": "2009-03-20",
  "sex": "F",
  "email": "marie@example.com",
  "phone_number": "+237691234568",
  "enrollment": {
    "school_year_id": 1,
    "class_id": 5,
    "filiere": "Science",
    "level": "Form 4"
  }
}
```

### Scenario 3: Complete Student with Parents

All fields filled as shown in the complete example above.

---

## Related Endpoints

- `GET /api/students` — List all students
- `GET /api/students/{id}` — View student details
- `PUT /api/students/{id}` — Update student
- `DELETE /api/students/{id}` — Archive student
- `GET /api/students/{id}/enrollments` — View enrollments
- `GET /api/students/{id}/family-contacts` — View family contacts

---

## Permissions Required

- `students.create` — Create new student records

---

## Best Practices

1. **Validate before sending** — Ensure data is clean before API call
2. **Use correct formats** — Follow phone/date formats strictly
3. **Set primary contact** — Mark one parent as primary for emergency contact
4. **Mark emergency contacts** — Set appropriate flag for emergency use
5. **Handle errors gracefully** — Check error messages for validation issues
6. **Test with minimal data** — Start with required fields, add optional ones
7. **Use transactions** — API handles transaction automatically, no need for client-side transaction logic

---

## Support

For issues or questions, please contact the MyScholar development team.

**Last Updated:** 2026-06-27  
**Version:** 1.0.0
