<?php

namespace Modules\Reporting\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Reporting\Services\ReportService;
use Modules\Reporting\Services\ExportService;
use Modules\Reporting\Services\AnalyticsService;
use Modules\Students\Models\Student;

class ReportController
{
    public function __construct(
        private ReportService $reportService,
        private ExportService $exportService,
        private AnalyticsService $analyticsService
    ) {}

    public function studentAcademicReport(Student $student, Request $request): JsonResponse
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->reportService->getStudentAcademicReport($student, $academicYearId);

        return response()->json([
            'data' => $report,
            'timestamp' => now(),
        ]);
    }

    public function studentAttendanceReport(Student $student, Request $request): JsonResponse
    {
        $monthsBack = $request->get('months_back', 3);
        $report = $this->reportService->getStudentAttendanceReport($student, $monthsBack);

        return response()->json([
            'data' => $report,
            'timestamp' => now(),
        ]);
    }

    public function studentFinancialReport(Student $student): JsonResponse
    {
        $report = $this->reportService->getStudentFinancialReport($student);

        return response()->json([
            'data' => $report,
            'timestamp' => now(),
        ]);
    }

    public function classReport(Request $request): JsonResponse
    {
        $classId = $request->get('class_id');
        $academicYearId = $request->get('academic_year_id');

        if (!$classId) {
            return response()->json([
                'message' => 'class_id is required'
            ], 400);
        }

        $report = $this->reportService->getClassReport($classId, $academicYearId);

        return response()->json([
            'data' => $report,
            'timestamp' => now(),
        ]);
    }

    public function schoolSummary(Request $request): JsonResponse
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->reportService->getSchoolSummaryReport($academicYearId);

        return response()->json([
            'data' => $report,
            'timestamp' => now(),
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $analytics = $this->analyticsService->getDashboardAnalytics();

        return response()->json([
            'data' => $analytics,
            'timestamp' => now(),
        ]);
    }

    public function trendAnalysis(Request $request): JsonResponse
    {
        $metric = $request->get('metric', 'grades');
        $months = $request->get('months', 6);

        $trends = $this->analyticsService->getTrendAnalysis($metric, $months);

        return response()->json([
            'data' => $trends,
            'metric' => $metric,
            'months' => $months,
            'timestamp' => now(),
        ]);
    }

    public function studentProgress(Student $student, Request $request): JsonResponse
    {
        $academicYearId = $request->get('academic_year_id');
        $progress = $this->analyticsService->getStudentProgressReport($student, $academicYearId);

        return response()->json([
            'data' => $progress,
            'timestamp' => now(),
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|in:student_academic,student_attendance,student_financial,class,school,grades,attendance,invoices',
            'format' => 'required|in:json,csv,excel,pdf',
            'student_id' => 'sometimes|exists:students,id',
            'class_id' => 'sometimes|exists:classes,id',
            'academic_year_id' => 'sometimes|exists:school_years,id',
            'status' => 'sometimes|string',
        ]);

        $format = $validated['format'];
        $reportType = $validated['report_type'];

        $data = match ($reportType) {
            'student_academic' => $this->exportService->exportStudentReport(
                Student::find($validated['student_id']),
                $format,
                'academic'
            ),
            'student_attendance' => $this->exportService->exportStudentReport(
                Student::find($validated['student_id']),
                $format,
                'attendance'
            ),
            'student_financial' => $this->exportService->exportStudentReport(
                Student::find($validated['student_id']),
                $format,
                'financial'
            ),
            'class' => $this->exportService->exportClassReport(
                $validated['class_id'],
                $format,
                $validated['academic_year_id'] ?? null
            ),
            'school' => $this->exportService->exportSchoolReport(
                $format,
                $validated['academic_year_id'] ?? null
            ),
            'grades' => $this->exportService->exportGradesList(
                $validated['class_id'] ?? null,
                $validated['academic_year_id'] ?? null
            ),
            'attendance' => $this->exportService->exportAttendanceList(
                $validated['class_id'] ?? null
            ),
            'invoices' => $this->exportService->exportInvoicesList(
                $validated['class_id'] ?? null,
                $validated['status'] ?? null
            ),
            default => [],
        };

        return response()->json([
            'format' => $format,
            'report_type' => $reportType,
            'data' => $data,
            'exported_at' => now(),
        ]);
    }
}
