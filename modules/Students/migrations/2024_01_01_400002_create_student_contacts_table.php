<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->enum('contact_type', ['father', 'mother', 'guardian']);
            $table->string('full_name');
            $table->string('relationship')->nullable();
            $table->string('profession')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_contacts');
    }
};
