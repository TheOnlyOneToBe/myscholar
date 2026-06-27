<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing school_year_id columns to tables
     * that were assumed to already exist in 800501/800502
     */
    public function up(): void
    {
        // Attendance Module
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

        // Billing Module
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

        Schema::table('payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_plans', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable()->after('student_id');
                $table->foreign('school_year_id')
                    ->references('id')
                    ->on('school_years')
                    ->onDelete('cascade');
                $table->index('school_year_id');
            }
        });

        // Billing Module - fee_structures needs school_year_id if not already present
        Schema::table('fee_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structures', 'school_year_id')) {
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
        // Optionnel
    }
};
