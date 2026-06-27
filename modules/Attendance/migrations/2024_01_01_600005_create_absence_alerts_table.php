<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->integer('absence_count_reached');
            $table->timestamp('alert_sent_date');
            $table->string('alert_sent_to')->nullable();
            $table->timestamps();
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_alerts');
    }
};
