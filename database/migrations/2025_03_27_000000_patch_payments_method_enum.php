<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function isMySql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize legacy value for all drivers.
        DB::statement("UPDATE payments SET method = 'tunai' WHERE method = 'cash'");

        // Enum alteration syntax below is MySQL-specific.
        if ($this->isMySql()) {
            DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans', 'tunai') DEFAULT 'tunai'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('tunai', 'midtrans') DEFAULT 'tunai'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE payments SET method = 'cash' WHERE method = 'tunai'");

        if ($this->isMySql()) {
            DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans', 'tunai') DEFAULT 'cash'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans') DEFAULT 'cash'");
        }
    }
};
