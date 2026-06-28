<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Attendance\Repositories\AttendanceSessionRepository;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Requests\CreateAttendanceSessionRequest;
use Modules\Attendance\Requests\UpdateAttendanceSessionRequest;
use Modules\Attendance\Models\AttendanceSession;

class AttendanceSessionController extends Controller
{
    public function __construct(
        private AttendanceSessionRepository $sessionRepository,
        private AttendanceService $attendanceService,
    ) {}

    public function index(): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $sessions = $this->sessionRepository->all($perPage);

        return response()->json($sessions);
    }

    public function store(CreateAttendanceSessionRequest $request): JsonResponse
    {
        $session = $this->attendanceService->createSession($request->validated());

        return response()->json($session, 201);
    }

    public function show(AttendanceSession $session): JsonResponse
    {
        $data = [
            'session' => $session,
            'attendance_rate' => $session->getAttendanceRate(),
            'total_records' => $session->records()->count(),
        ];

        return response()->json($data);
    }

    public function update(AttendanceSession $session, UpdateAttendanceSessionRequest $request): JsonResponse
    {
        $updated = $this->attendanceService->updateSession($session, $request->validated());

        return response()->json($updated);
    }

    public function destroy(AttendanceSession $session): JsonResponse
    {
        $this->attendanceService->deleteSession($session);

        return response()->json(['message' => 'Session deleted successfully']);
    }

    public function byClass($classId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $sessions = $this->sessionRepository->findByClass($classId, $perPage);

        return response()->json($sessions);
    }

    public function bySubject($subjectId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $sessions = $this->sessionRepository->findBySubject($subjectId, $perPage);

        return response()->json($sessions);
    }

    public function report($sessionId): JsonResponse
    {
        $report = $this->attendanceService->getSessionAttendanceReport($sessionId);

        if (!$report) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        return response()->json($report);
    }
}
