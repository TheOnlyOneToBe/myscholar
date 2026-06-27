<?php

namespace Modules\Students\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Students\Models\StudentEnrollment;
use Modules\Students\Models\Student;
use Modules\Students\Requests\CreateEnrollmentRequest;
use Modules\Students\Requests\UpdateEnrollmentRequest;
use Modules\Students\Services\StudentService;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected StudentService $studentService
    ) {}

    /**
     * Get all enrollments with advanced filtering
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', StudentEnrollment::class);

        $query = StudentEnrollment::query()
            ->with(['student', 'class', 'schoolYear']);

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        // Filter by school year
        if ($request->has('school_year_id')) {
            $query->where('school_year_id', $request->input('school_year_id'));
        }

        // Filter by class
        if ($request->has('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        // Filter by filiere
        if ($request->has('filiere')) {
            $query->where('filiere', $request->input('filiere'));
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by enrollment date range
        if ($request->has('enrollment_date_from')) {
            $query->whereDate('enrollment_date', '>=', $request->input('enrollment_date_from'));
        }

        if ($request->has('enrollment_date_to')) {
            $query->whereDate('enrollment_date', '<=', $request->input('enrollment_date_to'));
        }

        // Search by student name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $enrollments = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'data' => $enrollments->items(),
            'pagination' => [
                'total' => $enrollments->total(),
                'per_page' => $enrollments->perPage(),
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'from' => $enrollments->firstItem(),
                'to' => $enrollments->lastItem(),
            ],
        ]);
    }

    /**
     * Get single enrollment
     */
    public function show(StudentEnrollment $enrollment): JsonResponse
    {
        $this->authorize('view', $enrollment);

        $enrollment->load(['student', 'class', 'schoolYear']);

        return response()->json([
            'data' => $enrollment,
        ]);
    }

    /**
     * Create a new enrollment
     */
    public function store(CreateEnrollmentRequest $request): JsonResponse
    {
        $this->authorize('create', StudentEnrollment::class);

        try {
            $validated = $request->validated();

            $enrollment = $this->studentService->addEnrollment(
                student: Student::findOrFail($validated['student_id']),
                schoolYearId: $validated['school_year_id'] ?? null,
                classId: $validated['class_id'] ?? null,
                filiere: $validated['filiere'] ?? null,
                level: $validated['level'] ?? null,
                enrollmentDate: $validated['enrollment_date']
                    ? Carbon::parse($validated['enrollment_date'])
                    : now(),
                status: $validated['status'] ?? 'active',
                notes: $validated['notes'] ?? null
            );

            return response()->json([
                'message' => trans('students.messages.enrollment_created'),
                'data' => $enrollment->load(['student', 'class', 'schoolYear']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.enrollment_creation_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update an enrollment
     */
    public function update(UpdateEnrollmentRequest $request, StudentEnrollment $enrollment): JsonResponse
    {
        $this->authorize('update', $enrollment);

        try {
            $validated = $request->validated();

            $updated = $this->studentService->updateEnrollment(
                enrollment: $enrollment,
                classId: $validated['class_id'] ?? null,
                filiere: $validated['filiere'] ?? null,
                level: $validated['level'] ?? null,
                status: $validated['status'] ?? null,
                notes: $validated['notes'] ?? null
            );

            return response()->json([
                'message' => trans('students.messages.enrollment_updated'),
                'data' => $updated->load(['student', 'class', 'schoolYear']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.enrollment_update_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete/Archive an enrollment
     */
    public function destroy(StudentEnrollment $enrollment): JsonResponse
    {
        $this->authorize('delete', $enrollment);

        try {
            $this->studentService->deleteEnrollment($enrollment);

            return response()->json([
                'message' => trans('students.messages.enrollment_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.enrollment_deletion_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Suspend an enrollment
     */
    public function suspend(Request $request, StudentEnrollment $enrollment): JsonResponse
    {
        $this->authorize('manageStatus', $enrollment);

        try {
            $enrollment->update(['status' => 'suspended']);

            return response()->json([
                'message' => trans('students.messages.enrollment_suspended'),
                'data' => $enrollment->fresh()->load(['student', 'class', 'schoolYear']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.enrollment_suspension_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Resume/Activate an enrollment
     */
    public function resume(StudentEnrollment $enrollment): JsonResponse
    {
        $this->authorize('manageStatus', $enrollment);

        try {
            $enrollment->update(['status' => 'active']);

            return response()->json([
                'message' => trans('students.messages.enrollment_resumed'),
                'data' => $enrollment->fresh()->load(['student', 'class', 'schoolYear']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.enrollment_resumption_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', StudentEnrollment::class);

        try {
            $request->validate([
                'school_year_id' => 'nullable|exists:school_years,id',
                'class_id' => 'nullable|exists:classes,id',
                'filiere' => 'nullable|string',
                'status' => 'nullable|string',
            ]);

            $query = StudentEnrollment::query()->with('student');

            if ($request->has('school_year_id')) {
                $query->where('school_year_id', $request->input('school_year_id'));
            }

            if ($request->has('class_id')) {
                $query->where('class_id', $request->input('class_id'));
            }

            if ($request->has('filiere')) {
                $query->where('filiere', $request->input('filiere'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $enrollments = $query->get();

            $csv = "Matricule,Nom,Prénom,Classe,Filière,Niveau,Année,Statut,Date Inscription\n";
            foreach ($enrollments as $enrollment) {
                $csv .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $enrollment->student->student_id_number,
                    $enrollment->student->last_name,
                    $enrollment->student->first_name,
                    $enrollment->class_id ?? 'N/A',
                    $enrollment->filiere ?? 'N/A',
                    $enrollment->level ?? 'N/A',
                    $enrollment->schoolYear?->name ?? 'N/A',
                    $enrollment->status,
                    $enrollment->enrollment_date->format('d/m/Y')
                );
            }

            return response()->json([
                'csv' => base64_encode($csv),
                'filename' => 'enrollments_' . now()->format('Y-m-d_H-i-s') . '.csv',
                'count' => $enrollments->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('students.errors.export_failed'),
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get enrollment statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', StudentEnrollment::class);

        $query = StudentEnrollment::query();

        if ($request->has('school_year_id')) {
            $query->where('school_year_id', $request->input('school_year_id'));
        }

        $totalEnrollments = $query->count();
        $activeEnrollments = $query->clone()->where('status', 'active')->count();
        $suspendedEnrollments = $query->clone()->where('status', 'suspended')->count();
        $withdrawnEnrollments = $query->clone()->where('status', 'withdrawn')->count();
        $graduatedEnrollments = $query->clone()->where('status', 'graduated')->count();

        $filiereStats = $query->clone()
            ->selectRaw('filiere, count(*) as total')
            ->groupBy('filiere')
            ->get();

        $classStats = $query->clone()
            ->selectRaw('class_id, count(*) as total')
            ->groupBy('class_id')
            ->get();

        return response()->json([
            'total_enrollments' => $totalEnrollments,
            'active' => $activeEnrollments,
            'suspended' => $suspendedEnrollments,
            'withdrawn' => $withdrawnEnrollments,
            'graduated' => $graduatedEnrollments,
            'by_filiere' => $filiereStats,
            'by_class' => $classStats,
        ]);
    }
}
