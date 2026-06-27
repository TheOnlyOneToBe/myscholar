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
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('reason_change', ['transfer', 'filiere_change'])->nullable();
            $table->timestamps();
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_assignments');
    }
};
