<?php

namespace Modules\Grades\Controllers;

use App\Http\Controllers\Controller;
use Modules\Grades\Services\SubjectService;
use Modules\Grades\Requests\CreateSubjectRequest;
use Modules\Grades\Requests\UpdateSubjectRequest;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function __construct(protected SubjectService $subjectService)
    {
    }

    public function index(): JsonResponse
    {
        $this->authorize('view', 'subjects');
        
        $subjects = $this->subjectService->getActiveSubjects();
        
        return response()->json([
            'data' => $subjects->items(),
            'pagination' => [
                'total' => $subjects->total(),
                'per_page' => $subjects->perPage(),
                'current_page' => $subjects->currentPage(),
                'last_page' => $subjects->lastPage(),
                'from' => $subjects->firstItem(),
                'to' => $subjects->lastItem(),
            ],
        ]);
    }

    public function store(CreateSubjectRequest $request): JsonResponse
    {
        $this->authorize('create', 'subjects');
        
        $subject = $this->subjectService->createSubject($request->validated());
        
        return response()->json($subject, 201);
    }

    public function show($id): JsonResponse
    {
        $this->authorize('view', 'subjects');
        
        $subject = $this->subjectService->getActiveSubjects()->firstWhere('id', $id);
        
        return response()->json($subject);
    }

    public function update(UpdateSubjectRequest $request, $id): JsonResponse
    {
        $this->authorize('edit', 'subjects');
        
        $this->subjectService->updateSubject($id, $request->validated());
        
        return response()->json(['message' => 'Subject updated successfully']);
    }

    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', 'subjects');
        
        $this->subjectService->deleteSubject($id);
        
        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
