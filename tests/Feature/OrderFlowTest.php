<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: Table, 1: Product, 2: Product} */
    private function seedMenu(): array
    {
        $cat = Category::create(['name' => 'Makanan']);
        $available = Product::create([
            'id_category' => $cat->id_category, 'name' => 'Nasi Goreng', 'price' => 20000, 'is_available' => true,
        ]);
        $habis = Product::create([
            'id_category' => $cat->id_category, 'name' => 'Mie Goreng', 'price' => 18000, 'is_available' => false,
        ]);
        $table = Table::create(['table_number' => 'A1', 'status' => 'available', 'qr_token' => 'secrettoken123']);

        return [$table, $available, $habis];
    }

    public function test_menu_rejects_invalid_qr_token(): void
    {
        [$table] = $this->seedMenu();

        // Tanpa token (atau token salah) -> halaman QR tidak valid
        $this->get(route('order.menu', $table))
            ->assertOk()
            ->assertSee('QR Code Tidak Valid');
    }

    public function test_menu_accepts_valid_token_and_shows_sold_out_items(): void
    {
        [$table] = $this->seedMenu();

        $this->get(route('order.menu', ['table' => $table->id_meja, 'token' => 'secrettoken123']))
            ->assertOk()
            ->assertSee('Nasi Goreng')
            ->assertSee('Mie Goreng') // menu habis tetap ditampilkan
            ->assertSee('Habis');
    }

    public function test_order_saves_customer_email_and_emails_invoice_on_cash_payment(): void
    {
        Mail::fake();
        [$table, $available] = $this->seedMenu();

        $this->post(route('order.store', $table), [
            'items' => [['id_menu' => $available->id_menu, 'quantity' => 2]],
            'payment_method' => 'tunai',
            'customer_email' => 'pelanggan@contoh.com',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('pelanggan@contoh.com', $order->customer_email);

        // Kasir konfirmasi pembayaran tunai -> nota dikirim ke email pelanggan
        $kasir = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($kasir)->post(route('kasir.payment.cash', $order))->assertRedirect();

        Mail::assertSent(InvoiceMail::class, fn ($mail) => $mail->hasTo('pelanggan@contoh.com'));
    }

    public function test_order_without_email_does_not_send_invoice(): void
    {
        Mail::fake();
        [$table, $available] = $this->seedMenu();

        $this->post(route('order.store', $table), [
            'items' => [['id_menu' => $available->id_menu, 'quantity' => 1]],
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $kasir = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($kasir)->post(route('kasir.payment.cash', Order::firstOrFail()))->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_customer_can_complete_a_paid_and_ready_order(): void
    {
        [$table] = $this->seedMenu();
        $order = Order::create(['id_meja' => $table->id_meja, 'queue_number' => 1, 'status' => 'ready', 'total_amount' => 20000]);
        Payment::create(['id_order' => $order->id_order, 'method' => 'tunai', 'amount' => 20000, 'status' => 'paid']);

        $this->postJson(route('order.complete', $order))->assertOk()->assertJson(['success' => true]);

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('available', $table->fresh()->status); // meja dibebaskan
    }

    public function test_customer_cannot_complete_unpaid_order(): void
    {
        [$table] = $this->seedMenu();
        $order = Order::create(['id_meja' => $table->id_meja, 'queue_number' => 1, 'status' => 'ready', 'total_amount' => 20000]);
        Payment::create(['id_order' => $order->id_order, 'method' => 'tunai', 'amount' => 20000, 'status' => 'pending']);

        $this->postJson(route('order.complete', $order))->assertStatus(422);
        $this->assertSame('ready', $order->fresh()->status);
    }

    public function test_completed_order_confirm_redirects_to_thanks(): void
    {
        [$table] = $this->seedMenu();
        $order = Order::create(['id_meja' => $table->id_meja, 'queue_number' => 1, 'status' => 'completed', 'total_amount' => 20000]);

        $this->get(route('order.confirm', $order))->assertRedirect(route('order.thanks', $order));
    }

    public function test_thanks_page_shows_review_for_completed_order(): void
    {
        [$table, $available] = $this->seedMenu();
        $order = Order::create(['id_meja' => $table->id_meja, 'queue_number' => 1, 'status' => 'completed', 'total_amount' => 20000]);
        OrderItem::create(['id_order' => $order->id_order, 'id_menu' => $available->id_menu, 'quantity' => 1, 'subtotal' => 20000]);

        $this->get(route('order.thanks', $order))
            ->assertOk()
            ->assertSee('Terima Kasih')
            ->assertSee('Beri Ulasan Menu')
            ->assertSee('Nasi Goreng'); // menu yang dipesan tampil di pilihan ulasan
    }
}
