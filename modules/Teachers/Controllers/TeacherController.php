<?php

namespace Modules\Teachers\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Teachers\Models\Teacher;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all teachers
     */
    public function index(Request $request)
    {
        $query = Teacher::query();

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        if ($request->has('filiere')) {
            $query->byFiliere($request->filiere);
        }

        if ($request->has('specialization')) {
            $query->bySpecialization($request->specialization);
        }

        $teachers = $query->with(['user', 'subjects', 'classes'])->paginate(15);

        return response()->json([
            'data' => $teachers->items(),
            'pagination' => [
                'total' => $teachers->total(),
                'per_page' => $teachers->perPage(),
                'current_page' => $teachers->currentPage(),
                'last_page' => $teachers->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created teacher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:teachers',
            'teacher_code' => 'required|string|unique:teachers',
            'specialization' => 'nullable|string',
            'qualification_level' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'filiere' => 'nullable|in:generale,technique',
            'office_location' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
            'phone_office' => 'nullable|string',
            'email_office' => 'nullable|email',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json($teacher->load(['user', 'subjects']), 201);
    }

    /**
     * Display a specific teacher
     */
    public function show(Teacher $teacher)
    {
        $this->authorize('view', $teacher);

        return response()->json(
            $teacher->load(['user', 'qualifications', 'subjects', 'classes', 'history'])
        );
    }

    /**
     * Update a teacher
     */
    public function update(Request $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $validated = $request->validate([
            'specialization' => 'nullable|string',
            'qualification_level' => 'nullable|string',
            'filiere' => 'nullable|in:generale,technique',
            'office_location' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
            'phone_office' => 'nullable|string',
            'email_office' => 'nullable|email',
            'is_active' => 'nullable|boolean',
        ]);

        $teacher->update($validated);

        return response()->json($teacher->load(['user', 'subjects']));
    }

    /**
     * Delete a teacher
     */
    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);

        $teacher->delete();

        return response()->json(null, 204);
    }

    /**
     * Get teacher's qualifications
     */
    public function getQualifications(Teacher $teacher)
    {
        $this->authorize('view', $teacher);

        return response()->json($teacher->qualifications);
    }

    /**
     * Get teacher's assigned classes
     */
    public function getClasses(Teacher $teacher, Request $request)
    {
        $this->authorize('view', $teacher);

        $query = $teacher->classes();

        if ($request->has('school_year_id')) {
            $query->wherePivot('school_year_id', $request->school_year_id);
        }

        if ($request->has('status')) {
            $query->wherePivot('status', $request->status);
        }

        $classes = $query->with(['schoolYear', 'subject'])->get();

        return response()->json($classes);
    }

    /**
     * Get total hours per week
     */
    public function getTotalHours(Teacher $teacher)
    {
        $this->authorize('view', $teacher);

        return response()->json([
            'total_hours_per_week' => $teacher->getTotalHoursPerWeek(),
        ]);
    }

    /**
     * Get teacher's history
     */
    public function getHistory(Teacher $teacher)
    {
        $this->authorize('view', $teacher);

        $history = $teacher->history()
            ->orderBy('created_at', 'desc')
            ->with('createdBy')
            ->get();

        return response()->json($history);
    }
}
