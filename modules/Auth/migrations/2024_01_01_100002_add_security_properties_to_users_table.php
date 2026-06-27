<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('last_password_change')->nullable()->after('password');
            $table->integer('failed_login_attempts')->default(0)->after('last_password_change');
            $table->dateTime('account_locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('two_factor_enabled')->default(false)->after('account_locked_until');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('ip_whitelist')->nullable()->after('two_factor_secret');
            $table->json('password_history')->nullable()->after('ip_whitelist');
            $table->dateTime('email_verified_at')->nullable()->after('password_history');
            $table->dateTime('phone_verified_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_password_change',
                'failed_login_attempts',
                'account_locked_until',
                'two_factor_enabled',
                'two_factor_secret',
                'ip_whitelist',
                'password_history',
                'email_verified_at',
                'phone_verified_at',
            ]);
        });
    }
};
