<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayOrders   = Order::whereDate('created_at', today())->count();
        $todayRevenue  = Order::whereDate('created_at', today())
            ->where('status', 'completed')->sum('total_amount');
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count();
        $totalProducts = Product::count();
        $occupiedTables = Table::where('status', 'occupied')->count();
        $totalTables    = Table::count();

        // Tren 7 hari terakhir untuk mini chart
        $weekTrend = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        // Isi hari yang kosong dengan 0
        $trendLabels = collect();
        $trendData   = collect();
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $trendLabels->push(now()->subDays($i)->format('d/m'));
            $trendData->push($weekTrend->get($d, 0));
        }

        // Top 5 menu hari ini
        $topToday = Product::withCount(['orderItems as today_count' => function ($q) {
            $q->whereHas('order', fn($o) => $o->whereDate('created_at', today()));
        }])->orderByDesc('today_count')->take(5)->get();

        return view('admin.dashboard', compact(
            'todayOrders', 'todayRevenue', 'pendingOrders',
            'totalProducts', 'occupiedTables', 'totalTables',
            'trendLabels', 'trendData', 'topToday'
        ));
    }
}
