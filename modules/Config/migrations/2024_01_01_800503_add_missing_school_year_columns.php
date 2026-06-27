<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: This migration has been superseded by bridge migrations
 * See:
 *   - bridges/2024_01_01_800505_config_link_billing.php
 *
 * This file is kept for backward compatibility with existing databases.
 * New installations should use the bridge migrations instead.
 */
return new class extends Migration
{
    /**
     * Add missing school_year_id columns to tables
     * that were assumed to already exist in 800501/800502
     *
     * DEPRECATED: Use bridges/2024_01_01_800505_config_link_billing.php instead
     */
    public function up(): void
    {
        // Attendance Module
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

        // Billing Module
        if (Schema::hasTable('invoices')) {
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
        }

        if (Schema::hasTable('payment_plans')) {
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
        }

        // Billing Module - fee_structures needs school_year_id if not already present
        if (Schema::hasTable('fee_structures')) {
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
    }

    public function down(): void
    {
        // Optionnel
    }
};
