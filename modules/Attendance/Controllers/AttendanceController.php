<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Requests\AttendanceMarkRequest;
use Modules\Attendance\Models\AttendanceRecord;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceRecordRepository $recordRepository,
        private AttendanceService $attendanceService,
    ) {}

    public function index(): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $records = $this->recordRepository->all($perPage);

        return response()->json($records);
    }

    public function store(AttendanceMarkRequest $request): JsonResponse
    {
        $data = $request->validated();
        $record = $this->attendanceService->markAttendance(
            $data['attendance_session_id'],
            $data['student_id'],
            $data['status'],
            $data['notes'] ?? null
        );

        return response()->json($record, 201);
    }

    public function show(AttendanceRecord $record): JsonResponse
    {
        return response()->json($record->load(['session', 'student']));
    }

    public function update(AttendanceRecord $record, AttendanceMarkRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->attendanceService->markAttendance(
            $data['attendance_session_id'],
            $data['student_id'],
            $data['status'],
            $data['notes'] ?? null
        );

        return response()->json($updated);
    }

    public function destroy(AttendanceRecord $record): JsonResponse
    {
        $this->recordRepository->delete($record);

        return response()->json(['message' => 'Record deleted successfully']);
    }

    public function byStudent($studentId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $records = $this->recordRepository->findByStudent($studentId, $perPage);

        return response()->json($records);
    }

    public function bySession($sessionId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $records = $this->recordRepository->findBySession($sessionId, $perPage);

        return response()->json($records);
    }

    public function studentAttendanceRate($studentId): JsonResponse
    {
        $rate = $this->attendanceService->calculateStudentAttendanceRate($studentId);

        return response()->json([
            'student_id' => $studentId,
            'attendance_rate' => $rate,
            'is_passing' => $rate >= 80,
        ]);
    }

    public function classOverview($classId): JsonResponse
    {
        $date = request()->input('date', now()->format('Y-m-d'));
        $overview = $this->attendanceService->getClassAttendanceOverview($classId, $date);

        return response()->json($overview);
    }
}
