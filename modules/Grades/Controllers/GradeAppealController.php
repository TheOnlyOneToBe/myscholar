<?php

namespace Modules\Grades\Controllers;

use App\Http\Controllers\Controller;
use Modules\Grades\Services\GradeAppealService;
use Modules\Grades\Repositories\GradeAppealRepository;
use Modules\Grades\Requests\CreateGradeAppealRequest;
use Modules\Grades\Requests\ReviewGradeAppealRequest;
use Illuminate\Http\JsonResponse;

class GradeAppealController extends Controller
{
    public function __construct(
        protected GradeAppealService $appealService,
        protected GradeAppealRepository $appealRepository
    ) {
    }

    public function index(): JsonResponse
    {
        $this->authorize('view', 'grade_appeals');
        
        $filters = request()->only(['status', 'student_id', 'subject_id']);
        $perPage = request()->input('per_page', 25);
        
        $appeals = $this->appealRepository->all($filters, $perPage);
        
        return response()->json([
            'data' => $appeals->items(),
            'pagination' => [
                'total' => $appeals->total(),
                'per_page' => $appeals->perPage(),
                'current_page' => $appeals->currentPage(),
                'last_page' => $appeals->lastPage(),
                'from' => $appeals->firstItem(),
                'to' => $appeals->lastItem(),
            ],
        ]);
    }

    public function store(CreateGradeAppealRequest $request): JsonResponse
    {
        $this->authorize('submit', 'grade_appeals');
        
        $appeal = $this->appealService->submitAppeal(
            array_merge($request->validated(), ['student_id' => auth()->id()])
        );
        
        return response()->json($appeal, 201);
    }

    public function show($id): JsonResponse
    {
        $this->authorize('view', 'grade_appeals');
        
        $appeal = $this->appealRepository->findById($id);
        
        return response()->json($appeal);
    }

    public function myAppeals(): JsonResponse
    {
        $status = request()->input('status');
        $appeals = $this->appealService->getStudentAppeals(auth()->id(), $status);
        
        return response()->json(['data' => $appeals]);
    }

    public function approve(ReviewGradeAppealRequest $request, $id): JsonResponse
    {
        $this->authorize('review', 'grade_appeals');
        
        $this->appealService->approveAppeal(
            $id,
            auth()->id(),
            $request->input('response')
        );
        
        return response()->json(['message' => 'Appeal approved successfully']);
    }

    public function reject(ReviewGradeAppealRequest $request, $id): JsonResponse
    {
        $this->authorize('review', 'grade_appeals');
        
        $this->appealService->rejectAppeal(
            $id,
            auth()->id(),
            $request->input('response')
        );
        
        return response()->json(['message' => 'Appeal rejected successfully']);
    }
}
