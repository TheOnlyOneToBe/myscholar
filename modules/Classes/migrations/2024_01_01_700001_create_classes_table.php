<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->enum('level', ['seconde', 'premiere', 'terminale']);
            $table->enum('filiere', ['general', 'technique', 'pro']);
            $table->string('class_name');
            $table->integer('max_capacity');
            $table->integer('current_capacity')->default(0);
            $table->unsignedBigInteger('professor_principal_id')->nullable();
            $table->timestamps();
            $table->index('school_year_id');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
