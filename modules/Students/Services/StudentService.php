<?php

namespace Modules\Students\Services;

use Illuminate\Support\Facades\DB;
use Modules\Students\Enums\EnrollmentStatus;
use Modules\Students\Models\FamilyContact;
use Modules\Students\Models\Student;
use Modules\Students\Enums\RelationshipType;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\PhoneNumber;

/**
 * Student Service
 * Handles student-related operations with proper value object validation
 */
class StudentService
{
    /**
     * Create a new student
     */
    public function createStudent(
        string $studentIdNumber,
        string $firstName,
        string $lastName,
        \Carbon\Carbon $dateOfBirth,
        Gender $gender,
        Email $email,
        PhoneNumber $phone,
        ?string $placeOfBirth = null,
        ?string $idNumber = null,
        ?string $photoUrl = null,
        ?int $currentClassId = null,
        ?string $currentFiliere = null,
        ?EnrollmentStatus $enrollmentStatus = null,
    ): Student {
        try {
            return DB::transaction(function () use (
                $studentIdNumber,
                $firstName,
                $lastName,
                $dateOfBirth,
                $gender,
                $email,
                $phone,
                $placeOfBirth,
                $idNumber,
                $photoUrl,
                $currentClassId,
                $currentFiliere,
                $enrollmentStatus,
            ) {
                $student = Student::create([
                    'student_id_number' => $studentIdNumber,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => $dateOfBirth,
                    'sex' => $gender->value(),
                    'email' => $email->value(),
                    'phone_number' => $phone->isEmpty() ? null : $phone->value(),
                    'place_of_birth' => $placeOfBirth,
                    'id_number' => $idNumber,
                    'photo_url' => $photoUrl,
                    'current_class_id' => $currentClassId,
                    'current_filiere' => $currentFiliere,
                    'enrollment_status' => $enrollmentStatus?->value ?? EnrollmentStatus::ACTIVE->value,
                ]);

                return $student;
            });
        } catch (\Exception $e) {
            throw new \RuntimeException(
                trans('students.errors.student_not_created', ['error' => $e->getMessage()])
            );
        }
    }

    /**
     * Update a student
     */
    public function updateStudent(
        Student $student,
        ?string $email = null,
        ?PhoneNumber $phone = null,
        ?Gender $gender = null,
        ?string $photoUrl = null,
        ?int $currentClassId = null,
        ?string $currentFiliere = null,
    ): Student {
        try {
            return DB::transaction(function () use (
                $student,
                $email,
                $phone,
                $gender,
                $photoUrl,
                $currentClassId,
                $currentFiliere,
            ) {
                $updateData = [];

                if ($email !== null) {
                    $updateData['email'] = new Email($email);
                    $updateData['email'] = $updateData['email']->value();
                }

                if ($phone !== null) {
                    $updateData['phone_number'] = $phone->isEmpty() ? null : $phone->value();
                }

                if ($gender !== null) {
                    $updateData['sex'] = $gender->value();
                }

                if ($photoUrl !== null) {
                    $updateData['photo_url'] = $photoUrl;
                }

                if ($currentClassId !== null) {
                    $updateData['current_class_id'] = $currentClassId;
                }

                if ($currentFiliere !== null) {
                    $updateData['current_filiere'] = $currentFiliere;
                }

                if (!empty($updateData)) {
                    $student->update($updateData);
                }

                return $student;
            });
        } catch (\Exception $e) {
            throw new \RuntimeException(
                trans('students.errors.student_not_updated', ['error' => $e->getMessage()])
            );
        }
    }

    /**
     * Add a family contact to a student
     */
    public function addFamilyContact(
        Student $student,
        RelationshipType $relationship,
        string $firstName,
        string $lastName,
        ?Email $email = null,
        ?PhoneNumber $phone = null,
        ?string $occupation = null,
        ?string $address = null,
        ?string $city = null,
        ?string $postalCode = null,
        bool $isPrimaryContact = false,
        bool $isEmergencyContact = false,
    ): FamilyContact {
        try {
            return DB::transaction(function () use (
                $student,
                $relationship,
                $firstName,
                $lastName,
                $email,
                $phone,
                $occupation,
                $address,
                $city,
                $postalCode,
                $isPrimaryContact,
                $isEmergencyContact,
            ) {
                // If marking as primary contact, unmark others
                if ($isPrimaryContact) {
                    $student->familyContacts()
                        ->update(['is_primary_contact' => false]);
                }

                $contact = $student->familyContacts()->create([
                    'relationship' => $relationship->value,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email?->value(),
                    'phone_number' => $phone && !$phone->isEmpty() ? $phone->value() : null,
                    'occupation' => $occupation,
                    'address' => $address,
                    'city' => $city,
                    'postal_code' => $postalCode,
                    'is_primary_contact' => $isPrimaryContact,
                    'is_emergency_contact' => $isEmergencyContact,
                ]);

                return $contact;
            });
        } catch (\Exception $e) {
            throw new \RuntimeException(
                trans('students.errors.family_contact_not_created', ['error' => $e->getMessage()])
            );
        }
    }

    /**
     * Update a family contact
     */
    public function updateFamilyContact(
        FamilyContact $contact,
        ?Email $email = null,
        ?PhoneNumber $phone = null,
        ?string $occupation = null,
        ?string $address = null,
        ?string $city = null,
        ?string $postalCode = null,
        ?bool $isPrimaryContact = null,
        ?bool $isEmergencyContact = null,
    ): FamilyContact {
        try {
            return DB::transaction(function () use (
                $contact,
                $email,
                $phone,
                $occupation,
                $address,
                $city,
                $postalCode,
                $isPrimaryContact,
                $isEmergencyContact,
            ) {
                $updateData = [];

                if ($email !== null) {
                    $updateData['email'] = $email->value();
                }

                if ($phone !== null) {
                    $updateData['phone_number'] = $phone->isEmpty() ? null : $phone->value();
                }

                if ($occupation !== null) {
                    $updateData['occupation'] = $occupation;
                }

                if ($address !== null) {
                    $updateData['address'] = $address;
                }

                if ($city !== null) {
                    $updateData['city'] = $city;
                }

                if ($postalCode !== null) {
                    $updateData['postal_code'] = $postalCode;
                }

                if ($isPrimaryContact === true) {
                    // Unmark others as primary
                    FamilyContact::where('student_id', $contact->student_id)
                        ->where('id', '!=', $contact->id)
                        ->update(['is_primary_contact' => false]);
                    $updateData['is_primary_contact'] = true;
                } elseif ($isPrimaryContact === false) {
                    $updateData['is_primary_contact'] = false;
                }

                if ($isEmergencyContact !== null) {
                    $updateData['is_emergency_contact'] = $isEmergencyContact;
                }

                if (!empty($updateData)) {
                    $contact->update($updateData);
                }

                return $contact;
            });
        } catch (\Exception $e) {
            throw new \RuntimeException(
                trans('students.errors.family_contact_not_updated', ['error' => $e->getMessage()])
            );
        }
    }

    /**
     * Delete a family contact
     */
    public function deleteFamilyContact(FamilyContact $contact): bool
    {
        return $contact->delete();
    }

    /**
     * Get students with family contacts
     */
    public function getStudentsWithFamilyContacts($limit = 20)
    {
        return Student::with('familyContacts')
            ->paginate($limit);
    }

    /**
     * Get a student with all relations
     */
    public function getStudentWithDetails(int $studentId): ?Student
    {
        return Student::with([
            'familyContacts',
            'enrollments',
            'history',
        ])->find($studentId);
    }

    /**
     * Find students by phone number
     */
    public function findByPhone(string $phoneNumber)
    {
        $phone = new PhoneNumber($phoneNumber);
        return Student::where('phone_number', $phone->value())->get();
    }

    /**
     * Find students by email
     */
    public function findByEmail(string $email)
    {
        $emailObj = new Email($email);
        return Student::where('email', $emailObj->value())->get();
    }

    /**
     * Find students by gender
     */
    public function findByGender(Gender $gender)
    {
        return Student::where('sex', $gender->value())->get();
    }

    /**
     * Find students by enrollment status
     */
    public function findByEnrollmentStatus(EnrollmentStatus $status)
    {
        return Student::where('enrollment_status', $status->value)->get();
    }
}
