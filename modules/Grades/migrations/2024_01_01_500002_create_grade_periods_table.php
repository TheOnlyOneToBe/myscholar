<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->date('grade_entry_start');
            $table->date('grade_entry_deadline');
            $table->date('publication_date')->nullable();
            $table->timestamps();
            $table->index('school_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_periods');
    }
};
