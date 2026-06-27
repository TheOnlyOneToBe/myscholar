<?php

namespace Modules\Students\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Students\Models\Student;
use Modules\Students\Requests\CreateStudentRequest;
use Modules\Students\Requests\UpdateStudentRequest;
use Modules\Students\Services\StudentService;
use Modules\Students\Services\StudentIdService;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\PhoneNumber;
use Modules\Students\Enums\EnrollmentStatus;
use Carbon\Carbon;

class StudentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected StudentService $studentService,
        protected StudentIdService $studentIdService
    ) {}

    /**
     * Get all students with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Student::class);

        $query = Student::query();

        // Filter by enrollment status
        if ($request->has('enrollment_status')) {
            $query->where('enrollment_status', $request->input('enrollment_status'));
        }

        // Filter by current class
        if ($request->has('current_class_id')) {
            $query->where('current_class_id', $request->input('current_class_id'));
        }

        // Filter by filiere
        if ($request->has('current_filiere')) {
            $query->where('current_filiere', $request->input('current_filiere'));
        }

        // Search by name or student ID
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('student_id_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $students = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'data' => $students->items(),
            'pagination' => [
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'from' => $students->firstItem(),
                'to' => $students->lastItem(),
            ],
        ]);
    }

    /**
     * Get a single student by ID
     */
    public function show(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $student->load(['contacts', 'enrollments', 'familyContacts', 'history']);

        return response()->json([
            'data' => $student,
        ]);
    }

    /**
     * Create a new student with enrollment and parents
     */
    public function store(CreateStudentRequest $request): JsonResponse
    {
        $this->authorize('create', Student::class);

        try {
            $validated = $request->validated();

            $gender = Gender::from($validated['sex']);
            $email = Email::from($validated['email']);
            $phone = PhoneNumber::from($validated['phone_number']);
            $dateOfBirth = Carbon::parse($validated['date_of_birth']);
            $enrollmentStatus = $validated['enrollment_status']
                ? EnrollmentStatus::from($validated['enrollment_status'])
                : EnrollmentStatus::ACTIVE;

            $student = $this->studentService->createStudent(
                studentIdNumber: $validated['student_id_number'],
                firstName: $validated['first_name'],
                lastName: $validated['last_name'],
                dateOfBirth: $dateOfBirth,
                gender: $gender,
                email: $email,
                phone: $phone,
                placeOfBirth: $validated['place_of_birth'] ?? null,
                idNumber: $validated['id_number'] ?? null,
                photoUrl: $validated['photo_url'] ?? null,
                currentClassId: $validated['current_class_id'] ?? null,
                currentFiliere: $validated['current_filiere'] ?? null,
                enrollmentStatus: $enrollmentStatus
            );

            // Add enrollment if provided
            if (!empty($validated['enrollment'])) {
                $this->studentService->addEnrollment(
                    student: $student,
                    schoolYearId: $validated['enrollment']['school_year_id'] ?? null,
                    classId: $validated['enrollment']['class_id'] ?? null,
                    filiere: $validated['enrollment']['filiere'] ?? null,
                    level: $validated['enrollment']['level'] ?? null,
                    enrollmentDate: $validated['enrollment']['enrollment_date']
                        ? Carbon::parse($validated['enrollment']['enrollment_date'])
                        : now(),
                    status: $validated['enrollment']['status'] ?? 'active',
                    notes: $validated['enrollment']['notes'] ?? null
                );
            }

            // Add parents/family contacts if provided
            if (!empty($validated['parents'])) {
                foreach ($validated['parents'] as $parentData) {
                    $parentGender = isset($parentData['sex'])
                        ? Gender::from($parentData['sex'])
                        : null;

                    $parentPhone = isset($parentData['phone_number'])
                        ? PhoneNumber::from($parentData['phone_number'])
                        : null;

                    $parentEmail = isset($parentData['email'])
                        ? Email::from($parentData['email'])
                        : null;

                    $this->studentService->addFamilyContact(
                        student: $student,
                        relationship: \Modules\Students\Enums\RelationshipType::from($parentData['relationship']),
                        firstName: $parentData['first_name'],
                        lastName: $parentData['last_name'],
                        gender: $parentGender,
                        phone: $parentPhone,
                        email: $parentEmail,
                        occupation: $parentData['occupation'] ?? null,
                        address: $parentData['address'] ?? null,
                        city: $parentData['city'] ?? null,
                        postalCode: $parentData['postal_code'] ?? null,
                        isPrimaryContact: $parentData['is_primary_contact'] ?? false,
                        isEmergencyContact: $parentData['is_emergency_contact'] ?? false
                    );
                }
            }

            $student->load(['enrollments', 'familyContacts', 'contacts']);

            return response()->json([
                'message' => trans('students.messages.student_created_successfully'),
                'data' => $student,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.student_creation_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update an existing student
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        try {
            $validated = $request->validated();

            if (isset($validated['sex'])) {
                $validated['sex'] = Gender::from($validated['sex'])->value();
            }

            $student->update($validated);

            return response()->json([
                'message' => trans('students.messages.student_updated_successfully'),
                'data' => $student->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.student_update_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Archive/Delete a student
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('delete', $student);

        try {
            $this->studentService->deleteStudent($student->id);

            return response()->json([
                'message' => trans('students.messages.student_archived_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.student_deletion_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Suspend a student
     */
    public function suspend(Request $request, Student $student): JsonResponse
    {
        $this->authorize('suspend', $student);

        try {
            $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $this->studentService->suspendStudent($student->id, $request->input('reason'));

            return response()->json([
                'message' => trans('students.messages.student_suspended_successfully'),
                'data' => $student->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.student_suspension_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Activate a suspended student
     */
    public function activate(Student $student): JsonResponse
    {
        $this->authorize('activate', $student);

        try {
            $this->studentService->activateStudent($student->id);

            return response()->json([
                'message' => trans('students.messages.student_activated_successfully'),
                'data' => $student->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.student_activation_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', Student::class);

        try {
            $request->validate([
                'enrollment_status' => 'nullable|string',
                'current_class_id' => 'nullable|integer',
                'current_filiere' => 'nullable|string',
            ]);

            $query = Student::query();

            if ($request->has('enrollment_status')) {
                $query->where('enrollment_status', $request->input('enrollment_status'));
            }

            if ($request->has('current_class_id')) {
                $query->where('current_class_id', $request->input('current_class_id'));
            }

            if ($request->has('current_filiere')) {
                $query->where('current_filiere', $request->input('current_filiere'));
            }

            $students = $query->get();

            $csv = "Matricule,Nom,Prénom,Email,Téléphone,Date de Naissance,Sexe,Classe,Filière,Statut\n";
            foreach ($students as $student) {
                $csv .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $student->student_id_number,
                    $student->last_name,
                    $student->first_name,
                    $student->email,
                    $student->phone_number,
                    $student->date_of_birth->format('d/m/Y'),
                    $student->sex,
                    $student->current_class_id ?? '',
                    $student->current_filiere ?? '',
                    $student->enrollment_status->value
                );
            }

            return response()->json([
                'csv' => base64_encode($csv),
                'filename' => 'students_' . now()->format('Y-m-d_H-i-s') . '.csv',
                'count' => $students->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.export_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get student contacts
     */
    public function getContacts(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $contacts = $student->contacts()->paginate(10);

        return response()->json([
            'data' => $contacts->items(),
            'pagination' => [
                'total' => $contacts->total(),
                'per_page' => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
            ],
        ]);
    }

    /**
     * Get student enrollments
     */
    public function getEnrollments(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $enrollments = $student->enrollments()->paginate(10);

        return response()->json([
            'data' => $enrollments->items(),
            'pagination' => [
                'total' => $enrollments->total(),
                'per_page' => $enrollments->perPage(),
                'current_page' => $enrollments->currentPage(),
            ],
        ]);
    }

    /**
     * Get student family contacts
     */
    public function getFamilyContacts(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $familyContacts = $student->familyContacts()->paginate(10);

        return response()->json([
            'data' => $familyContacts->items(),
            'pagination' => [
                'total' => $familyContacts->total(),
                'per_page' => $familyContacts->perPage(),
                'current_page' => $familyContacts->currentPage(),
            ],
        ]);
    }

    /**
     * Get student history
     */
    public function getHistory(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $history = $student->history()->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'data' => $history->items(),
            'pagination' => [
                'total' => $history->total(),
                'per_page' => $history->perPage(),
                'current_page' => $history->currentPage(),
            ],
        ]);
    }
}
