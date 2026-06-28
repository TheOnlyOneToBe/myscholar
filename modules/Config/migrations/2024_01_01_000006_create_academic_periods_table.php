<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Trimestre 1", "Semestre 1", etc.
            $table->enum('type', ['term', 'semester', 'quarter', 'year'])->default('term'); // Type de période
            $table->date('start_date'); // Date de début
            $table->date('end_date'); // Date de fin
            $table->year('academic_year'); // Année académique
            $table->integer('order')->default(1); // Ordre dans l'année (1, 2, 3, etc.)
            $table->boolean('is_active')->default(true); // Période active
            $table->text('description')->nullable(); // Description optionnelle
            $table->timestamps();

            $table->index('academic_year');
            $table->index('type');
            $table->unique(['academic_year', 'type', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
