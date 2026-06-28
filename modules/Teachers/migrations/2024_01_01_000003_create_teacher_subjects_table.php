<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('proficiency_level')->default(3); // 1-5 (expert)
            $table->year('since_year');
            $table->boolean('is_primary')->default(false); // Spécialité principale
            $table->timestamps();

            $table->unique(['teacher_id', 'subject_id']);
            $table->index('teacher_id');
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};
