<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('parent_user_id');
            $table->string('relationship_type')->default('parent'); // parent, guardian, emergency_contact
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_access_records')->default(true);
            $table->boolean('can_receive_alerts')->default(true);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['student_id', 'parent_user_id']);
            $table->index('parent_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
