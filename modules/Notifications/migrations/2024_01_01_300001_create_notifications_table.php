<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['academic', 'financial', 'attendance', 'system', 'security', 'approval']);
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal');
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('actions')->nullable();
            $table->string('action_target_route')->nullable();
            $table->json('action_parameters')->nullable();
            $table->enum('action_status', ['pending', 'approved', 'rejected', 'executed'])->default('pending')->nullable();
            $table->foreignId('actioned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('is_read');
            $table->index('priority');
            $table->index('type');
            $table->index('action_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
