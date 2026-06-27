<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('student_id_number');
            $table->string('phone_number')->nullable()->after('email');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropUnique(['email']);
            $table->dropColumn(['email', 'phone_number']);
        });
    }
};
