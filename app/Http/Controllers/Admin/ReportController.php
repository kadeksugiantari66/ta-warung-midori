<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Mengambil data pesanan buat laporan harian kasir/admin berdasar tanggal tertentu.
     */
    public function daily(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();
        $orders = $this->getOrders($date, $date);
        $summary = $this->getSummary($orders);

        return view('admin.reports.daily', compact('orders', 'summary', 'date'));
    }

    /**
     * Menampilkan laporan bulanan sekaligus bikin perhitungan untuk data grafik tren harian.
     */
    public function monthly(Request $request): View
    {
        $month = $request->month ?? today()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $orders = $this->getOrders(
            "{$year}-{$mon}-01",
            date('Y-m-t', mktime(0, 0, 0, $mon, 1, $year))
        );

        $summary = $this->getSummary($orders);

        // Data tren harian dalam bulan ini untuk grafik
        $dailyTrend = Order::selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_amount) as revenue')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.monthly', compact('orders', 'summary', 'month', 'dailyTrend'));
    }

    /**
     * Menampilkan daftar dan statistik menu mana saja yang paling laku dipesan.
     */
    public function topProducts(Request $request): View
    {
        $month = $request->month ?? today()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $products = Product::with('category')
            ->withCount(['orderItems as total_ordered' => function ($q) use ($year, $mon) {
                $q->whereHas('order', fn ($o) => $o->whereYear('created_at', $year)->whereMonth('created_at', $mon));
            }])
            ->orderByDesc('total_ordered')
            ->take(10)
            ->get();

        return view('admin.reports.top-products', compact('products', 'month'));
    }

    /**
     * Export PDF laporan harian — profesional & siap cetak.
     */
    public function exportDailyPdf(Request $request)
    {
        $date = $request->date ?? today()->toDateString();

        $orders = $this->getOrders($date, $date)
            ->where('status', 'completed')
            ->sortBy('created_at')
            ->map(fn ($o) => [
                'queue' => str_pad((string) $o->queue_number, 4, '0', STR_PAD_LEFT),
                'table' => $o->table?->table_number ?? '-',
                'items' => $o->orderItems->map(fn ($it) => $it->quantity.'x '.($it->product?->name ?? 'Menu'))->implode(', '),
                'method' => $o->payment?->method === 'tunai' ? 'Tunai' : 'Digital',
                'time' => $o->created_at->format('H:i'),
                'total' => (int) $o->total_amount,
            ])->values()->all();

        $summary = [
            'total_orders' => count($orders),
            'revenue' => array_sum(array_column($orders, 'total')),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf-daily', compact('orders', 'summary', 'date'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Laporan-Harian-{$date}.pdf");
    }

    /**
     * Export PDF laporan bulanan — dengan ringkasan & tren harian.
     */
    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->month ?? today()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $orders = $this->getOrders(
            "{$year}-{$mon}-01",
            date('Y-m-t', mktime(0, 0, 0, $mon, 1, $year))
        );

        $summary = $this->getSummary($orders);

        $dailyTrend = Order::selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_amount) as revenue')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf-monthly', compact('orders', 'summary', 'month', 'dailyTrend'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Laporan-Bulanan-{$month}.pdf");
    }

    /**
     * Export PDF menu terlaris — ranking & kontribusi pendapatan.
     */
    public function exportTopProductsPdf(Request $request)
    {
        $month = $request->month ?? today()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $products = Product::with('category')
            ->withCount(['orderItems as total_ordered' => function ($q) use ($year, $mon) {
                $q->whereHas('order', fn ($o) => $o->whereYear('created_at', $year)->whereMonth('created_at', $mon));
            }])
            ->withSum(['orderItems as total_revenue' => function ($q) use ($year, $mon) {
                $q->whereHas('order', fn ($o) => $o->whereYear('created_at', $year)->whereMonth('created_at', $mon));
            }], 'subtotal')
            ->orderByDesc('total_ordered')
            ->get()
            ->filter(fn ($p) => $p->total_ordered > 0)  // hanya menu yang benar-benar terjual
            ->values();

        $totalOrderedAll = $products->sum('total_ordered');
        $totalRevenueAll = $products->sum('total_revenue');

        $pdf = Pdf::loadView('admin.reports.pdf-top-products', compact('products', 'month', 'totalOrderedAll', 'totalRevenueAll'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Laporan-Menu-Terlaris-{$month}.pdf");
    }

    /**
     * Fungsi buat otomatis mencetak laporan jadi file PDF (kompatibilitas lama).
     */
    public function exportPdf(Request $request)
    {
        return $this->exportDailyPdf($request);
    }

    /**
     * Fungsi untuk export atau download rekap laporan dalam format Excel.
     */
    public function exportExcel(Request $request)
    {
        $date = $request->date ?? today()->toDateString();

        return Excel::download(new ReportExport($date), "laporan-{$date}.xlsx");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getOrders(string $from, string $to)
    {
        return Order::with(['table', 'orderItems.product', 'payment'])
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->latest()
            ->get();
    }

    private function getSummary($orders): array
    {
        // Semua angka pendapatan berbasis pesanan SELESAI (completed) agar konsisten:
        // Tunai + Digital = Total Pendapatan, dan cocok dengan tabel tren harian.
        $completed = $orders->where('status', 'completed');

        return [
            'total_orders' => $orders->count(),
            'completed' => $completed->count(),
            'revenue' => $completed->sum('total_amount'),
            'cash_revenue' => $completed->filter(fn ($o) => $o->payment?->method === 'tunai')->sum('total_amount'),
            'digital_revenue' => $completed->filter(fn ($o) => $o->payment?->method !== 'tunai')->sum('total_amount'),
        ];
    }
}
