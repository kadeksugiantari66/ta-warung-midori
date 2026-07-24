<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Transaksi hari ini yang sudah dibayar via Midtrans
        $todayOrders = Order::with(['table', 'orderItems.product', 'payment'])
            ->whereDate('created_at', today())
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->latest()
            ->get();

        $todayRevenue = $todayOrders->sum('total_amount');

        // Pesanan aktif (sudah bayar, sedang diproses dapur)
        $activeOrders = Order::with(['table'])
            ->whereIn('status', ['confirmed', 'processing', 'ready'])
            ->latest()
            ->get();

        // Pesanan selesai tapi meja belum dikosongkan (pelanggan belum/telah pergi)
        $occupiedOrders = Order::with(['table'])
            ->where('status', 'completed')
            ->whereHas('table', fn($q) => $q->where('status', 'occupied'))
            ->latest()
            ->get();

        // Pesanan menunggu pembayaran (tunai atau midtrans pending)
        $pendingOrders = Order::with(['table', 'payment'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Hash sinkronisasi realtime berdasarkan aktivitas pesanan dan pembayaran (termasuk yang masuk dapur dan payment status)
        $pollHash = Order::max('updated_at');

        return view('kasir.dashboard', compact('todayOrders', 'todayRevenue', 'activeOrders', 'pendingOrders', 'occupiedOrders', 'pollHash'));
    }

    public function poll(): \Illuminate\Http\JsonResponse
    {
        $hash = Order::max('updated_at');
        return response()->json(['hash' => $hash]);
    }
}
