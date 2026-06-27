<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->foreign('period_id')->references('id')->on('grade_periods')->onDelete('set null');
            $table->enum('evaluation_type', ['CC', 'DS', 'EXAM', 'TP']);
            $table->decimal('score', 4, 2);
            $table->decimal('weight', 3, 1)->default(1);
            $table->unsignedBigInteger('entered_by_teacher_id')->nullable();
            $table->foreign('entered_by_teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('entered_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'subject_id']);
            $table->index('period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
