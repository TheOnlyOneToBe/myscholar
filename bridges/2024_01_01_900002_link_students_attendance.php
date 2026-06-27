<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_records') && Schema::hasTable('students')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_records', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }

        if (Schema::hasTable('absence_counters') && Schema::hasTable('students')) {
            Schema::table('absence_counters', function (Blueprint $table) {
                if (!Schema::hasColumn('absence_counters', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }

        if (Schema::hasTable('absence_alerts') && Schema::hasTable('students')) {
            Schema::table('absence_alerts', function (Blueprint $table) {
                if (!Schema::hasColumn('absence_alerts', 'student_id')) {
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
        if (Schema::hasTable('attendance_records')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
        if (Schema::hasTable('absence_counters')) {
            Schema::table('absence_counters', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
        if (Schema::hasTable('absence_alerts')) {
            Schema::table('absence_alerts', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id']);
            });
        }
    }
};
