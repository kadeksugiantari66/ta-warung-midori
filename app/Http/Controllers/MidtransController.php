<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$clientKey    = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = config('services.midtrans.is_sanitized');
        Config::$is3ds        = config('services.midtrans.is_3ds');
    }

    /**
     * Membuat token Snap Midtrans untuk fungsi pembayaran digital.
     */
    public function snapToken(Order $order): JsonResponse
    {
        abort_if($order->payment?->status === 'paid', 422, 'Pesanan sudah dibayar.');

        $params = [
            'transaction_details' => [
                'order_id'     => 'MIDORI-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total_amount,
            ],
            'item_details' => $order->orderItems->map(fn($item) => [
                'id'       => $item->product_id,
                'price'    => (int) $item->product->price,
                'quantity' => $item->quantity,
                'name'     => $item->product->name,
            ])->toArray(),
            'customer_details' => [
                'first_name' => 'Pelanggan',
                'last_name'  => 'Meja ' . $order->table->table_number,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan payment record dengan status pending
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'midtrans',
                'amount' => $order->total_amount,
                'status' => 'pending',
            ]
        );

        return response()->json([
            'snap_token' => $snapToken,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Endpoint buat nerima callback notifikasi otomatis dari server Midtrans.
     */
    public function webhook(Request $request): JsonResponse
    {
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId           = $notification->order_id; // format: MIDORI-{id}-{timestamp}
        $fraudStatus       = $notification->fraud_status;

        // Ekstrak order ID asli
        $parts   = explode('-', $orderId);
        $localId = $parts[1] ?? null;

        $order = Order::find($localId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $paymentStatus = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement'                           => 'paid',
            in_array($transactionStatus, ['cancel', 'deny', 'expire'])   => 'failed',
            $transactionStatus === 'pending'                              => 'pending',
            default                                                       => 'pending',
        };

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method'         => 'midtrans',
                'amount'         => $order->total_amount,
                'status'         => $paymentStatus,
                'transaction_id' => $notification->transaction_id,
            ]
        );

        if ($paymentStatus === 'paid') {
            $order->update(['status' => 'confirmed']);
            // Meja TIDAK di-set available di sini, menunggu Dapur set 'completed'
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Fallback verifikasi dari frontend (khususnya untuk localhost jika webhook gagal masuk)
     */
    public function verify(Request $request, Order $order): JsonResponse
    {
        $transactionId = $request->input('transaction_id');
        $orderId       = $request->input('order_id'); // e.g., MIDORI-15-...

        if (!$transactionId || !$orderId) {
            return response()->json(['success' => false, 'message' => 'Missing param']);
        }

        try {
            $status = \Midtrans\Transaction::status($orderId);
            $transactionStatus = $status->transaction_status;
            $fraudStatus       = $status->fraud_status ?? null;

            $paymentStatus = match (true) {
                $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
                $transactionStatus === 'settlement'                           => 'paid',
                in_array($transactionStatus, ['cancel', 'deny', 'expire'])   => 'failed',
                $transactionStatus === 'pending'                              => 'pending',
                default                                                       => 'pending',
            };

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'method'         => 'midtrans',
                    'amount'         => $order->total_amount,
                    'status'         => $paymentStatus,
                    'transaction_id' => $status->transaction_id,
                ]
            );

            // Jika sebelumnya masih pending namun sekarang paid
            if ($paymentStatus === 'paid' && $order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
                // Meja TIDAK di-set available di sini, menunggu Dapur set 'completed'
            }

            return response()->json(['success' => true, 'status' => $paymentStatus]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
