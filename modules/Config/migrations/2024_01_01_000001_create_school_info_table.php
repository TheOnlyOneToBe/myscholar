<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_info', function (Blueprint $table) {
            $table->id();

            // Identité
            $table->string('name');
            $table->string('acronym', 50)->nullable();
            $table->string('motto')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->enum('school_type', ['public', 'prive', 'confessionnel'])->default('prive');

            // Coordonnées
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('po_box', 50)->nullable();

            // Infos administratives
            $table->string('approval_number', 100)->nullable();
            $table->string('creation_decree')->nullable();
            $table->string('founder_name')->nullable();
            $table->string('director_name')->nullable();
            $table->year('foundation_year')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_info');
    }
};
