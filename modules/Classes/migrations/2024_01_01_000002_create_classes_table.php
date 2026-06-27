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
            $table->string('name'); // Nom: "Terminale A1", "Première S2"
            $table->string('code')->unique(); // Code unique
            $table->string('level'); // Niveau: Form 4, Form 5, etc
            $table->string('section')->nullable(); // Section: A, B, C
            $table->string('filiere')->nullable(); // Filière: Science, Littéraire, etc
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->integer('capacity')->default(45);
            $table->integer('current_students')->default(0);
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['level', 'filiere']);
            $table->index('school_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
