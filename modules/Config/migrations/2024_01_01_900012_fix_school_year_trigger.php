<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix the school year trigger to allow activation of locked years
     */
    public function up(): void
    {
        // Drop the overly restrictive trigger
        DB::statement("DROP TRIGGER IF EXISTS trg_prevent_locked_year_update");

        // Create a better trigger that prevents is_locked from being changed back to false
        // but allows is_active to be updated
        DB::statement("
            CREATE TRIGGER trg_prevent_unlock_school_year
            BEFORE UPDATE ON school_years
            FOR EACH ROW
            WHEN OLD.is_locked = true AND NEW.is_locked = false
            BEGIN
                SELECT RAISE(ABORT, 'Cannot unlock (unarchive) a locked school year');
            END;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS trg_prevent_unlock_school_year");

        // Recreate the original (overly restrictive) trigger
        DB::statement("
            CREATE TRIGGER trg_prevent_locked_year_update
            BEFORE UPDATE ON school_years
            FOR EACH ROW
            WHEN OLD.is_locked = true AND NEW.is_locked = true
            BEGIN
                SELECT RAISE(ABORT, 'Cannot modify a locked (archived) school year');
            END;
        ");
    }
};
