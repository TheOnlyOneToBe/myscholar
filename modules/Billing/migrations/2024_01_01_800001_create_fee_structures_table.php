<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->string('level')->nullable();
            $table->enum('filiere', ['general', 'technique', 'pro'])->nullable();
            $table->decimal('inscription_fee', 10, 2)->default(0);
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('trimester_fee', 10, 2)->default(0);
            $table->decimal('material_fee_t1', 10, 2)->default(0);
            $table->decimal('material_fee_t2', 10, 2)->default(0);
            $table->decimal('material_fee_t3', 10, 2)->default(0);
            $table->decimal('exam_fee_bac_white', 10, 2)->default(0);
            $table->decimal('exam_fee_bac_official', 10, 2)->default(0);
            $table->json('optional_fees')->nullable();
            $table->timestamps();
            $table->unique(['school_year_id', 'level', 'filiere']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
