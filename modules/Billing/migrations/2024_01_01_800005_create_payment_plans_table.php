<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->integer('installment_count');
            $table->date('start_date');
            $table->enum('status', ['active', 'completed', 'failed'])->default('active');
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->timestamps();
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
