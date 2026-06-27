<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('school_year');
            $table->string('class');
            $table->string('filiere');
            $table->decimal('average_grade', 4, 2)->nullable();
            $table->integer('ranking')->nullable();
            $table->string('mention')->nullable();
            $table->enum('status', ['admis', 'ajourné', 'exclu'])->nullable();
            $table->timestamps();
            $table->index('student_id');
            $table->index('school_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_history');
    }
};
