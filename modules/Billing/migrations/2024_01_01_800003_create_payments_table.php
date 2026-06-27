<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_method', ['cash', 'check', 'virement', 'mobile_money', 'card']);
            $table->string('reference_number')->nullable();
            $table->unsignedBigInteger('validated_by_user_id')->nullable();
            $table->string('receipt_url')->nullable();
            $table->timestamps();
            $table->index('invoice_id');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
