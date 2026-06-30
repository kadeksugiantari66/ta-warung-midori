<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tableNumbers = ['A1', 'A2', 'A3', 'B1', 'B2', 'B3'];

        foreach ($tableNumbers as $number) {
            $table = Table::firstOrCreate(
                ['table_number' => $number],
                ['status' => 'available']
            );

            // Generate QR dinamis (token) jika belum ada
            if (! $table->qr_code_path) {
                $table->generateQr();
            }
        }
    }
}
