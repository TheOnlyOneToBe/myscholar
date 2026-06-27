<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('day_of_week'); // Monday, Tuesday, etc
            $table->time('start_time');
            $table->time('end_time');
            $table->string('subject_code');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Enseignant
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->string('session_type')->default('regular'); // regular, exam, makeup
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['class_id', 'day_of_week']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
