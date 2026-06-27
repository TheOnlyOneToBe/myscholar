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
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->integer('total_installments');
            $table->decimal('installment_amount', 10, 2);
            $table->enum('frequency', ['weekly', 'bi_weekly', 'monthly', 'quarterly'])->default('monthly');
            $table->date('start_date')->nullable();
            $table->enum('status', ['active', 'completed', 'failed'])->default('active');
            $table->timestamps();
            $table->index('invoice_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
