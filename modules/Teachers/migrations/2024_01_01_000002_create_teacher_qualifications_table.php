<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('qualification_name'); // CAPES, Agrégation, Master, etc
            $table->string('issuing_institution'); // Université, Ministère
            $table->year('year_obtained');
            $table->string('diploma_file')->nullable(); // Chemin du fichier
            $table->text('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_qualifications');
    }
};
