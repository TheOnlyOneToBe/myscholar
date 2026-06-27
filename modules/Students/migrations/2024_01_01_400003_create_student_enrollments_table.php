<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('filiere')->nullable();
            $table->string('level')->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'suspended', 'withdrawn', 'graduated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('student_id');
            $table->index('school_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
