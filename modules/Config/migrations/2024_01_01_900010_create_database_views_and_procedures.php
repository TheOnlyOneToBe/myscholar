<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create database views and triggers to optimize queries
     * and reduce burden on the PHP application
     * Note: SQLite doesn't have stored procedures, but we use views and triggers instead
     */
    public function up(): void
    {
        // ========== VIEWS ==========

        // View: Active school year with stats
        DB::statement("
            DROP VIEW IF EXISTS v_active_school_year
        ");
        DB::statement("
            CREATE VIEW v_active_school_year AS
            SELECT
                sy.id,
                sy.name,
                sy.start_year,
                sy.end_year,
                sy.start_date,
                sy.end_date,
                sy.is_active,
                sy.is_locked,
                sy.description,
                CAST(JULIANDAY(sy.end_date) - JULIANDAY(sy.start_date) AS INTEGER) as total_days,
                CASE
                    WHEN date('now') < date(sy.start_date) THEN 0
                    WHEN date('now') > date(sy.end_date) THEN 100
                    ELSE CAST((JULIANDAY(date('now')) - JULIANDAY(sy.start_date)) /
                             (JULIANDAY(sy.end_date) - JULIANDAY(sy.start_date)) * 100 AS INTEGER)
                END as progress_percentage,
                sy.created_at,
                sy.updated_at
            FROM school_years sy
            WHERE sy.is_active = true
            LIMIT 1
        ");

        // View: School year with student enrollment counts
        DB::statement("DROP VIEW IF EXISTS v_school_year_enrollments");
        DB::statement("
            CREATE VIEW v_school_year_enrollments AS
            SELECT
                sy.id,
                sy.name,
                sy.start_year,
                sy.end_year,
                sy.is_active,
                sy.is_locked,
                COUNT(DISTINCT se.student_id) as total_students,
                COUNT(DISTINCT se.class_id) as total_classes,
                sy.created_at,
                sy.updated_at
            FROM school_years sy
            LEFT JOIN student_enrollments se ON sy.id = se.school_year_id
            GROUP BY sy.id
        ");

        // View: Classes with enrollment and grade statistics
        if (Schema::hasTable('classes') && Schema::hasTable('grades')) {
            DB::statement("DROP VIEW IF EXISTS v_class_statistics");
            DB::statement("
                CREATE VIEW v_class_statistics AS
                SELECT
                    c.id,
                    c.school_year_id,
                    c.code,
                    c.name,
                    sy.name as school_year_name,
                    COUNT(DISTINCT ca.student_id) as total_students,
                    COUNT(DISTINCT g.student_id) as graded_students,
                    ROUND(AVG(g.score), 2) as average_score,
                    COUNT(DISTINCT subj.id) as total_subjects,
                    c.created_at,
                    c.updated_at
                FROM classes c
                LEFT JOIN school_years sy ON c.school_year_id = sy.id
                LEFT JOIN class_assignments ca ON c.id = ca.class_id
                LEFT JOIN grades g ON c.id = g.class_id AND g.school_year_id = c.school_year_id
                LEFT JOIN class_subjects cs ON c.id = cs.class_id
                LEFT JOIN subjects subj ON cs.subject_id = subj.id
                GROUP BY c.id
            ");
        }

        // View: Student grades summary by year
        if (Schema::hasTable('grades')) {
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
        }

        // View: Attendance summary by student and year
        if (Schema::hasTable('attendance_records') && Schema::hasTable('attendance_sessions')) {
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
        }

        // View: Billing summary by student and year
        if (Schema::hasTable('invoices')) {
            DB::statement("DROP VIEW IF EXISTS v_billing_summary");
            DB::statement("
                CREATE VIEW v_billing_summary AS
                SELECT
                    s.id as student_id,
                    s.first_name,
                    s.last_name,
                    sy.id as school_year_id,
                    sy.name as school_year,
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
                LEFT JOIN school_years sy ON i.school_year_id = sy.id
                GROUP BY s.id, sy.id
            ");
        }

        // View: School year comparison (year-over-year metrics)
        if (Schema::hasTable('classes') && Schema::hasTable('invoices') && Schema::hasTable('grades')) {
            DB::statement("DROP VIEW IF EXISTS v_school_year_comparison");
            DB::statement("
                CREATE VIEW v_school_year_comparison AS
                SELECT
                    sy.id,
                    sy.name as school_year,
                    sy.start_year,
                    sy.end_year,
                    (SELECT COUNT(DISTINCT student_id) FROM student_enrollments WHERE school_year_id = sy.id) as total_students,
                    (SELECT COUNT(DISTINCT class_id) FROM classes WHERE school_year_id = sy.id) as total_classes,
                    (SELECT AVG(score) FROM grades WHERE school_year_id = sy.id) as average_grade,
                    (SELECT SUM(amount) FROM invoices WHERE school_year_id = sy.id) as total_revenue,
                    (SELECT SUM(amount_paid) FROM invoices WHERE school_year_id = sy.id) as amount_collected,
                    sy.is_active,
                    sy.is_locked
                FROM school_years sy
            ");
        }

        // ========== TRIGGERS FOR AUTOMATION ==========

        // Trigger: When a school year is activated, deactivate others
        DB::statement("DROP TRIGGER IF EXISTS trg_activate_school_year");
        DB::statement("
            CREATE TRIGGER trg_activate_school_year
            AFTER UPDATE OF is_active ON school_years
            FOR EACH ROW
            WHEN NEW.is_active = true
            BEGIN
                UPDATE school_years
                SET is_active = false, updated_at = CURRENT_TIMESTAMP
                WHERE id != NEW.id;
            END;
        ");

        // Trigger: Prevent modification of locked school years
        DB::statement("DROP TRIGGER IF EXISTS trg_prevent_locked_year_update");
        DB::statement("
            CREATE TRIGGER trg_prevent_locked_year_update
            BEFORE UPDATE ON school_years
            FOR EACH ROW
            WHEN OLD.is_locked = true AND NEW.is_locked = true
            BEGIN
                SELECT RAISE(ABORT, 'Cannot modify a locked (archived) school year');
            END;
        ");

        // Trigger: Update timestamp when school year is modified
        DB::statement("DROP TRIGGER IF EXISTS trg_school_year_updated");
        DB::statement("
            CREATE TRIGGER trg_school_year_updated
            AFTER UPDATE ON school_years
            FOR EACH ROW
            BEGIN
                UPDATE school_years SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
            END;
        ");

        // Indexes for performance on common queries
        DB::statement("CREATE INDEX IF NOT EXISTS idx_school_years_is_active ON school_years(is_active)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_school_years_is_locked ON school_years(is_locked)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_school_years_start_year ON school_years(start_year)");

        if (Schema::hasTable('student_enrollments')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_student_enrollments_school_year ON student_enrollments(school_year_id)");
        }

        if (Schema::hasTable('classes')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_classes_school_year ON classes(school_year_id)");
        }

        if (Schema::hasTable('grades')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_grades_school_year ON grades(school_year_id)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_grades_student_school ON grades(student_id, school_year_id)");
        }

        if (Schema::hasTable('attendance_sessions')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_attendance_sessions_school_year ON attendance_sessions(school_year_id)");
        }

        if (Schema::hasTable('attendance_records')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_attendance_records_status ON attendance_records(status)");
        }

        if (Schema::hasTable('invoices')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_invoices_school_year ON invoices(school_year_id)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_invoices_student_school ON invoices(student_id, school_year_id)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices(status)");
        }
    }

    public function down(): void
    {
        // Drop triggers
        DB::statement("DROP TRIGGER IF EXISTS trg_activate_school_year");
        DB::statement("DROP TRIGGER IF EXISTS trg_prevent_locked_year_update");
        DB::statement("DROP TRIGGER IF EXISTS trg_school_year_updated");

        // Drop views
        DB::statement("DROP VIEW IF EXISTS v_active_school_year");
        DB::statement("DROP VIEW IF EXISTS v_school_year_enrollments");
        DB::statement("DROP VIEW IF EXISTS v_class_statistics");
        DB::statement("DROP VIEW IF EXISTS v_student_grades_summary");
        DB::statement("DROP VIEW IF EXISTS v_attendance_summary");
        DB::statement("DROP VIEW IF EXISTS v_billing_summary");
        DB::statement("DROP VIEW IF EXISTS v_school_year_comparison");

        // Drop indices
        DB::statement("DROP INDEX IF EXISTS idx_school_years_is_active");
        DB::statement("DROP INDEX IF EXISTS idx_school_years_is_locked");
        DB::statement("DROP INDEX IF EXISTS idx_school_years_start_year");
        DB::statement("DROP INDEX IF EXISTS idx_student_enrollments_school_year");
        DB::statement("DROP INDEX IF EXISTS idx_classes_school_year");
        DB::statement("DROP INDEX IF EXISTS idx_grades_school_year");
        DB::statement("DROP INDEX IF EXISTS idx_grades_student_school");
        DB::statement("DROP INDEX IF EXISTS idx_attendance_sessions_school_year");
        DB::statement("DROP INDEX IF EXISTS idx_attendance_records_status");
        DB::statement("DROP INDEX IF EXISTS idx_invoices_school_year");
        DB::statement("DROP INDEX IF EXISTS idx_invoices_student_school");
        DB::statement("DROP INDEX IF EXISTS idx_invoices_status");
    }
};
