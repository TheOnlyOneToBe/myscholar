<?php

namespace Modules\Grades\Controllers;

use App\Http\Controllers\Controller;
use Modules\Grades\Services\GradeService;
use Modules\Grades\Repositories\GradeRepository;
use Modules\Grades\Requests\CreateGradeRequest;
use Modules\Grades\Requests\UpdateGradeRequest;
use Illuminate\Http\JsonResponse;

class GradeController extends Controller
{
    public function __construct(
        protected GradeService $gradeService,
        protected GradeRepository $gradeRepository
    ) {
    }

    public function index(): JsonResponse
    {
        $this->authorize('view', 'grades');
        
        $filters = request()->only(['student_id', 'subject_id', 'grade_period_id', 'teacher_id', 'grade_type', 'school_year_id']);
        $perPage = request()->input('per_page', 25);
        
        $grades = $this->gradeRepository->all($filters, $perPage);
        
        return response()->json([
            'data' => $grades->items(),
            'pagination' => [
                'total' => $grades->total(),
                'per_page' => $grades->perPage(),
                'current_page' => $grades->currentPage(),
                'last_page' => $grades->lastPage(),
                'from' => $grades->firstItem(),
                'to' => $grades->lastItem(),
            ],
        ]);
    }

    public function store(CreateGradeRequest $request): JsonResponse
    {
        $this->authorize('create', 'grades');
        
        $grade = $this->gradeService->createGrade($request->validated());
        
        return response()->json($grade, 201);
    }

    public function show($id): JsonResponse
    {
        $this->authorize('view', 'grades');
        
        $grade = $this->gradeRepository->findById($id);
        
        return response()->json($grade);
    }

    public function update(UpdateGradeRequest $request, $id): JsonResponse
    {
        $this->authorize('edit', 'grades');
        
        $this->gradeService->updateGrade($id, $request->validated());
        
        return response()->json(['message' => 'Grade updated successfully']);
    }

    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', 'grades');
        
        $this->gradeService->deleteGrade($id);
        
        return response()->json(['message' => 'Grade deleted successfully']);
    }

    public function getStudentGrades($studentId): JsonResponse
    {
        $this->authorize('view', 'grades');
        
        $gradePeriodId = request()->input('grade_period_id');
        $schoolYearId = request()->input('school_year_id');
        
        $grades = $this->gradeRepository->getStudentGrades($studentId, $gradePeriodId, $schoolYearId);
        
        return response()->json([
            'data' => $grades,
        ]);
    }

    public function statistics(): JsonResponse
    {
        $this->authorize('view', 'grades');
        
        $classId = request()->input('class_id');
        $gradePeriodId = request()->input('grade_period_id');
        $schoolYearId = request()->input('school_year_id');
        
        return response()->json([
            'message' => 'Statistics endpoint',
        ]);
    }
}
