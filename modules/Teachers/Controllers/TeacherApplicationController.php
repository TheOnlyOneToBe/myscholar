<?php

namespace Modules\Teachers\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Teachers\Models\TeacherApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherApplicationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get all teacher applications (admin only)
     */
    public function index(Request $request)
    {
        $this->authorize('review-teacher-applications');

        $query = TeacherApplication::with('user', 'approvedBy');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($applications);
    }

    /**
     * Get a specific application
     */
    public function show(TeacherApplication $application)
    {
        $this->authorize('view', $application);

        return response()->json($application->load(['user', 'approvedBy']));
    }

    /**
     * Approve a teacher application
     */
    public function approve(Request $request, TeacherApplication $application)
    {
        $this->authorize('approve-teacher-application');

        if (!$application->isPending()) {
            return response()->json(['message' => 'Cette candidature a déjà été traitée.'], 422);
        }

        $application->approve(auth()->user());

        return response()->json([
            'message' => 'Candidature approuvée avec succès.',
            'data' => $application->load(['user', 'approvedBy']),
        ]);
    }

    /**
     * Reject a teacher application
     */
    public function reject(Request $request, TeacherApplication $application)
    {
        $this->authorize('approve-teacher-application');

        $validated = $request->validate([
            'reason' => 'required|string|min:5|max:500',
        ]);

        if (!$application->isPending()) {
            return response()->json(['message' => 'Cette candidature a déjà été traitée.'], 422);
        }

        $application->reject($validated['reason']);

        return response()->json([
            'message' => 'Candidature rejetée.',
            'data' => $application,
        ]);
    }

    /**
     * Get current user's application
     */
    public function myApplication()
    {
        $application = TeacherApplication::where('user_id', auth()->id())->first();

        if (!$application) {
            return response()->json(['message' => 'Aucune candidature trouvée.'], 404);
        }

        return response()->json($application);
    }
}
