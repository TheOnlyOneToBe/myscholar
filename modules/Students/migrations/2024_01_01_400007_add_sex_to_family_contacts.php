<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_contacts', function (Blueprint $table) {
            $table->char('sex', 1)->nullable()->after('last_name'); // M or F
            $table->index('sex');
        });
    }

    public function down(): void
    {
        Schema::table('family_contacts', function (Blueprint $table) {
            $table->dropIndex(['sex']);
            $table->dropColumn('sex');
        });
    }
};
