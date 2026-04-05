<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman nota pesanan (invoice) buat keperluan cetak struk.
     */
    public function show(Order $order): View
    {
        abort_if($order->payment?->status !== 'paid', 403, 'Pesanan belum dibayar.');
        $order->load(['table', 'orderItems.product', 'payment']);
        return view('kasir.invoice', compact('order'));
    }
}
