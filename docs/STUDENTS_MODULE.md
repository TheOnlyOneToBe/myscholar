# Students Module - Complete Documentation

## Overview

The Students module manages student registration, profiles, and family contact information with full multi-language support, Value Objects for data validation, and comprehensive error handling.

## Architecture

### Value Objects (Domain-Driven Design)

Value Objects ensure data integrity at the domain level through strict validation and immutability.

#### Gender

Represents student gender with full translation support.

```php
use Modules\Students\ValueObjects\Gender;

// Creation
$male = Gender::male();
$female = Gender::female();
$from = Gender::from('M');  // 'M' or 'F'

// Properties
$male->value();      // 'M'
$male->label();      // trans('students.genders.male') → "Masculin"
$male->isMale();     // true
$male->isFemale();   // false

// Comparison
$gender1->equals($gender2);  // true/false

// In Model
$student->setGenderFromObject(Gender::female());
$gender = $student->getGender();
```

**Supported Values:**
- `M` - Male / Masculin
- `F` - Female / Féminin

**Error Handling:**
```php
try {
    $invalid = new Gender('X');
} catch (InvalidArgumentException $e) {
    // trans('students.errors.invalid_gender', ['value' => 'X'])
    // "Genre invalide: X. Valeurs acceptées: M (Masculin) ou F (Féminin)"
}
```

#### Email

Represents email address with RFC and DNS validation.

```php
use Modules\Students\ValueObjects\Email;

// Creation
$email = new Email('student@school.com');
$email = Email::from('student@school.com');

// Properties
$email->value();       // 'student@school.com'
$email->localPart();   // 'student'
$email->domain();      // 'school.com'

// Comparison
$email1->equals($email2);

// In Model
$student->setEmailFromObject(new Email('student@school.com'));
$email = $student->getEmailObject();
```

**Validation:**
- RFC 5321 compliant format
- DNS record verification (if enabled)
- Case-insensitive storage (stored as lowercase)

**Error Handling:**
```php
try {
    $invalid = new Email('not-an-email');
} catch (InvalidArgumentException $e) {
    // trans('students.errors.invalid_email', ['email' => 'not-an-email'])
    // "Adresse email invalide: not-an-email"
}
```

#### PhoneNumber

Represents phone numbers with Cameroon-specific formatting.

```php
use Modules\Students\ValueObjects\PhoneNumber;

// Creation (supports multiple formats)
$phone = new PhoneNumber('237691234567');     // With country code
$phone = new PhoneNumber('+237-691234567');   // With + and separator
$phone = new PhoneNumber('691234567');        // Without country code
$phone = new PhoneNumber('(237)691234567');   // With parentheses
$phone = new PhoneNumber(null);               // Null/empty phone

// Properties
$phone->value();           // '237691234567' (raw input)
$phone->formatted();       // '+237-691234567' (standardized)
$phone->nationalNumber();  // '691234567' (without country code)
$phone->countryCode();     // '+237'
$phone->isEmpty();         // true if null/empty

// Comparison (compares national numbers)
$phone1->equals($phone2);  // true if national parts match

// In Model
$student->setPhoneFromObject(new PhoneNumber('691234567'));
$phone = $student->getPhoneObject();
```

**Supported Formats:**
- `+237XXXXXXXXX` - International format
- `237XXXXXXXXX` - Country code with digit
- `6XXXXXXXX`, `7XXXXXXXX`, `8XXXXXXXX`, `9XXXXXXXX` - Local format (Cameroon)
- With separators: `-`, `.`, spaces, parentheses

**Validation:**
- Minimum 9 digits
- Valid Cameroon area codes (6, 7, 8, 9)
- Automatic default to +237 (Cameroon) if no country code

**Error Handling:**
```php
try {
    $invalid = new PhoneNumber('123');
} catch (InvalidArgumentException $e) {
    // trans('students.errors.invalid_phone', ['phone' => '123'])
    // "Numéro de téléphone invalide: 123. Formats acceptés: +237XXXXXXXXX ou 6XXXXXXXX"
}
```

### Enums

Enums provide type-safe, translatable status and relationship management.

#### EnrollmentStatus

Student enrollment states with automatic state validation.

```php
use Modules\Students\Enums\EnrollmentStatus;

// Creation
$status = EnrollmentStatus::ACTIVE;
$status = EnrollmentStatus::from('active');  // From string value

// Properties
$status->value;                // 'active'
$status->label();              // trans('students.enrollment_status.active') → "Actif"
$status->description();        // trans('students.enrollment_status_descriptions.active')
$status->canModify();          // true if not graduated/withdrawn

// Comparison
$status === EnrollmentStatus::ACTIVE

// In Model
if ($status->canModify()) {
    $student->update(['enrollment_status' => EnrollmentStatus::SUSPENDED]);
}

// Get all options for select
$options = EnrollmentStatus::options();
// ['active' => 'Actif', 'suspended' => 'Suspendu', ...]
```

**Available Statuses:**
| Value | Label (FR) | Label (EN) | Can Modify | Description |
|-------|-----------|-----------|-----------|-------------|
| `active` | Actif | Active | ✓ Yes | Currently enrolled and active |
| `suspended` | Suspendu | Suspended | ✓ Yes | Temporarily suspended |
| `withdrawn` | Retiré | Withdrawn | ✗ No | Withdrawn from program |
| `graduated` | Diplômé | Graduated | ✗ No | Has graduated |
| `deferred` | Ajourné | Deferred | ✓ Yes | Deferred their year |

**State Protection:**
```php
// Prevent invalid operations on locked states
if (!$student->enrollment_status->canModify()) {
    throw new InvalidArgumentException(
        trans('students.errors.cannot_modify_status', 
            ['status' => $student->enrollment_status->label()])
    );
}
```

#### RelationshipType

Family relationship types with parent/emergency filtering.

```php
use Modules\Students\Enums\RelationshipType;

// Creation
$relationship = RelationshipType::FATHER;
$relationship = RelationshipType::from('father');

// Properties
$relationship->value;          // 'father'
$relationship->label();        // trans('students.relationships.father') → "Père"
$relationship->isParent();     // true/false
$relationship->isEmergencyContact();  // true/false

// Get options for select
$parentOptions = RelationshipType::parentOptions();
// Only father, mother, guardian

$allOptions = RelationshipType::options();
// All relationship types
```

**Available Relationships:**
| Value | Label (FR) | Label (EN) | Is Parent | Emergency |
|-------|-----------|-----------|-----------|-----------|
| `father` | Père | Father | ✓ | Can be |
| `mother` | Mère | Mother | ✓ | Can be |
| `guardian` | Tuteur | Guardian | ✓ | Can be |
| `emergency_contact` | Contact d'urgence | Emergency Contact | ✗ | ✓ Yes |
| `sibling` | Frère/Sœur | Sibling | ✗ | Can be |
| `grandparent` | Grand-parent | Grandparent | ✗ | Can be |
| `uncle` | Oncle | Uncle | ✗ | Can be |
| `aunt` | Tante | Aunt | ✗ | Can be |
| `cousin` | Cousin | Cousin | ✗ | Can be |
| `other` | Autre | Other | ✗ | Can be |

### Models

#### Student

```php
use Modules\Students\Models\Student;
use Modules\Students\Enums\EnrollmentStatus;

// Basic properties
$student->student_id_number    // Unique student number
$student->email                // Student email address
$student->phone_number         // Student phone number
$student->first_name           // First name
$student->last_name            // Last name
$student->date_of_birth        // DOB as Carbon date
$student->sex                  // M or F (use Value Object)
$student->place_of_birth       // Birth location
$student->id_number            // National ID (optional)
$student->photo_url            // Photo URL (optional)
$student->current_class_id     // Current class enrollment
$student->current_filiere      // Current study track
$student->enrollment_status    // EnrollmentStatus enum

// Methods
$student->getFullName();                  // "John Doe"
$student->getAge();                       // 20 (years)
$student->getGender();                    // Gender value object
$student->getEmailObject();               // Email value object
$student->getPhoneObject();               // PhoneNumber value object
$student->isActive();                     // bool
$student->canModify();                    // bool

// Status changes with validation
$student->suspend();                      // Sets status to SUSPENDED
$student->reactivate();                   // Sets status to ACTIVE
$student->withdraw();                     // Sets status to WITHDRAWN
$student->graduate();                     // Sets status to GRADUATED
$student->defer();                        // Sets status to DEFERRED

// Relationships
$student->familyContacts();               // All family contacts
$student->getPrimaryContact();            // Primary contact
$student->getEmergencyContacts();         // Collection of emergency contacts
$student->enrollments();                  // Class enrollments
$student->history();                      // Academic history
```

#### FamilyContact

```php
use Modules\Students\Models\FamilyContact;
use Modules\Students\Enums\RelationshipType;

// Properties
$contact->student_id           // Parent student
$contact->relationship         // RelationshipType enum
$contact->first_name           // Contact first name
$contact->last_name            // Contact last name
$contact->email                // Contact email
$contact->phone_number         // Contact phone number
$contact->occupation           // Occupation/profession
$contact->address              // Street address
$contact->city                 // City
$contact->postal_code          // Postal code
$contact->is_primary_contact   // Primary contact flag
$contact->is_emergency_contact // Emergency contact flag

// Methods
$contact->getFullName();                           // "John Smith"
$contact->getEmailObject();                        // Email value object
$contact->getPhoneObject();                        // PhoneNumber value object
$contact->setEmailFromObject(new Email('...'));    // Set from value object
$contact->setPhoneFromObject(new PhoneNumber('...')); // Set from value object
$contact->markAsPrimary();                         // Set as primary (unmarks others)
$contact->markAsEmergencyContact();                // Set as emergency

// Scopes
$contacts = FamilyContact::primary()->get();       // Primary only
$contacts = FamilyContact::emergency()->get();     // Emergency only
$contacts = FamilyContact::byRelationship($type)->get();  // By type
$contacts = FamilyContact::parents()->get();       // Parents only

// Relationships
$contact->student();           // Parent student
```

### Service Layer

The StudentService handles all student operations with transaction support and value object integration.

```php
use Modules\Students\Services\StudentService;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\PhoneNumber;
use Modules\Students\Enums\RelationshipType;

$service = app(StudentService::class);
```

#### Creating Students

```php
$student = $service->createStudent(
    studentIdNumber: 'STU-2024-001',
    firstName: 'John',
    lastName: 'Doe',
    dateOfBirth: Carbon::parse('2006-05-15'),
    gender: Gender::male(),
    email: new Email('john.doe@school.com'),
    phone: new PhoneNumber('237691234567'),
    placeOfBirth: 'Yaoundé',
    idNumber: '123456789',
    photoUrl: null,
    currentClassId: 1,
    currentFiliere: 'Scientifique',
    enrollmentStatus: EnrollmentStatus::ACTIVE  // Optional, defaults to ACTIVE
);
```

#### Updating Students

```php
$service->updateStudent(
    student: $student,
    email: 'newemail@school.com',  // Optional
    phone: new PhoneNumber('237692222222'),  // Optional
    gender: Gender::female(),  // Optional
    photoUrl: 'https://...',   // Optional
    currentClassId: 2,         // Optional
    currentFiliere: 'Littéraire'  // Optional
);
```

#### Managing Family Contacts

```php
// Add a family contact
$contact = $service->addFamilyContact(
    student: $student,
    relationship: RelationshipType::MOTHER,
    firstName: 'Jane',
    lastName: 'Doe',
    email: new Email('jane.doe@email.com'),
    phone: new PhoneNumber('237691111111'),
    occupation: 'Teacher',
    address: '123 Main St',
    city: 'Yaoundé',
    postalCode: '00000',
    isPrimaryContact: true,
    isEmergencyContact: false
);

// Update a family contact
$service->updateFamilyContact(
    contact: $contact,
    email: new Email('newemail@email.com'),  // Optional
    phone: new PhoneNumber('237692222222'),  // Optional
    occupation: 'Principal',                  // Optional
    isPrimaryContact: true,                   // Optional
    isEmergencyContact: true                  // Optional
);

// Delete a family contact
$service->deleteFamilyContact($contact);
```

#### Querying Students

```php
// Find by gender
$females = $service->findByGender(Gender::female());

// Find by email
$students = $service->findByEmail('john@school.com');

// Find by phone
$students = $service->findByPhone('237691234567');

// Find by enrollment status
$active = $service->findByEnrollmentStatus(EnrollmentStatus::ACTIVE);

// Get student with all relations
$student = $service->getStudentWithDetails($studentId);

// Get paginated list with family contacts
$students = $service->getStudentsWithFamilyContacts(limit: 20);
```

### Request Validation

The CreateStudentRequest provides comprehensive form validation.

```php
use Modules\Students\Requests\CreateStudentRequest;

// In controller
public function store(CreateStudentRequest $request)
{
    $validated = $request->validated();
    // Automatically validated and translated
}
```

**Validation Rules:**
| Field | Rules | Message Key |
|-------|-------|------------|
| `student_id_number` | required, unique, max 100 | `students.validation.*` |
| `email` | required, email, unique | `students.validation.email_*` |
| `phone_number` | required, regex | `students.validation.phone_*` |
| `first_name` | required, max 100 | `students.validation.first_name_required` |
| `last_name` | required, max 100 | `students.validation.last_name_required` |
| `date_of_birth` | required, before today | `students.validation.date_of_birth_required` |
| `sex` | required, in M/F | `students.validation.gender_required` |
| `place_of_birth` | nullable | |
| `id_number` | nullable, unique, max 100 | |
| `photo_url` | nullable, url, max 500 | |
| `current_class_id` | nullable, exists | |
| `current_filiere` | nullable, max 100 | |
| `enrollment_status` | nullable, valid status | |

## Translations

All messages are fully translatable in French and English.

### French (fr)
```json
{
  "genders": {
    "male": "Masculin",
    "female": "Féminin"
  },
  "enrollment_status": {
    "active": "Actif",
    "suspended": "Suspendu",
    "withdrawn": "Retiré",
    "graduated": "Diplômé",
    "deferred": "Ajourné"
  },
  "relationships": {
    "father": "Père",
    "mother": "Mère",
    "guardian": "Tuteur",
    // ... more
  },
  "errors": {
    "invalid_gender": "Genre invalide: {value}. Valeurs acceptées: M (Masculin) ou F (Féminin)",
    "invalid_email": "Adresse email invalide: {email}",
    "invalid_phone": "Numéro de téléphone invalide: {phone}. Formats acceptés: +237XXXXXXXXX ou 6XXXXXXXX",
    // ... more
  },
  "messages": {
    "student_created": "L'étudiant {name} a été créé avec succès",
    // ... more
  },
  "validation": {
    "email_unique": "Cet email est déjà utilisé",
    "phone_format": "Le format du numéro de téléphone est invalide",
    // ... more
  }
}
```

## Error Handling

All operations throw `InvalidArgumentException` with translated messages.

```php
// Automatic error translation
try {
    new Email('invalid');
} catch (InvalidArgumentException $e) {
    // Message: "Adresse email invalide: invalid"
    // Already translated based on app locale
    return response()->json(['error' => $e->getMessage()], 422);
}
```

## Database Schema

### students table modifications
```sql
ALTER TABLE students ADD COLUMN email VARCHAR(255) UNIQUE;
ALTER TABLE students ADD COLUMN phone_number VARCHAR(20);
CREATE INDEX idx_students_email ON students(email);
```

### family_contacts table
```sql
CREATE TABLE family_contacts (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  student_id BIGINT NOT NULL,
  relationship VARCHAR(50) NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  phone_number VARCHAR(20),
  occupation VARCHAR(255),
  address VARCHAR(255),
  city VARCHAR(100),
  postal_code VARCHAR(20),
  is_primary_contact BOOLEAN DEFAULT false,
  is_emergency_contact BOOLEAN DEFAULT false,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE KEY unique_student_email (student_id, email),
  INDEX (student_id, relationship),
  INDEX (is_primary_contact),
  INDEX (is_emergency_contact)
);
```

## Usage Examples

### Complete Student Registration

```php
use Modules\Students\Services\StudentService;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\PhoneNumber;
use Modules\Students\Enums\RelationshipType;

$service = app(StudentService::class);

// 1. Create the student
$student = $service->createStudent(
    studentIdNumber: 'STU-2024-001',
    firstName: 'Alice',
    lastName: 'Johnson',
    dateOfBirth: Carbon::parse('2007-02-15'),
    gender: Gender::female(),
    email: new Email('alice.johnson@school.com'),
    phone: new PhoneNumber('237691234567'),
    placeOfBirth: 'Douala',
    currentFiliere: 'Scientifique'
);

// 2. Add mother as primary contact
$service->addFamilyContact(
    student: $student,
    relationship: RelationshipType::MOTHER,
    firstName: 'Marie',
    lastName: 'Johnson',
    email: new Email('marie.johnson@email.com'),
    phone: new PhoneNumber('237695555555'),
    occupation: 'Doctor',
    isPrimaryContact: true
);

// 3. Add father as emergency contact
$service->addFamilyContact(
    student: $student,
    relationship: RelationshipType::FATHER,
    firstName: 'Richard',
    lastName: 'Johnson',
    email: new Email('richard.johnson@email.com'),
    phone: new PhoneNumber('237696666666'),
    occupation: 'Engineer',
    isEmergencyContact: true
);

// 4. Retrieve with all relations
$fullStudent = $service->getStudentWithDetails($student->id);
echo "Student: " . $fullStudent->getFullName();
echo "Primary Contact: " . $fullStudent->getPrimaryContact()->getFullName();
echo "Emergency Contacts: " . $fullStudent->getEmergencyContacts()->count();
```

### Querying Students

```php
// Get all female students
$females = $service->findByGender(Gender::female());

// Get specific student by email
$students = $service->findByEmail('alice.johnson@school.com');

// Get students in a specific status
$graduated = $service->findByEnrollmentStatus(
    EnrollmentStatus::GRADUATED
);

// Get all students (with pagination)
$paginated = $service->getStudentsWithFamilyContacts();
foreach ($paginated as $student) {
    echo $student->getFullName();
    if ($primary = $student->getPrimaryContact()) {
        echo " - Primary: " . $primary->getFullName();
    }
}
```

## Testing

```bash
# Test Value Objects
php artisan tinker << 'EOF'
$gender = \Modules\Students\ValueObjects\Gender::female();
$email = new \Modules\Students\ValueObjects\Email('student@school.com');
$phone = new \Modules\Students\ValueObjects\PhoneNumber('237691234567');
EOF

# Test Student Creation
php artisan test Modules/Students/Tests/StudentServiceTest
```

## Best Practices

1. **Always use Value Objects**
   ```php
   // ✓ Good
   $student->setEmailFromObject(new Email('student@school.com'));
   
   // ✗ Avoid direct assignment
   $student->email = 'student@school.com';  // No validation
   ```

2. **Handle Translations**
   ```php
   // ✓ Good
   echo $student->enrollment_status->label();  // Translated
   
   // ✗ Avoid hardcoding
   echo 'Active';  // Not translated
   ```

3. **Use Service Layer**
   ```php
   // ✓ Good
   $service->createStudent(...);  // Transactional, validated
   
   // ✗ Avoid direct model creation
   Student::create(...);  // No validation
   ```

4. **Check Permissions**
   ```php
   // Before modifying students
   if (!auth()->user()->hasPermission('students.edit')) {
       abort(403);
   }
   ```

## Localization

To add new languages:

1. Create `modules/Students/translations/{locale}/students.json`
2. Translate all keys from the French/English files
3. Laravel will automatically use them

```bash
# Current languages
- en (English)
- fr (French)
```

## Related Documentation

- [VALUE OBJECTS](./VALUE_OBJECTS.md) - Domain-Driven Design patterns
- [PERMISSIONS AND ROLES](./PERMISSIONS_AND_ROLES.md) - Access control
- [SCHOOL_YEAR_GUIDE](./SCHOOL_YEAR_GUIDE.md) - Multi-year data management

---

**Status:** ✅ Production Ready  
**Last Updated:** 2024-06-27  
**Version:** 1.0
