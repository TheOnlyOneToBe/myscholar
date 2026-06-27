<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_assignments') && Schema::hasTable('students') && Schema::hasTable('classes')) {
            Schema::table('class_assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('class_assignments', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable();
                }
                if (!Schema::hasColumn('class_assignments', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable();
                }
                try {
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                    $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign keys already exist
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('class_assignments')) {
            Schema::table('class_assignments', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['student_id', 'class_id']);
            });
        }
    }
};
