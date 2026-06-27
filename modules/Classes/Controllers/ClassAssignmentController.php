<?php

namespace Modules\Classes\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Classes\Models\ClassAssignment;
use Modules\Classes\Models\ClassModel;
use Modules\Auth\Models\User;

class ClassAssignmentController extends Controller
{
    use AuthorizesRequests;

    public function indexByClass(ClassModel $class): JsonResponse
    {
        $this->authorize('view', $class);

        $assignments = $class->assignments()
            ->with('teacher', 'schoolYear')
            ->paginate(25);

        return response()->json([
            'data' => $assignments->items(),
            'pagination' => [
                'total' => $assignments->total(),
                'per_page' => $assignments->perPage(),
            ],
        ]);
    }

    public function store(Request $request, ClassModel $class): JsonResponse
    {
        $this->authorize('manageAssignments', $class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:teacher,class_teacher,coordinator',
            'subject' => 'nullable|string|max:100',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $assignment = $class->assignments()->create($validated);

        return response()->json([
            'message' => 'Affectation créée avec succès',
            'data' => $assignment->load('teacher'),
        ], 201);
    }

    public function update(Request $request, ClassModel $class, ClassAssignment $assignment): JsonResponse
    {
        $this->authorize('manageAssignments', $class);

        $validated = $request->validate([
            'role' => 'sometimes|string|in:teacher,class_teacher,coordinator',
            'subject' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($validated);

        return response()->json([
            'message' => 'Affectation mise à jour avec succès',
            'data' => $assignment->fresh()->load('teacher'),
        ]);
    }

    public function destroy(ClassModel $class, ClassAssignment $assignment): JsonResponse
    {
        $this->authorize('manageAssignments', $class);

        $assignment->delete();

        return response()->json([
            'message' => 'Affectation supprimée avec succès',
        ]);
    }
}
