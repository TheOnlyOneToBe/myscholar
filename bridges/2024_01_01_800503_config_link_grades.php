<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge: Config ↔ Grades Module
 * Links school years to grades and related academic records
 * Dependencies: Config (core), Grades
 */
return new class extends Migration
{
    public function up(): void
    {
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
                $table->unsignedBigInteger('school_year_id')->nullable()->after('grade_id');
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
        // Down migrations intentionally left empty for production safety
    }
};
