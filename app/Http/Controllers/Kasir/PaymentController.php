<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Menampilkan detail pesanan buat dicek sama kasir.
     */
    public function show(Order $order): View
    {
        $order->load(['table', 'orderItems.product', 'payment']);
        return view('kasir.payment.show', compact('order'));
    }

    /**
     * Memproses konfirmasi kalau pelanggan bayar pakai uang tunai langsung ke kasir.
     */
    public function confirmCash(Order $order): \Illuminate\Http\RedirectResponse
    {
        // Pastikan order masih pending/belum dibayar
        if ($order->payment?->status === 'paid') {
            return back()->with('error', 'Pesanan ini sudah dibayar.');
        }

        // Update record payment menjadi paid (dicatat sebagai tunai)
        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'tunai',
                'amount' => $order->total_amount,
                'status' => 'paid',
            ]
        );

        // Update status order (meja tetap occupied sampai pesanan completed oleh dapur)
        $order->update(['status' => 'confirmed']);

        return back()->with('success', 'Pembayaran tunai berhasil dikonfirmasi.');
    }
}
