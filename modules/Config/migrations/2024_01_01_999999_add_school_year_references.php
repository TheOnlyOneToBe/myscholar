<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // StudentEnrollment
        Schema::table('student_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_enrollments', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Classes
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'school_year_id')) {
                // La colonne existe déjà mais sans FK
                if (Schema::hasColumn('classes', 'school_year_id')) {
                    // Ajouter juste la FK
                    // NOTE: SQLite n'aime pas ajouter FK après coup
                    // On ajoute juste l'index
                    $table->index('school_year_id');
                }
            }
        });

        // Grades
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

        // GradePeriods
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

        // Invoices
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // FeeStructures
        Schema::table('fee_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structures', 'school_year_id')) {
                // La colonne existe déjà
                $table->index('school_year_id');
            }
        });

        // AttendanceSessions
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

        // PaymentPlans
        Schema::table('payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_plans', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('id');
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
        // Optionnel: supprimer les colonnes
        // Note: On ne supprime généralement pas lors du down pour éviter les problèmes
    }
};
