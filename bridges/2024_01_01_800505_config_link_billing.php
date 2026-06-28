<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge: Config ↔ Billing Module
 * Links school years to billing and payment records
 * Dependencies: Config (core), Billing
 */
return new class extends Migration
{
    public function up(): void
    {
        // Fee Structures
        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_structures', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        // Invoices
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

        // Payments
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

        // Payment Plans
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

        // Payment Installments
        if (Schema::hasTable('payment_installments')) {
            Schema::table('payment_installments', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_installments', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('payment_plan_id');
                    $table->foreign('school_year_id')
                        ->references('id')
                        ->on('school_years')
                        ->onDelete('cascade');
                    $table->index('school_year_id');
                }
            });
        }

        // Fee Waivers
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

        // Payment Transactions
        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_transactions', 'school_year_id')) {
                    $table->unsignedBigInteger('school_year_id')->nullable()->after('payment_id');
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
