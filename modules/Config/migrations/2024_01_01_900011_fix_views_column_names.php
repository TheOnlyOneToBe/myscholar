<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix database views to use correct column names
     */
    public function up(): void
    {
        // Drop and recreate views with correct column names
        DB::statement("DROP VIEW IF EXISTS v_attendance_summary");
        DB::statement("
            CREATE VIEW v_attendance_summary AS
            SELECT
                s.id as student_id,
                s.first_name,
                s.last_name,
                sy.id as school_year_id,
                sy.name as school_year,
                COUNT(DISTINCT ar.id) as total_sessions,
                SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN ar.status = 'late' THEN 1 ELSE 0 END) as late_count,
                CASE
                    WHEN COUNT(DISTINCT ar.id) = 0 THEN NULL
                    ELSE ROUND(100.0 * SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) / COUNT(DISTINCT ar.id), 2)
                END as attendance_percentage
            FROM students s
            LEFT JOIN attendance_records ar ON s.id = ar.student_id
            LEFT JOIN attendance_sessions ats ON ar.attendance_session_id = ats.id
            LEFT JOIN school_years sy ON ats.school_year_id = sy.id
            GROUP BY s.id, sy.id
        ");

        DB::statement("DROP VIEW IF EXISTS v_student_grades_summary");
        DB::statement("
            CREATE VIEW v_student_grades_summary AS
            SELECT
                s.id as student_id,
                s.first_name,
                s.last_name,
                sy.id as school_year_id,
                sy.name as school_year,
                COUNT(g.id) as total_grades,
                ROUND(AVG(g.score), 2) as average_score,
                MIN(g.score) as min_score,
                MAX(g.score) as max_score,
                COUNT(DISTINCT g.subject_id) as subjects_graded
            FROM students s
            LEFT JOIN grades g ON s.id = g.student_id
            LEFT JOIN school_years sy ON g.school_year_id = sy.id
            GROUP BY s.id, sy.id
        ");

        DB::statement("DROP VIEW IF EXISTS v_billing_summary");
        DB::statement("
            CREATE VIEW v_billing_summary AS
            SELECT
                s.id as student_id,
                s.first_name,
                s.last_name,
                COUNT(DISTINCT i.id) as total_invoices,
                SUM(i.amount) as total_amount_due,
                SUM(i.amount_paid) as total_amount_paid,
                SUM(i.amount - i.amount_paid) as outstanding_balance,
                CASE
                    WHEN SUM(i.amount) = 0 THEN NULL
                    ELSE ROUND(100.0 * SUM(i.amount_paid) / SUM(i.amount), 2)
                END as payment_percentage
            FROM students s
            LEFT JOIN invoices i ON s.id = i.student_id
            GROUP BY s.id
        ");
    }

    public function down(): void
    {
        // Recreate views with original (incorrect) column names
        DB::statement("DROP VIEW IF EXISTS v_attendance_summary");
        DB::statement("DROP VIEW IF EXISTS v_student_grades_summary");
        DB::statement("DROP VIEW IF EXISTS v_billing_summary");
    }
};
