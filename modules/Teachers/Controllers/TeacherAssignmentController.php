<?php

namespace Modules\Teachers\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Teachers\Models\Teacher;
use Modules\Teachers\Models\TeacherClass;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherAssignmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Assign a teacher to a class
     */
    public function assignToClass(Request $request, Teacher $teacher)
    {
        $this->authorize('assignClass', $teacher);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'school_year_id' => 'nullable|exists:school_years,id',
            'hours_per_week' => 'nullable|integer|min:1',
        ]);

        $assignment = TeacherClass::firstOrCreate(
            [
                'teacher_id' => $teacher->id,
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'school_year_id' => $validated['school_year_id'] ?? null,
            ],
            [
                'hours_per_week' => $validated['hours_per_week'] ?? 0,
                'status' => 'active',
            ]
        );

        return response()->json($assignment, 201);
    }

    /**
     * Remove teacher from class
     */
    public function removeFromClass(Teacher $teacher, TeacherClass $assignment)
    {
        $this->authorize('removeClass', $teacher);

        if ($assignment->teacher_id !== $teacher->id) {
            abort(404);
        }

        $assignment->delete();

        return response()->json(null, 204);
    }

    /**
     * Update assignment status
     */
    public function updateAssignmentStatus(Teacher $teacher, TeacherClass $assignment, Request $request)
    {
        $this->authorize('updateClass', $teacher);

        if ($assignment->teacher_id !== $teacher->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,suspended,completed',
            'hours_per_week' => 'nullable|integer|min:0',
        ]);

        $assignment->update($validated);

        return response()->json($assignment);
    }

    /**
     * Add subject to teacher
     */
    public function addSubject(Teacher $teacher, Request $request)
    {
        $this->authorize('addSubject', $teacher);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'proficiency_level' => 'nullable|integer|between:1,5',
            'since_year' => 'required|year',
            'is_primary' => 'nullable|boolean',
        ]);

        $teacher->subjects()->attach($validated['subject_id'], [
            'proficiency_level' => $validated['proficiency_level'] ?? 3,
            'since_year' => $validated['since_year'],
            'is_primary' => $validated['is_primary'] ?? false,
        ]);

        return response()->json(['message' => __('teachers::messages.success.subject_added')], 201);
    }

    /**
     * Remove subject from teacher
     */
    public function removeSubject(Teacher $teacher, Request $request)
    {
        $this->authorize('removeSubject', $teacher);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher->subjects()->detach($validated['subject_id']);

        return response()->json(['message' => __('teachers::messages.success.subject_removed')], 204);
    }
}
