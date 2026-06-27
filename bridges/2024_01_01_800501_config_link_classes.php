<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge: Config ↔ Classes Module
 * Links school years to classes and related tables
 * Dependencies: Config (core), Classes
 */
return new class extends Migration
{
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
    }

    public function down(): void
    {
        // Down migrations intentionally left empty for production safety
    }
};
