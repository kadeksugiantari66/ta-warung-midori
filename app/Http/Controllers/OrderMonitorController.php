<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman "Lihat Pesanan" untuk Admin & Kasir.
 * Menampilkan daftar pesanan (nomor meja, item, status, metode bayar, total)
 * dengan filter berdasarkan status dan rentang tanggal, serta detail pesanan.
 */
class OrderMonitorController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'processing', 'ready', 'completed', 'cancelled'];

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $from = $request->input('from');
        $to = $request->input('to');
        $search = $request->input('search');

        $orders = Order::with(['table', 'orderItems.product', 'payment'])
            ->when(in_array($status, self::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->when($search, fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('queue_number', $search)
                    ->orWhereHas('table', fn ($t) => $t->where('table_number', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view($this->prefix().'.orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'search' => $search,
            'prefix' => $this->prefix(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['table', 'orderItems.product', 'payment']);

        return view($this->prefix().'.orders.show', [
            'order' => $order,
            'prefix' => $this->prefix(),
        ]);
    }

    /**
     * Prefix route/view sesuai peran pengguna (admin atau kasir).
     */
    private function prefix(): string
    {
        return auth()->user()->isAdmin() ? 'admin' : 'kasir';
    }
}
