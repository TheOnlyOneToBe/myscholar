<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('sex', ['M', 'F']);
            $table->string('place_of_birth')->nullable();
            $table->string('id_number', 100)->nullable();
            $table->string('photo_url')->nullable();
            $table->unsignedBigInteger('current_class_id')->nullable();
            $table->string('current_filiere')->nullable();
            $table->enum('enrollment_status', ['active', 'suspended', 'withdrawn', 'graduated'])->default('active');
            $table->timestamps();
            $table->index('enrollment_status');
            $table->index('current_class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
