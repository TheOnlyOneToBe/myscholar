<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('type', ['maladie', 'transport', 'medecin', 'conge', 'autre']);
            $table->string('document_url')->nullable();
            $table->unsignedBigInteger('submitted_by_parent_id')->nullable();
            $table->timestamp('submitted_at');
            $table->unsignedBigInteger('validated_by_user_id')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            $table->timestamps();
            $table->index('attendance_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justifications');
    }
};
