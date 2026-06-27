<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            // Dates pour rôles multiples et temporaires
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable()->comment('NULL = rôle permanent, Date = rôle temporaire');

            // Audit
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('reason')->nullable()->comment('Raison de l\'assignation du rôle');

            $table->timestamps();

            // Indices
            $table->unique(['user_id', 'role_id', 'started_at'])->comment('Un user ne peut avoir le même rôle qu\'une fois à la fois');
            $table->index('ended_at');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
