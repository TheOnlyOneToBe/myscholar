<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('grade_period_id')->nullable()->change();
            $table->foreignId('school_year_id')->nullable()->change();
            $table->foreignId('teacher_id')->nullable()->change();
        });

        Schema::table('grade_averages', function (Blueprint $table) {
            $table->foreignId('grade_period_id')->nullable()->change();
            $table->foreignId('school_year_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Migration is not reversible as we're just adding nullability
    }
};
