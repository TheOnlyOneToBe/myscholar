<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_ip_blocklist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->string('reason'); // rate_limit, suspicious_activity, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('unblock_at')->nullable();
            $table->unsignedBigInteger('blocked_by_user_id')->nullable();
            $table->foreign('blocked_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('ip_address');
            $table->index('is_active');
            $table->index('blocked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_ip_blocklist');
    }
};
