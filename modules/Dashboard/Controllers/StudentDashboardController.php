<?php

namespace Modules\Dashboard\Controllers;

use Modules\Dashboard\Services\StudentDashboardService;
use App\Traits\VerifiesModuleAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StudentDashboardController extends Controller
{
    use VerifiesModuleAccess;

    public function __construct(
        private StudentDashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only students can access the student dashboard',
            ], 403);
        }

        try {
            return response()->json([
                'data' => [
                    'student_info' => $this->dashboardService->getStudentInfo(),
                    'quick_stats' => $this->dashboardService->getQuickStats(),
                    'recent_grades' => $this->dashboardService->getRecentGrades(),
                    'attendance_summary' => $this->dashboardService->getAttendanceSummary(),
                    'upcoming_payments' => $this->dashboardService->getUpcomingPaymentsDue(),
                    'class_info' => $this->dashboardService->getClassInformation(),
                    'is_chef_classe' => $this->dashboardService->isChefClasse(),
                    'chef_classe_data' => $this->dashboardService->getChefClasseData(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Dashboard Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function grades(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only students can access grades',
            ], 403);
        }

        if ($error = $this->verifyModuleAccess('Grades')) {
            return $error;
        }

        try {
            return response()->json([
                'data' => [
                    'recent_grades' => $this->dashboardService->getRecentGrades(10),
                    'grade_trend' => $this->dashboardService->getGradeTrend(),
                    'subject_performance' => $this->dashboardService->getSubjectPerformance(),
                    'pending_appeals' => $this->dashboardService->getPendingAppeals(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Grades Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function attendance(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only students can access attendance',
            ], 403);
        }

        if ($error = $this->verifyModuleAccess('Attendance')) {
            return $error;
        }

        try {
            return response()->json([
                'data' => $this->dashboardService->getAttendanceSummary(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Attendance Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function billing(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only students can access billing',
            ], 403);
        }

        if ($error = $this->verifyModuleAccess('Billing')) {
            return $error;
        }

        try {
            return response()->json([
                'data' => [
                    'upcoming_payments' => $this->dashboardService->getUpcomingPaymentsDue(5),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Billing Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function chefClasseData(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('chef_classe')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only class leaders can access this data',
            ], 403);
        }

        if ($error = $this->verifyModuleAccess('Classes')) {
            return $error;
        }

        try {
            return response()->json([
                'data' => $this->dashboardService->getChefClasseData(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Chef de Classe Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Only students can access profiles',
            ], 403);
        }

        if ($error = $this->verifyModuleAccess('Students')) {
            return $error;
        }

        try {
            return response()->json([
                'data' => $this->dashboardService->getStudentInfo(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Profile Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
