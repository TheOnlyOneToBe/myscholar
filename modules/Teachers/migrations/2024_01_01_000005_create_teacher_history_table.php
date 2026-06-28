<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('action'); // hired, transferred, promoted, demoted, retired
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Données additionnelles
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('teacher_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_history');
    }
};
