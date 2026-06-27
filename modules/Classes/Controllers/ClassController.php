<?php

namespace Modules\Classes\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Requests\CreateClassRequest;
use Modules\Classes\Requests\UpdateClassRequest;

class ClassController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ClassModel::class);

        $query = ClassModel::query()->with(['schoolYear', 'room']);

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->has('filiere')) {
            $query->where('filiere', $request->input('filiere'));
        }

        if ($request->has('school_year_id')) {
            $query->where('school_year_id', $request->input('school_year_id'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $classes = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'data' => $classes->items(),
            'pagination' => [
                'total' => $classes->total(),
                'per_page' => $classes->perPage(),
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
            ],
        ]);
    }

    public function show(ClassModel $class): JsonResponse
    {
        $this->authorize('view', $class);

        $class->load(['schoolYear', 'room', 'assignments.teacher', 'subjects', 'timetables']);

        return response()->json([
            'data' => $class,
        ]);
    }

    public function store(CreateClassRequest $request): JsonResponse
    {
        $this->authorize('create', ClassModel::class);

        $validated = $request->validated();

        $class = ClassModel::create($validated);

        return response()->json([
            'message' => 'Classe créée avec succès',
            'data' => $class->load(['schoolYear', 'room']),
        ], 201);
    }

    public function update(UpdateClassRequest $request, ClassModel $class): JsonResponse
    {
        $this->authorize('update', $class);

        $validated = $request->validated();

        $class->update($validated);

        return response()->json([
            'message' => 'Classe mise à jour avec succès',
            'data' => $class->fresh()->load(['schoolYear', 'room']),
        ]);
    }

    public function destroy(ClassModel $class): JsonResponse
    {
        $this->authorize('delete', $class);

        $class->delete();

        return response()->json([
            'message' => 'Classe supprimée avec succès',
        ]);
    }
}
