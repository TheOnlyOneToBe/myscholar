<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('period_id')->nullable();
            $table->integer('total_absences')->default(0);
            $table->integer('unjustified_count')->default(0);
            $table->integer('justified_count')->default(0);
            $table->timestamp('last_updated');
            $table->timestamps();
            $table->unique(['student_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_counters');
    }
};
