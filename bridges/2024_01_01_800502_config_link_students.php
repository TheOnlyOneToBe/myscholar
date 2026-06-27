<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge: Config ↔ Students Module
 * Links school years to student records and history
 * Dependencies: Config (core), Students
 */
return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        // Down migrations intentionally left empty for production safety
    }
};
