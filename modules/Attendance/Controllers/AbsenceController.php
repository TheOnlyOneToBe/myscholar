<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Attendance\Repositories\AbsenceRepository;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Models\AbsenceAlert;

class AbsenceController extends Controller
{
    public function __construct(
        private AbsenceRepository $absenceRepository,
        private AttendanceService $attendanceService,
    ) {}

    public function getCounter($studentId): JsonResponse
    {
        $counter = $this->absenceRepository->getAbsenceCounter($studentId);

        if (!$counter) {
            return response()->json(['error' => 'Counter not found'], 404);
        }

        return response()->json($counter);
    }

    public function getAlerts($studentId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $alerts = $this->absenceRepository->getStudentAbsenceAlerts($studentId, $perPage);

        return response()->json($alerts);
    }

    public function getPendingAlerts(): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $alerts = $this->absenceRepository->getPendingAlerts($perPage);

        return response()->json($alerts);
    }

    public function acknowledge(AbsenceAlert $alert): JsonResponse
    {
        $acknowledged = $this->absenceRepository->acknowledgeAlert($alert);

        return response()->json($acknowledged);
    }

    public function checkThresholds($studentId): JsonResponse
    {
        $threshold = request()->input('threshold', 10);

        try {
            $alerts = $this->attendanceService->checkAbsenceThresholds($studentId, $threshold);

            return response()->json([
                'student_id' => $studentId,
                'alerts_created' => count($alerts),
                'alerts' => $alerts,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getStats($studentId): JsonResponse
    {
        $stats = $this->attendanceService->updateAbsenceCounter($studentId);

        return response()->json($stats);
    }
}
