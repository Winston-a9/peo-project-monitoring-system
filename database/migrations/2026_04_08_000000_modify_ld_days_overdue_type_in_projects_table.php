<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear any legacy date values before changing type.
        DB::statement("UPDATE `projects` SET `ld_days_overdue` = NULL WHERE `ld_days_overdue` IS NOT NULL");
        DB::statement("ALTER TABLE `projects` MODIFY `ld_days_overdue` INT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `projects` MODIFY `ld_days_overdue` DATE NULL");
    }
};
