<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('teacher_code')->unique(); // Matricule
            $table->string('specialization')->nullable(); // Ex: Mathématiques
            $table->string('qualification_level')->nullable(); // Bac+2, Bac+3, Doctorat
            $table->date('hire_date')->nullable(); // Date d'embauche
            $table->enum('filiere', ['generale', 'technique'])->nullable(); // Filière : Générale ou Technique
            $table->string('office_location')->nullable(); // Salle de bureau
            $table->integer('years_of_experience')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('bio')->nullable();
            $table->string('phone_office')->nullable();
            $table->string('email_office')->nullable();
            $table->timestamps();

            $table->index('specialization');
            $table->index('filiere');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
