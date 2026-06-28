<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_session_id');
            $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'justified']);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'attendance_session_id']);
            $table->index('student_id');
            $table->index('attendance_session_id');
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
