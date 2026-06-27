<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: This migration has been superseded by bridge migrations
 * See:
 *   - bridges/2024_01_01_800503_config_link_grades.php
 *   - bridges/2024_01_01_800504_config_link_attendance.php
 *   - bridges/2024_01_01_800505_config_link_billing.php
 *
 * This file is kept for backward compatibility with existing databases.
 * New installations should use the bridge migrations instead.
 */
return new class extends Migration
{
    /**
     * BATCH 2: Secondary Module Links
     * Grades, Attendance, Billing core tables
     *
     * DEPRECATED: Use bridges/2024_01_01_800503_config_link_*.php instead
     */
    public function up(): void
    {
        // Grades Module
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('averages_cache', function (Blueprint $table) {
            if (!Schema::hasColumn('averages_cache', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('class_averages', function (Blueprint $table) {
            if (!Schema::hasColumn('class_averages', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('appeals', function (Blueprint $table) {
            if (!Schema::hasColumn('appeals', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Attendance Module
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_sessions', 'school_year_id')) {
                // Colonne déjà existante de migration précédente
                $table->index('school_year_id');
            }
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_records', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('attendance_session_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('justifications', function (Blueprint $table) {
            if (!Schema::hasColumn('justifications', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('absence_counters', function (Blueprint $table) {
            if (!Schema::hasColumn('absence_counters', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('absence_alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('absence_alerts', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Billing Module
        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_structures', 'school_year_id')) {
                    // Colonne déjà existante
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'school_year_id')) {
                    // Colonne déjà existante
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('invoice_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('payment_plans')) {
            Schema::table('payment_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_plans', 'school_year_id')) {
                    // Colonne déjà existante
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('installments')) {
            Schema::table('installments', function (Blueprint $table) {
                if (!Schema::hasColumn('installments', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('payment_plan_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('scholarships')) {
            Schema::table('scholarships', function (Blueprint $table) {
                if (!Schema::hasColumn('scholarships', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        if (Schema::hasTable('fee_waivers')) {
            Schema::table('fee_waivers', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_waivers', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Optionnel
    }
};
