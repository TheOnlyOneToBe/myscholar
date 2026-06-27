<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->enum('scholarship_type', ['bourse_gouvernement', 'fratrie', 'enfant_personnel', 'special']);
            $table->integer('percentage')->default(0);
            $table->string('reason')->nullable();
            $table->string('document_proof_url')->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->index('student_id');
            $table->index('scholarship_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
