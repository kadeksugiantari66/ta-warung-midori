<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $tables = Table::all();
        $products = Product::all();

        if ($tables->isEmpty() || $products->isEmpty()) {
            return;
        }

        $queueCounter = 1;

        // ===== 30 hari terakhir: order historis (completed) =====
        for ($daysAgo = 29; $daysAgo >= 2; $daysAgo--) {
            $date = now()->subDays($daysAgo);
            $ordersPerDay = rand(5, 15);

            for ($i = 0; $i < $ordersPerDay; $i++) {
                $table = $tables->random();
                $orderTime = $date->copy()->setTime(rand(10, 21), rand(0, 59));

                $order = $this->createOrder($table->id_meja, $queueCounter++, 'completed', $orderTime);
                $total = $this->attachItems($order, $products, $orderTime);
                $order->update(['total_amount' => $total]);

                // Random customer email
                $email = rand(1, 3) === 1 ? 'pelanggan' . rand(1, 50) . '@email.com' : null;
                if ($email) $order->update(['customer_email' => $email]);

                // Mixed payment methods
                $method = rand(1, 10) <= 7 ? 'tunai' : 'midtrans';
                Payment::create([
                    'id_order' => $order->id_order,
                    'method' => $method,
                    'amount' => $total,
                    'status' => 'paid',
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);
            }
        }

        // ===== Kemarin & hari ini: campuran status biar dashboard hidup =====
        $activeStatuses = [
            'pending', 'pending',
            'confirmed', 'confirmed',
            'processing', 'processing',
            'ready', 'ready',
            'completed', 'completed',
            'cancelled',
        ];

        $activeTables = $tables->shuffle()->take(4);

        for ($daysAgo = 1; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo);
            $ordersPerDay = $daysAgo === 0 ? 3 : rand(6, 12);

            for ($i = 0; $i < $ordersPerDay; $i++) {
                $table = $activeTables->random();
                $status = $activeStatuses[array_rand($activeStatuses)];
                $orderTime = $date->copy()->setTime(rand(10, 21), rand(0, 59));

                $order = $this->createOrder($table->id_meja, $queueCounter++, $status, $orderTime);
                $total = $this->attachItems($order, $products, $orderTime);
                $order->update(['total_amount' => $total]);

                // Set table ke occupied untuk order yang masih aktif
                if (in_array($status, ['pending', 'confirmed', 'processing', 'ready'])) {
                    $table->update(['status' => 'occupied']);
                }

                // Isi customer_email untuk sebagian order
                if (rand(1, 3) === 1) {
                    $order->update(['customer_email' => 'pelanggan' . rand(1, 20) . '@email.com']);
                }

                // Payment sesuai status
                $paymentStatus = match ($status) {
                    'pending' => 'pending',
                    'cancelled' => (rand(0, 1) ? 'failed' : 'pending'),
                    default => 'paid',
                };

                $method = rand(1, 10) <= 6 ? 'tunai' : 'midtrans';
                Payment::create([
                    'id_order' => $order->id_order,
                    'method' => $method,
                    'amount' => $total,
                    'status' => $paymentStatus,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);
            }
        }

        // ===== Pastikan ada order TODAY dengan berbagai status =====
        $todayStatuses = ['pending', 'confirmed', 'processing', 'ready', 'completed'];

        // Reset all tables to available first, lalu set occupied sesuai
        Table::query()->update(['status' => 'available']);

        foreach ($todayStatuses as $idx => $status) {
            $table = $tables[$idx % $tables->count()];
            $orderTime = now()->setTime(rand(10, 20), rand(0, 59));

            $order = $this->createOrder($table->id_meja, $queueCounter++, $status, $orderTime);
            $total = $this->attachItems($order, $products, $orderTime);
            $email = ($idx % 2 === 0) ? 'pelanggan.test' . ($idx + 1) . '@email.com' : null;
            $order->update(['total_amount' => $total, 'customer_email' => $email]);

            if (in_array($status, ['pending', 'confirmed', 'processing', 'ready'])) {
                $table->update(['status' => 'occupied']);
            }

            $paymentStatus = ($status === 'pending') ? 'pending' : 'paid';
            Payment::create([
                'id_order' => $order->id_order,
                'method' => ($idx % 2 === 0) ? 'tunai' : 'midtrans',
                'amount' => $total,
                'status' => $paymentStatus,
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);
        }
    }

    private function createOrder(int $tableId, int $queueNumber, string $status, $time): Order
    {
        return Order::create([
            'id_meja' => $tableId,
            'queue_number' => $queueNumber,
            'status' => $status,
            'total_amount' => 0,
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }

    private function attachItems(Order $order, $products, $time): int
    {
        $selectedProducts = $products->random(rand(1, min(4, $products->count())));
        $total = 0;

        foreach ($selectedProducts as $product) {
            $qty = rand(1, 3);
            $subtotal = $product->price * $qty;
            $total += $subtotal;

            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $product->id_menu,
                'quantity' => $qty,
                'note' => rand(0, 2) === 0 ? $this->randomNote() : null,
                'subtotal' => $subtotal,
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }

        return $total;
    }

    private function randomNote(): string
    {
        $notes = [
            'Pedas ya',
            'Tidak pakai gula',
            'Extra nasi',
            'Kurang asin',
            'Panas-panas',
            'Jangan terlalu matang',
            'Bungkus',
        ];

        return $notes[array_rand($notes)];
    }
}
