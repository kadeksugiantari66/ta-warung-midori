<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

            // Generate QR jika belum ada
            if (!$table->qr_code_path) {
                $url  = url("/order/menu/{$table->id}");
                $path = "qrcodes/table_{$table->id}.svg";
                $svg  = QrCode::format('svg')->size(300)->generate($url);
                Storage::disk('public')->put($path, $svg);
                $table->update(['qr_code_path' => $path]);
            }
        }
    }
}
