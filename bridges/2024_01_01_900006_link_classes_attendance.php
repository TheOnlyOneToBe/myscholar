<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_sessions') && Schema::hasTable('classes')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_sessions', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable();
                }
                try {
                    $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }

        if (Schema::hasTable('attendance_records') && Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_records', 'session_id')) {
                    $table->unsignedBigInteger('session_id')->nullable();
                }
                try {
                    $table->foreign('session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['class_id']);
            });
        }
        if (Schema::hasTable('attendance_records')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['session_id']);
            });
        }
    }
};
