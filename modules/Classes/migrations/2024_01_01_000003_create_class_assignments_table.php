<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Enseignant
            $table->string('role'); // 'teacher', 'class_teacher', 'coordinator'
            $table->string('subject')->nullable(); // Matière enseignée
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->dateTime('assigned_at')->useCurrent();
            $table->dateTime('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'user_id', 'subject', 'school_year_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_assignments');
    }
};
