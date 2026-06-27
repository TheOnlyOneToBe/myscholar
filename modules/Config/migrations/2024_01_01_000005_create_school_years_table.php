<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // "2024-2025"
            $table->year('start_year'); // 2024
            $table->year('end_year'); // 2025
            $table->date('start_date'); // Date de démarrage
            $table->date('end_date'); // Date de fin
            $table->boolean('is_active')->default(false); // Année scolaire actuelle
            $table->boolean('is_locked')->default(false); // Données verrouillées (archivées)
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['start_year', 'end_year']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
