<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: This migration has been superseded by bridge migrations
 * See: bridges/2024_01_01_800501_config_link_classes.php
 *
 * This file is kept for backward compatibility with existing databases.
 * New installations should use the bridge migration instead.
 */
return new class extends Migration
{
    /**
     * BATCH 1: Primary Module Bridges
     * Classes, StudentEnrollments, GradePeriods
     *
     * DEPRECATED: Use bridges/2024_01_01_800501_config_link_classes.php instead
     */
    public function up(): void
    {
        // Classes Module
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('class_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('class_assignments', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('class_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('class_subjects', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        Schema::table('timetables', function (Blueprint $table) {
            if (!Schema::hasColumn('timetables', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('class_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Students Module - Enrollments
        Schema::table('student_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_enrollments', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
                // Unique constraint: un étudiant ne peut s'inscrire qu'une fois par année
                $table->unique(['student_id', 'class_id', 'school_year_id']);
            }
        });

        Schema::table('student_history', function (Blueprint $table) {
            if (!Schema::hasColumn('student_history', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Grades Module - Periods (Foundation)
        Schema::table('grade_periods', function (Blueprint $table) {
            if (!Schema::hasColumn('grade_periods', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Attendance Module - Sessions (Core Bridge)
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

    public function down(): void
    {
        // Optionnel: on ne supprime généralement pas en production
    }
};
