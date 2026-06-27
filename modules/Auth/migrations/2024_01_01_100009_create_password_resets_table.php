<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();

            // Email de l'utilisateur
            $table->string('email')->index();

            // Token de réinitialisation (hashed)
            $table->string('token')->unique()->comment('Token SHA256 envoyé par email');

            // Timestamp de création (expiré après 1 heure)
            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
