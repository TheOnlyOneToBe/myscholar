<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pour les événements de classe
        Schema::create('class_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->string('name', 255);
            $table->date('date');
            $table->enum('type', ['exam', 'control', 'project', 'holiday', 'meeting', 'other']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['class_id', 'date']);
        });

        // Table pour l'horaire des examens
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50);
            $table->integer('total_students')->default(0);
            $table->timestamps();
            $table->index(['exam_date']);
            $table->index(['subject_id']);
        });

        // Table pour les périodes académiques (si elle n'existe pas)
        if (!Schema::hasTable('academic_periods')) {
            Schema::create('academic_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years');
                $table->string('name', 255);
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('type', ['term', 'semester', 'trimester', 'quarter'])->default('term');
                $table->integer('order')->default(0);
                $table->timestamps();
                $table->index(['start_date', 'end_date']);
            });
        }

        // Table pour les vacances scolaires
        Schema::create('school_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['start_date', 'end_date']);
        });

        // Table pour l'horaire (timetable) - si elle n'existe pas
        if (!Schema::hasTable('timetables')) {
            Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_id')->constrained('classes');
                $table->foreignId('subject_id')->constrained('subjects');
                $table->foreignId('teacher_id')->constrained('users');
                $table->tinyInteger('day_of_week'); // 1-6 (lun-sam)
                $table->time('start_time');
                $table->time('end_time');
                $table->string('room', 50)->nullable();
                $table->timestamps();
                $table->index(['class_id', 'day_of_week']);
                $table->unique(['class_id', 'day_of_week', 'start_time']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('school_holidays');
        Schema::dropIfExists('academic_periods');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('class_events');
    }
};
