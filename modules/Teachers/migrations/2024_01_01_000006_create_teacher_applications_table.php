<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('teacher_code')->nullable();
            $table->string('specialization')->nullable();
            $table->string('qualification_level')->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('filiere', ['generale', 'technique'])->nullable();
            $table->string('office_location')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('phone_office')->nullable();
            $table->string('email_office')->nullable();
            $table->json('subjects_data')->nullable(); // Format: [{subject_id, proficiency_level, since_year}]
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_applications');
    }
};
