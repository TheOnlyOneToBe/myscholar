<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->integer('total_absences')->default(0);
            $table->integer('unjustified_absences')->default(0);
            $table->timestamps();
            $table->unique('student_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_counters');
    }
};
