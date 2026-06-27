<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasTable('students')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }

        if (Schema::hasTable('scholarships') && Schema::hasTable('students')) {
            Schema::table('scholarships', function (Blueprint $table) {
                if (!Schema::hasColumn('scholarships', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }

        if (Schema::hasTable('fee_waivers') && Schema::hasTable('students')) {
            Schema::table('fee_waivers', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_waivers', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
        if (Schema::hasTable('scholarships')) {
            Schema::table('scholarships', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
        if (Schema::hasTable('fee_waivers')) {
            Schema::table('fee_waivers', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
    }
};
