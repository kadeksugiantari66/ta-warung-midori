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
        // Add 'tunai' to enum list temporarily to avoid truncation error during update
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans', 'tunai') DEFAULT 'tunai'");
        
        // Update old 'cash' values to 'tunai' safely
        DB::statement("UPDATE payments SET method = 'tunai' WHERE method = 'cash'");
        
        // Finalize enum to only 'tunai' and 'midtrans'
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('tunai', 'midtrans') DEFAULT 'tunai'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans', 'tunai') DEFAULT 'cash'");
        DB::statement("UPDATE payments SET method = 'cash' WHERE method = 'tunai'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'midtrans') DEFAULT 'cash'");
    }
};
