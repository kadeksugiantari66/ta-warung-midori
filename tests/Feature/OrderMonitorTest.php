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

class OrderMonitorTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(string $status = 'completed'): Order
    {
        $category = Category::create(['name' => 'Makanan']);
        $product = Product::create([
            'id_category' => $category->id_category,
            'name' => 'Nasi Goreng',
            'price' => 20000,
            'is_available' => true,
        ]);
        $table = Table::create(['table_number' => 'A1', 'status' => 'occupied']);
        $order = Order::create([
            'id_meja' => $table->id_meja,
            'queue_number' => 1,
            'status' => $status,
            'total_amount' => 20000,
        ]);
        OrderItem::create([
            'id_order' => $order->id_order,
            'id_menu' => $product->id_menu,
            'quantity' => 1,
            'subtotal' => 20000,
        ]);
        Payment::create([
            'id_order' => $order->id_order,
            'method' => 'tunai',
            'amount' => 20000,
            'status' => 'paid',
        ]);

        return $order;
    }

    public function test_admin_can_view_orders_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeOrder();

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Nasi Goreng')
            ->assertSee('Pesanan');
    }

    public function test_orders_list_filters_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeOrder('completed');

        $this->actingAs($admin)->get(route('admin.orders.index', ['status' => 'completed']))
            ->assertOk()->assertSee('Nasi Goreng');

        $this->actingAs($admin)->get(route('admin.orders.index', ['status' => 'pending']))
            ->assertOk()->assertDontSee('Nasi Goreng');
    }

    public function test_orders_list_filters_by_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeOrder();

        // Rentang tanggal di masa depan -> tidak ada hasil
        $this->actingAs($admin)->get(route('admin.orders.index', ['from' => '2999-01-01', 'to' => '2999-12-31']))
            ->assertOk()->assertDontSee('Nasi Goreng');
    }

    public function test_admin_can_view_order_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->makeOrder();

        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Nomor Antrean')
            ->assertSee('Nasi Goreng');
    }

    public function test_kasir_can_view_orders_list(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);
        $this->makeOrder();

        $this->actingAs($kasir)->get(route('kasir.orders.index'))
            ->assertOk()->assertSee('Nasi Goreng');
    }

    public function test_dapur_cannot_access_orders_page(): void
    {
        $dapur = User::factory()->create(['role' => 'dapur']);

        $this->actingAs($dapur)->get(route('admin.orders.index'))->assertForbidden();
        $this->actingAs($dapur)->get(route('kasir.orders.index'))->assertForbidden();
    }

    public function test_kasir_can_complete_ready_order_and_free_table(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);
        $order = $this->makeOrder('ready');
        $order->table->update(['status' => 'occupied']);

        $this->actingAs($kasir)
            ->post(route('kasir.orders.complete', $order))
            ->assertRedirect();

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('available', $order->table->fresh()->status);
    }

    public function test_complete_ignored_when_order_not_ready(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);
        $order = $this->makeOrder('processing');

        $this->actingAs($kasir)->post(route('kasir.orders.complete', $order))->assertRedirect();

        $this->assertSame('processing', $order->fresh()->status);
    }
}
