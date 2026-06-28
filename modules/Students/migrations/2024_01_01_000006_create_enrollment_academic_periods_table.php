<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_academic_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->timestamps();

            // Unique constraint: Un trimestre par inscription
            $table->unique(['enrollment_id', 'academic_period_id']);

            // Indexes
            $table->index('enrollment_id');
            $table->index('academic_period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_academic_periods');
    }
};
