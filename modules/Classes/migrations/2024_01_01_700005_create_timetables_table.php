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
            $table->unsignedBigInteger('class_id');
            $table->integer('day_of_week');
            $table->time('time_start');
            $table->time('time_end');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['class_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
