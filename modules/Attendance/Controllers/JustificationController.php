<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Attendance\Repositories\JustificationRepository;
use Modules\Attendance\Services\JustificationService;
use Modules\Attendance\Requests\JustificationRequest;
use Modules\Attendance\Requests\ReviewJustificationRequest;
use Modules\Attendance\Models\Justification;

class JustificationController extends Controller
{
    public function __construct(
        private JustificationRepository $justificationRepository,
        private JustificationService $justificationService,
    ) {}

    public function index(): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $status = request()->input('status');

        if ($status) {
            $justifications = $this->justificationRepository->findByStatus($status, $perPage);
        } else {
            $justifications = $this->justificationRepository->all($perPage);
        }

        return response()->json($justifications);
    }

    public function store(JustificationRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $justification = $this->justificationService->submitJustification(
                $data['student_id'],
                $data['attendance_record_id'],
                $data['reason'],
                $data['supporting_document'] ?? null
            );

            return response()->json($justification, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Justification $justification): JsonResponse
    {
        return response()->json($justification->load(['student', 'record']));
    }

    public function destroy(Justification $justification): JsonResponse
    {
        try {
            $this->justificationService->deleteJustification($justification);

            return response()->json(['message' => 'Justification deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function approve(Justification $justification): JsonResponse
    {
        $approved = $this->justificationService->approveJustification($justification);

        return response()->json($approved);
    }

    public function reject(Justification $justification, ReviewJustificationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $rejected = $this->justificationService->rejectJustification(
            $justification,
            $data['rejection_reason']
        );

        return response()->json($rejected);
    }

    public function byStudent($studentId): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $justifications = $this->justificationService->getStudentJustifications($studentId, $perPage);

        return response()->json($justifications);
    }

    public function pending(): JsonResponse
    {
        $perPage = request()->input('per_page', 25);
        $pending = $this->justificationService->getPendingJustifications($perPage);

        return response()->json($pending);
    }
}
