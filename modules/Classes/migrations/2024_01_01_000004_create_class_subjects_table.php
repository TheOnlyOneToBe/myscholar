<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('subject_code'); // Code unique: MATH, FR, ENG
            $table->string('subject_name');
            $table->integer('hours_per_week')->default(0); // Heures par semaine
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['class_id', 'subject_code', 'school_year_id']);
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
