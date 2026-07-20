<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_ready_order_shows_in_siap_diantar_section_with_complete_button(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $category = Category::create(['name' => 'Makanan']);
        $product = Product::create([
            'id_category' => $category->id_category, 'name' => 'Nasi Goreng', 'price' => 20000, 'is_available' => true,
        ]);
        $table = Table::create(['table_number' => 'A1', 'status' => 'occupied']);
        $order = Order::create([
            'id_meja' => $table->id_meja, 'queue_number' => 7, 'status' => 'ready', 'total_amount' => 20000,
        ]);
        OrderItem::create(['id_order' => $order->id_order, 'id_menu' => $product->id_menu, 'quantity' => 1, 'subtotal' => 20000]);
        Payment::create(['id_order' => $order->id_order, 'method' => 'tunai', 'amount' => 20000, 'status' => 'paid']);

        $this->actingAs($kasir)->get(route('kasir.dashboard'))
            ->assertOk()
            ->assertSee('Siap Diantar')
            ->assertSee('Pesanan Selesai')
            ->assertSee(route('kasir.orders.complete', $order));
    }
}
