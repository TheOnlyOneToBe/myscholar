<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('relationship'); // parent, guardian, emergency_contact, etc.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('occupation')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('is_emergency_contact')->default(false);
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->index(['student_id', 'relationship']);
            $table->index('is_primary_contact');
            $table->index('is_emergency_contact');
            $table->unique(['student_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_contacts');
    }
};
