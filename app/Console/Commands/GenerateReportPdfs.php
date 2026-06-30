<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateReportPdfs extends Command
{
    protected $signature = 'reports:generate';

    protected $description = 'Generate mockup PDF laporan harian, bulanan, dan menu terlaris';

    public function handle(): int
    {
        $this->info('Generating laporan harian...');
        $this->generateDailyPdf();
        $this->info('✓ Laporan harian selesai.');

        $this->info('Generating laporan bulanan...');
        $this->generateMonthlyPdf();
        $this->info('✓ Laporan bulanan selesai.');

        $this->info('Generating laporan menu terlaris...');
        $this->generateTopProductsPdf();
        $this->info('✓ Laporan menu terlaris selesai.');

        $this->newLine();
        $this->info('Semua PDF tersimpan di storage/app/public/reports/');

        return self::SUCCESS;
    }

    private function generateDailyPdf(): void
    {
        $date = '2026-06-12';
        $summary = $this->dailySummary();
        $orders = $this->dailyOrders();

        $pdf = Pdf::loadView('reports.pdf-daily', compact('orders', 'summary', 'date'));
        $pdf->setPaper('A4', 'portrait');

        Storage::disk('public')->put('reports/Laporan-Harian-12-Juni-2026.pdf', $pdf->output());
    }

    private function generateMonthlyPdf(): void
    {
        $month = '2026-05';
        $summary = $this->monthlySummary();
        $orders = $this->monthlyOrders();
        $dailyTrend = $this->dailyTrendData();

        $pdf = Pdf::loadView('reports.pdf-monthly', compact('orders', 'summary', 'month', 'dailyTrend'));
        $pdf->setPaper('A4', 'portrait');

        Storage::disk('public')->put('reports/Laporan-Bulanan-Mei-2026.pdf', $pdf->output());
    }

    private function generateTopProductsPdf(): void
    {
        $month = '2026-05';
        $products = $this->topProductsData();
        $totalOrderedAll = collect($products)->sum('total_ordered');
        $totalRevenueAll = collect($products)->sum('total_revenue');

        $pdf = Pdf::loadView('reports.pdf-top-products', compact('products', 'month', 'totalOrderedAll', 'totalRevenueAll'));
        $pdf->setPaper('A4', 'portrait');

        Storage::disk('public')->put('reports/Laporan-Menu-Terlaris-Mei-2026.pdf', $pdf->output());
    }

    // ── DUMMY DATA ───────────────────────────────────────────────────────────

    private function dailySummary(): array
    {
        return [
            'total_orders' => 11,
            'completed' => 11,
            'revenue' => 1762000,
            'cash_revenue' => 1093000,
            'digital_revenue' => 669000,
        ];
    }

    private function dailyOrders(): array
    {
        return [
            ['queue' => '0156', 'table' => 'M01', 'items' => '2× Mujair Nyat-Nyat + Nasi, 1× Es Jeruk',                                  'total' => 64000,  'method' => 'Tunai',     'time' => '10:15'],
            ['queue' => '0157', 'table' => 'M03', 'items' => '1× Ayam Plecing + Nasi, 1× Es Teh / Teh Hangat, 1× Kentang Goreng',       'total' => 43000,  'method' => 'Digital',   'time' => '10:42'],
            ['queue' => '0158', 'table' => 'M07', 'items' => '1× Ayam Nyat-Nyat + Nasi, 2× Jus Alpukat',                                'total' => 47000,  'method' => 'Tunai',     'time' => '11:05'],
            ['queue' => '0159', 'table' => 'M02', 'items' => '1× Mujair Plecing + Nasi, 1× Sosis Goreng, 1× Kopi Bali',                 'total' => 42000,  'method' => 'Digital',   'time' => '11:38'],
            ['queue' => '0160', 'table' => 'M05', 'items' => '3× Mujair Sambal Matah + Nasi, 2× Air Mineral',                            'total' => 79000,  'method' => 'Tunai',     'time' => '12:20'],
            ['queue' => '0161', 'table' => 'M10', 'items' => '1× Mujair Nyat-Nyat + Nasi, 1× Jeruk Hangat, 1× Jus Sirsak',              'total' => 46000,  'method' => 'Tunai',     'time' => '12:55'],
            ['queue' => '0162', 'table' => 'M04', 'items' => '1× Ayam Nyat-Nyat + Nasi, 1× Es Teh / Teh Hangat',                        'total' => 33000,  'method' => 'Digital',   'time' => '13:18'],
            ['queue' => '0163', 'table' => 'M01', 'items' => '1× Mujair Nyat-Nyat + Nasi, 1× Kentang Goreng, 1× Jus Mangga',            'total' => 51000,  'method' => 'Tunai',     'time' => '13:45'],
            ['queue' => '0164', 'table' => 'M09', 'items' => '1× Ayam Plecing + Nasi, 1× Sosis Goreng, 1× Jus Semangka',                'total' => 46000,  'method' => 'Digital',   'time' => '14:10'],
            ['queue' => '0165', 'table' => 'M06', 'items' => '1× Mujair Plecing + Nasi, 1× Es Jeruk, 1× Jus Melon',                     'total' => 40000,  'method' => 'Tunai',     'time' => '14:35'],
            ['queue' => '0166', 'table' => 'M08', 'items' => '2× Ayam Nyat-Nyat + Nasi, 1× Jus Wortel, 1× Jus Tomat',                   'total' => 72000,  'method' => 'Tunai',     'time' => '15:00'],
        ];
    }

    private function monthlySummary(): array
    {
        return [
            'total_orders' => 312,
            'completed' => 305,
            'revenue' => 48350000,
            'cash_revenue' => 31825000,
            'digital_revenue' => 16525000,
        ];
    }

    private function monthlyOrders(): array
    {
        return [
            ['queue' => '0001', 'table' => 'M02', 'items' => 'Mujair Nyat-Nyat + Nasi, Es Teh',        'total' => 34000,  'method' => 'Tunai',   'time' => '01 Mei 10:15'],
            ['queue' => '0005', 'table' => 'M05', 'items' => 'Ayam Nyat-Nyat + Nasi, Jus Alpukat',      'total' => 37000,  'method' => 'Digital', 'time' => '01 Mei 12:30'],
            ['queue' => '0012', 'table' => 'M01', 'items' => 'Mujair Plecing + Nasi, Kentang Goreng',   'total' => 37000,  'method' => 'Tunai',   'time' => '02 Mei 11:00'],
            ['queue' => '0020', 'table' => 'M07', 'items' => 'Mujair Sambal Matah + Nasi',              'total' => 23000,  'method' => 'Digital', 'time' => '03 Mei 13:20'],
            ['queue' => '0045', 'table' => 'M03', 'items' => 'Ayam Plecing + Nasi, Es Jeruk',           'total' => 31000,  'method' => 'Tunai',   'time' => '07 Mei 10:40'],
            ['queue' => '0080', 'table' => 'M10', 'items' => 'Mujair Nyat-Nyat + Nasi, Jus Sirsak',     'total' => 38000,  'method' => 'Digital', 'time' => '10 Mei 14:15'],
            ['queue' => '0120', 'table' => 'M04', 'items' => 'Ayam Nyat-Nyat + Nasi, Kopi Bali',        'total' => 32000,  'method' => 'Tunai',   'time' => '15 Mei 11:45'],
            ['queue' => '0150', 'table' => 'M09', 'items' => 'Mujair Plecing + Nasi, Sosis Goreng',     'total' => 37000,  'method' => 'Digital', 'time' => '18 Mei 13:10'],
            ['queue' => '0185', 'table' => 'M06', 'items' => 'Mujair Nyat-Nyat + Nasi, Jus Mangga',     'total' => 37000,  'method' => 'Tunai',   'time' => '22 Mei 10:30'],
            ['queue' => '0220', 'table' => 'M08', 'items' => 'Ayam Nyat-Nyat + Nasi, Jus Semangka',     'total' => 36000,  'method' => 'Tunai',   'time' => '25 Mei 12:55'],
            ['queue' => '0260', 'table' => 'M02', 'items' => 'Mujair Plecing + Nasi, Jeruk Hangat',     'total' => 31000,  'method' => 'Digital', 'time' => '28 Mei 11:20'],
            ['queue' => '0295', 'table' => 'M05', 'items' => 'Ayam Plecing + Nasi, Kentang Goreng',     'total' => 37000,  'method' => 'Tunai',   'time' => '30 Mei 14:00'],
        ];
    }

    private function dailyTrendData(): array
    {
        $days = [];
        $revenues = [1450000, 1320000, 1580000, 1200000, 1750000, 1890000, 1420000, 1650000, 1500000, 1380000,
            1720000, 1840000, 1480000, 1600000, 1550000, 1400000, 1770000, 1920000, 1680000, 1450000,
            1510000, 1630000, 1390000, 1800000, 1710000, 1580000, 1470000, 1760000, 1830000, 1550000];
        $orderCounts = [9, 8, 10, 7, 11, 12, 9, 10, 9, 8, 11, 12, 9, 10, 10, 8, 11, 12, 10, 9, 10, 10, 8, 11, 11, 10, 9, 11, 12, 10];

        for ($d = 1; $d <= 30; $d++) {
            $days[] = [
                'date' => '2026-05-'.str_pad($d, 2, '0', STR_PAD_LEFT),
                'total_orders' => $orderCounts[$d - 1],
                'revenue' => $revenues[$d - 1],
            ];
        }

        // Hanya return 31 Mei
        for ($d = 1; $d <= 31; $d++) {
            if ($d > 30) {
                $days[] = [
                    'date' => '2026-05-31',
                    'total_orders' => 8,
                    'revenue' => 1200000,
                ];
            }
        }

        return $days;
    }

    private function topProductsData(): array
    {
        return [
            ['rank' => 1,  'name' => 'Mujair Nyat-Nyat + Nasi',    'category' => 'Makanan',  'price' => 28000, 'total_ordered' => 78,  'total_revenue' => 2184000],
            ['rank' => 2,  'name' => 'Ayam Nyat-Nyat + Nasi',       'category' => 'Makanan',  'price' => 27000, 'total_ordered' => 65,  'total_revenue' => 1755000],
            ['rank' => 3,  'name' => 'Es Teh / Teh Hangat',         'category' => 'Minuman',  'price' => 6000,  'total_ordered' => 124, 'total_revenue' => 744000],
            ['rank' => 4,  'name' => 'Mujair Plecing + Nasi',       'category' => 'Makanan',  'price' => 23000, 'total_ordered' => 52,  'total_revenue' => 1196000],
            ['rank' => 5,  'name' => 'Ayam Plecing + Nasi',         'category' => 'Makanan',  'price' => 23000, 'total_ordered' => 48,  'total_revenue' => 1104000],
            ['rank' => 6,  'name' => 'Es Jeruk',                    'category' => 'Minuman',  'price' => 8000,  'total_ordered' => 98,  'total_revenue' => 784000],
            ['rank' => 7,  'name' => 'Mujair Sambal Matah + Nasi',  'category' => 'Makanan',  'price' => 23000, 'total_ordered' => 41,  'total_revenue' => 943000],
            ['rank' => 8,  'name' => 'Jus Alpukat',                 'category' => 'Jus',      'price' => 10000, 'total_ordered' => 73,  'total_revenue' => 730000],
            ['rank' => 9,  'name' => 'Air Mineral',                 'category' => 'Minuman',  'price' => 5000,  'total_ordered' => 156, 'total_revenue' => 780000],
            ['rank' => 10, 'name' => 'Kentang Goreng',              'category' => 'Makanan',  'price' => 14000, 'total_ordered' => 55,  'total_revenue' => 770000],
            ['rank' => 11, 'name' => 'Kopi Bali',                   'category' => 'Minuman',  'price' => 5000,  'total_ordered' => 87,  'total_revenue' => 435000],
            ['rank' => 12, 'name' => 'Sosis Goreng',                'category' => 'Makanan',  'price' => 14000, 'total_ordered' => 42,  'total_revenue' => 588000],
            ['rank' => 13, 'name' => 'Jus Sirsak',                  'category' => 'Jus',      'price' => 10000, 'total_ordered' => 38,  'total_revenue' => 380000],
            ['rank' => 14, 'name' => 'Jus Mangga',                  'category' => 'Jus',      'price' => 9000,  'total_ordered' => 45,  'total_revenue' => 405000],
            ['rank' => 15, 'name' => 'Jeruk Hangat',                'category' => 'Minuman',  'price' => 8000,  'total_ordered' => 34,  'total_revenue' => 272000],
            ['rank' => 16, 'name' => 'Jus Semangka',                'category' => 'Jus',      'price' => 9000,  'total_ordered' => 31,  'total_revenue' => 279000],
            ['rank' => 17, 'name' => 'Jus Melon',                   'category' => 'Jus',      'price' => 9000,  'total_ordered' => 28,  'total_revenue' => 252000],
            ['rank' => 18, 'name' => 'Jus Wortel',                  'category' => 'Jus',      'price' => 9000,  'total_ordered' => 22,  'total_revenue' => 198000],
            ['rank' => 19, 'name' => 'Jus Tomat',                   'category' => 'Jus',      'price' => 9000,  'total_ordered' => 19,  'total_revenue' => 171000],
        ];
    }
}
