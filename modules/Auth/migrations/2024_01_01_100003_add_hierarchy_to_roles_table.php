<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Ajouter hierarchy_level pour la hiérarchie
            // 0 = Admin système (au-dessus de tout)
            // 1 = Proviseur
            // 2 = Censeur
            // 3 = Prof Principal, Chef de Classe
            // 4 = Enseignant
            // 5 = Surveillant
            // 99 = Parent
            // 100 = Élève
            $table->integer('hierarchy_level')->default(99)->after('description');

            // Catégorie du rôle
            // admin, hierarchy, staff, external
            $table->string('category')->default('staff')->after('hierarchy_level');

            // Label pour affichage
            $table->string('label')->nullable()->after('category');

            // Statut actif/inactif
            $table->boolean('is_active')->default(true)->after('label');

            // Indices
            $table->index('hierarchy_level');
            $table->index('category');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['hierarchy_level']);
            $table->dropIndex(['category']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['hierarchy_level', 'category', 'label', 'is_active']);
        });
    }
};
