<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Tombol aksi khusus staf dapur buat ubah status masakan (contoh: lagi dimasak atau udah siap dianter).
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:processing,ready,completed'],
        ]);

        // Saat pesanan selesai: bebaskan meja + rotasi QR (lewat service bersama)
        if ($request->status === 'completed') {
            $order->complete();
        } else {
            $order->update(['status' => $request->status]);
        }

        return back()->with('success', "Status pesanan #{$order->queue_number} diperbarui.");
    }
}
