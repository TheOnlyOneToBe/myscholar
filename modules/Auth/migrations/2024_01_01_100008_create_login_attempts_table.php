<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();

            // Relation (nullable car on peut logger des tentatives sans user trouvé)
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Email ou username tenté
            $table->string('email_or_username');

            // Informations réseau
            $table->string('ip_address');
            $table->text('user_agent')->nullable();

            // Résultat
            $table->boolean('success')->default(false);
            $table->string('reason')->nullable()->comment('Raison si échoué: wrong_password, account_locked, user_not_found, etc.');

            // Timestamp
            $table->timestamp('attempted_at')->useCurrent();

            // Indices
            $table->index('user_id');
            $table->index('email_or_username');
            $table->index('ip_address');
            $table->index('success');
            $table->index('attempted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
