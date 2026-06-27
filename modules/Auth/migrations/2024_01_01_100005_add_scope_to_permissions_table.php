<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Catégorie de la permission
            // config, students, classes, grades, attendance, billing, users, audit, etc.
            $table->string('category')->nullable()->after('module');

            // Portée de la permission
            // global = s'applique partout
            // by_class = limitée à une classe (Prof Principal)
            // by_subject = limitée à une matière (Enseignant)
            // by_student = limitée à un enfant (Parent)
            $table->string('scope')->default('global')->after('category');

            // Statut actif/inactif
            $table->boolean('is_active')->default(true)->after('scope');

            // Indices
            $table->index('category');
            $table->index('scope');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['scope']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['category', 'scope', 'is_active']);
        });
    }
};
