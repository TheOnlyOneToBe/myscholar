<?php

namespace Modules\Reporting\Services;

use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Billing\Models\Invoice;
use Carbon\Carbon;

class ExportService
{
    public function exportStudentReport(Student $student, $format = 'json', $reportType = 'academic')
    {
        $reportService = new ReportService();

        $data = match ($reportType) {
            'academic' => $reportService->getStudentAcademicReport($student),
            'attendance' => $reportService->getStudentAttendanceReport($student),
            'financial' => $reportService->getStudentFinancialReport($student),
            default => $reportService->getStudentAcademicReport($student),
        };

        return match ($format) {
            'csv' => $this->convertToCSV($data),
            'pdf' => $this->convertToPDF($data),
            'excel' => $this->convertToExcel($data),
            'json' => $data,
            default => $data,
        };
    }

    public function exportClassReport($classId, $format = 'json', $academicYearId = null)
    {
        $reportService = new ReportService();
        $data = $reportService->getClassReport($classId, $academicYearId);

        return match ($format) {
            'csv' => $this->convertToCSV($data),
            'pdf' => $this->convertToPDF($data),
            'excel' => $this->convertToExcel($data),
            'json' => $data,
            default => $data,
        };
    }

    public function exportSchoolReport($format = 'json', $academicYearId = null)
    {
        $reportService = new ReportService();
        $data = $reportService->getSchoolSummaryReport($academicYearId);

        return match ($format) {
            'csv' => $this->convertToCSV($data),
            'pdf' => $this->convertToPDF($data),
            'excel' => $this->convertToExcel($data),
            'json' => $data,
            default => $data,
        };
    }

    public function exportGradesList($classId = null, $academicYearId = null)
    {
        $query = Grade::query();

        if ($classId) {
            $query->whereHas('student', fn($q) =>
                $q->where('class_id', $classId)
            );
        }

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $grades = $query->with('student', 'subject', 'teacher')->get();

        return $grades->map(fn($g) => [
            'student' => $g->student->fullName ?? '',
            'subject' => $g->subject->name ?? '',
            'teacher' => $g->teacher->fullName ?? '',
            'score' => $g->score,
            'graded_at' => $g->graded_at,
        ]);
    }

    public function exportAttendanceList($classId = null, $monthsBack = 3)
    {
        $fromDate = Carbon::now()->subMonths($monthsBack);

        $query = AttendanceRecord::where('date', '>=', $fromDate);

        if ($classId) {
            $query->whereHas('student', fn($q) =>
                $q->where('class_id', $classId)
            );
        }

        $records = $query->with('student', 'session')->get();

        return $records->map(fn($r) => [
            'student' => $r->student->fullName ?? '',
            'date' => $r->date,
            'status' => $r->status,
            'session' => $r->session->subject ?? '',
            'time_marked' => $r->created_at,
        ]);
    }

    public function exportInvoicesList($classId = null, $status = null)
    {
        $query = Invoice::query();

        if ($classId) {
            $query->whereHas('student', fn($q) =>
                $q->where('class_id', $classId)
            );
        }

        if ($status) {
            $query->where('status', $status);
        }

        $invoices = $query->with('student', 'student.class')->get();

        return $invoices->map(fn($i) => [
            'student' => $i->student->fullName ?? '',
            'class' => $i->student->class->name ?? '',
            'amount' => $i->amount,
            'status' => $i->status,
            'due_date' => $i->due_date,
            'description' => $i->description,
        ]);
    }

    private function convertToCSV($data)
    {
        // Simplified CSV conversion - could be enhanced with proper CSV formatting
        return json_encode($data);
    }

    private function convertToPDF($data)
    {
        // PDF conversion would use a library like MPDF or TCPDF
        // For now, returning JSON representation
        return json_encode([
            'format' => 'pdf',
            'data' => $data,
            'generated_at' => now(),
        ]);
    }

    private function convertToExcel($data)
    {
        // Excel conversion would use a library like PhpSpreadsheet
        // For now, returning JSON representation
        return json_encode([
            'format' => 'excel',
            'data' => $data,
            'generated_at' => now(),
        ]);
    }
}
