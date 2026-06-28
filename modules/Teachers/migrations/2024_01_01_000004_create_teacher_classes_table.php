<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();
            $table->integer('hours_per_week')->default(0);
            $table->enum('status', ['active', 'suspended', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'class_id', 'subject_id', 'school_year_id']);
            $table->index('teacher_id');
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classes');
    }
};
