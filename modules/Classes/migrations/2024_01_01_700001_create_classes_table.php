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
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->enum('level', ['seconde', 'premiere', 'terminale'])->nullable();
            $table->enum('filiere', ['general', 'technique', 'pro'])->nullable();
            $table->integer('max_students')->default(45);
            $table->unsignedBigInteger('class_supervisor_id')->nullable();
            $table->timestamps();
            $table->index('school_year_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
