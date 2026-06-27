<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_averages') && Schema::hasTable('classes')) {
            Schema::table('class_averages', function (Blueprint $table) {
                if (!Schema::hasColumn('class_averages', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable();
                }
                try {
                    $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('class_averages')) {
            Schema::table('class_averages', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['class_id']);
            });
        }
    }
};
