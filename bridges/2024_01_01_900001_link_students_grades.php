<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('grades') && Schema::hasTable('students')) {
            Schema::table('grades', function (Blueprint $table) {
                if (!Schema::hasColumn('grades', 'student_id_fk')) {
                    $table->unsignedBigInteger('student_id_fk')->nullable();
                    $table->foreign('student_id_fk', 'fk_grades_students')->references('id')->on('students')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('grades')) {
            Schema::table('grades', function (Blueprint $table) {
                if (Schema::hasColumn('grades', 'student_id_fk')) {
                    $table->dropForeignKey('fk_grades_students');
                    $table->dropColumn('student_id_fk');
                }
            });
        }
    }
};
