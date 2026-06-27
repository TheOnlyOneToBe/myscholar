<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->nullable();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('fee_structure_id')->nullable();
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('currency')->default('FCFA');
            $table->date('issue_date');
            $table->date('due_date');
            $table->enum('status', ['pending', 'issued', 'paid', 'partial', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('student_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
