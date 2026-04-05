<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    /**
     * Mengambil data pesanan buat laporan harian kasir/admin berdasar tanggal tertentu.
     */
    public function daily(Request $request): View
    {
        $date   = $request->date ?? today()->toDateString();
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
                $q->whereHas('order', fn($o) => $o->whereYear('created_at', $year)->whereMonth('created_at', $mon));
            }])
            ->orderByDesc('total_ordered')
            ->take(10)
            ->get();

        return view('admin.reports.top-products', compact('products', 'month'));
    }

    /**
     * Fungsi buat otomatis mencetak laporan jadi file PDF.
     */
    public function exportPdf(Request $request)
    {
        $date   = $request->date ?? today()->toDateString();
        $orders = $this->getOrders($date, $date);
        $summary = $this->getSummary($orders);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'summary', 'date'));
        return $pdf->download("laporan-{$date}.pdf");
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
        return [
            'total_orders'   => $orders->count(),
            'completed'      => $orders->where('status', 'completed')->count(),
            'revenue'        => $orders->where('status', 'completed')->sum('total_amount'),
            'cash_revenue'   => $orders->filter(fn($o) => $o->payment?->method === 'tunai' && $o->payment?->status === 'paid')->sum('total_amount'),
            'digital_revenue'=> $orders->filter(fn($o) => $o->payment?->method === 'midtrans' && $o->payment?->status === 'paid')->sum('total_amount'),
        ];
    }
}
