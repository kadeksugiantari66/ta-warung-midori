<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $tables   = Table::all();
        $products = Product::all();

        if ($tables->isEmpty() || $products->isEmpty()) return;

        // Generate order untuk 30 hari terakhir
        $queueCounter = 1;

        for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
            $date          = now()->subDays($daysAgo);
            $ordersPerDay  = rand(5, 15);

            for ($i = 0; $i < $ordersPerDay; $i++) {
                $table = $tables->random();
                $orderTime = $date->copy()->setTime(rand(10, 21), rand(0, 59));

                $order = Order::create([
                    'table_id'     => $table->id,
                    'queue_number' => $queueCounter++,
                    'status'       => 'completed',
                    'total_amount' => 0,
                    'created_at'   => $orderTime,
                    'updated_at'   => $orderTime,
                ]);

                // 1–4 item per pesanan
                $selectedProducts = $products->random(rand(1, min(4, $products->count())));
                $total = 0;

                foreach ($selectedProducts as $product) {
                    $qty      = rand(1, 3);
                    $subtotal = $product->price * $qty;
                    $total   += $subtotal;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'note'       => null,
                        'subtotal'   => $subtotal,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);
                }

                $order->update(['total_amount' => $total]);

                // Payment — 70% tunai, 30% digital
                Payment::create([
                    'order_id'   => $order->id,
                    'method'     => 'midtrans',
                    'amount'     => $total,
                    'status'     => 'paid',
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);
            }
        }
    }
}
