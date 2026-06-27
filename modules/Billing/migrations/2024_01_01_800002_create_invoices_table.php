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
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('amount_total', 12, 2);
            $table->decimal('discount_applied', 12, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->decimal('amount_after_discount', 12, 2);
            $table->enum('status', ['draft', 'issued', 'paid', 'partial', 'overdue'])->default('draft');
            $table->json('items')->nullable();
            $table->timestamp('sent_to_parent_at')->nullable();
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
