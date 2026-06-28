<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Attendance\Services\BulkAttendanceService;
use Illuminate\Validation\ValidationException;

class BulkAttendanceController extends Controller
{
    public function __construct(
        private BulkAttendanceService $bulkService,
    ) {}

    /**
     * Mark attendance for multiple students at once
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function markBulk(): JsonResponse
    {
        $data = request()->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'records' => 'required|array|max:100',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:present,absent,late,excused,justified',
            'records.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->bulkService->markBulkAttendance(
                request()->user(),
                $data['session_id'],
                $data['records']
            );

            return response()->json([
                'message' => 'Bulk attendance marked successfully',
                'result' => $result,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Bulk operation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Bulk operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate bulk records before marking
     *
     * @return JsonResponse
     */
    public function validateBulk(): JsonResponse
    {
        $data = request()->validate([
            'records' => 'required|array',
        ]);

        $validation = $this->bulkService->validateBulkRecords($data['records']);

        return response()->json($validation);
    }

    /**
     * Get bulk operation summary for a session
     *
     * @param int $sessionId
     * @return JsonResponse
     */
    public function getSummary($sessionId): JsonResponse
    {
        $summary = $this->bulkService->getBulkSummary($sessionId);

        if (empty($summary)) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        return response()->json($summary);
    }

    /**
     * Get bulk import template
     *
     * @return JsonResponse
     */
    public function getTemplate(): JsonResponse
    {
        return response()->json($this->bulkService->getBulkTemplate());
    }

    /**
     * Import bulk attendance from CSV/JSON
     *
     * @return JsonResponse
     */
    public function importBulk(): JsonResponse
    {
        $file = request()->file('file');

        if (!$file) {
            return response()->json([
                'error' => 'No file provided',
            ], 422);
        }

        // Get session ID
        $sessionId = request()->input('session_id');
        if (!$sessionId) {
            return response()->json([
                'error' => 'Session ID required',
            ], 422);
        }

        try {
            $records = $this->parseImportFile($file);

            // Validate records
            $validation = $this->bulkService->validateBulkRecords($records);
            if (!$validation['valid']) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Process bulk operation
            $result = $this->bulkService->markBulkAttendance(
                request()->user(),
                $sessionId,
                $records
            );

            return response()->json([
                'message' => 'Bulk import completed',
                'result' => $result,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse import file (CSV or JSON)
     *
     * @param mixed $file
     * @return array
     */
    private function parseImportFile($file): array
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            return $this->parseCSV($file);
        } elseif ($extension === 'json') {
            return $this->parseJSON($file);
        }

        throw new \Exception('Unsupported file format. Use CSV or JSON.');
    }

    /**
     * Parse CSV file
     *
     * @param mixed $file
     * @return array
     */
    private function parseCSV($file): array
    {
        $records = [];
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        while ($row = fgetcsv($handle)) {
            $record = array_combine($header, $row);
            $records[] = [
                'student_id' => (int) $record['student_id'],
                'status' => $record['status'],
                'notes' => $record['notes'] ?? null,
            ];
        }

        fclose($handle);

        return $records;
    }

    /**
     * Parse JSON file
     *
     * @param mixed $file
     * @return array
     */
    private function parseJSON($file): array
    {
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new \Exception('Invalid JSON format');
        }

        return $data;
    }
}
