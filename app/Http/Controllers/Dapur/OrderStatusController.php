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

        $order->update(['status' => $request->status]);

        // Bebaskan meja saat pesanan selesai diantar
        if ($request->status === 'completed') {
            $order->table->update(['status' => 'available']);
        }

        return back()->with('success', "Status pesanan #{$order->queue_number} diperbarui.");
    }
}
