<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge: Config ↔ Attendance Module
 * Links school years to attendance and related records
 * Dependencies: Config (core), Attendance
 */
return new class extends Migration
{
    public function up(): void
    {
        // Attendance Module - Sessions (Foundation)
        if (Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_sessions', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        // Attendance Records
        if (Schema::hasTable('attendance_records')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_records', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        // Justifications
        if (Schema::hasTable('justifications')) {
            Schema::table('justifications', function (Blueprint $table) {
                if (!Schema::hasColumn('justifications', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('attendance_record_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        // Absence Counters
        if (Schema::hasTable('absence_counters')) {
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
        }

        // Absence Alerts
        if (Schema::hasTable('absence_alerts')) {
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
        }
    }

    public function down(): void
    {
        // Down migrations intentionally left empty for production safety
    }
};
