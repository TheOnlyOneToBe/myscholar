<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_actions_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->enum('status', ['approved', 'rejected']);
            $table->text('reason')->nullable();
            $table->json('response_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['notification_id', 'user_id']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_actions_log');
    }
};
