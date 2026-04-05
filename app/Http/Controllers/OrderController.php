<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman menu pelanggan setelah scan QR.
     */
    public function menu(Table $table): View
    {
        // Jika meja occupied dan ada order aktif, redirect ke halaman konfirmasi
        if ($table->status === 'occupied') {
            $activeOrder = $table->orders()
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()
                ->first();

            if ($activeOrder) {
                return view('order.menu', [
                    'table'      => $table,
                    'categories' => collect(),
                    'allProducts'=> [],
                    'redirect'   => route('order.confirm', $activeOrder),
                ]);
            }

            abort(423, 'Meja sedang digunakan.');
        }

        $categories = Category::with(['products' => function ($q) {
            $q->where('is_available', true)
              ->with('reviews')
              ->orderBy('name');
        }])->get()->filter(fn($c) => $c->products->isNotEmpty());

        // Flatten semua produk untuk search di client-side
        $allProducts = $categories->flatMap(function ($c) {
            return $c->products->map(function ($p) use ($c) {
                return [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'description' => $p->description ?? '',
                    'price'       => (float) $p->price,
                    'image'       => $p->image ? \Illuminate\Support\Facades\Storage::url($p->image) : null,
                    'category'    => $c->name,
                ];
            });
        })->values();

        return view('order.menu', compact('table', 'categories', 'allProducts'));
    }

    /**
     * Menyimpan data pesanan baru dari pelanggan.
     */
    public function store(Request $request, Table $table): RedirectResponse
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.note'       => ['nullable', 'string', 'max:255'],
            'payment_method'     => ['required', 'in:midtrans,tunai'],
        ]);

        // Nomor antrean: hitung pesanan hari ini + 1
        $queueNumber = Order::whereDate('created_at', today())->count() + 1;

        $order = Order::create([
            'table_id'     => $table->id,
            'queue_number' => $queueNumber,
            'status'       => 'pending',
            'total_amount' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product  = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $total   += $subtotal;

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'note'       => $item['note'] ?? null,
                'subtotal'   => $subtotal,
            ]);
        }

        $order->update(['total_amount' => $total]);

        // Tandai meja sebagai occupied
        $table->update(['status' => 'occupied']);

        // Simpan info ke session untuk halaman konfirmasi
        session([
            'order_id'       => $order->id,
            'payment_method' => $request->payment_method,
        ]);

        // Simpan record payment awal (terutama untuk tunai)
        \App\Models\Payment::create([
            'order_id' => $order->id,
            'method'   => $request->payment_method,
            'amount'   => $order->total_amount,
            'status'   => 'pending',
        ]);

        return redirect()->route('order.confirm', $order);
    }

    /**
     * Menampilkan halaman konfirmasi dan nomor antrean.
     * Bisa diakses ulang selama order belum completed.
     */
    public function confirm(Order $order): View
    {
        // Jika session ada, simpan ulang (refresh)
        if (session('order_id') == $order->id) {
            session(['order_id' => $order->id]);
        }

        // Izinkan akses selama order masih aktif (bukan completed/cancelled)
        if (in_array($order->status, ['completed', 'cancelled']) && session('order_id') != $order->id) {
            $order->load('table');
            return view('order.done', compact('order'));
        }

        $order->load(['orderItems.product', 'table']);
        $paymentMethod = session('payment_method', 'tunai');

        return view('order.confirm', compact('order', 'paymentMethod'));
    }

    /**
     * Polling endpoint: cek status order terkini untuk halaman konfirmasi pelanggan.
     */
    public function status(Order $order): JsonResponse
    {
        $order->load('payment');

        $label = match($order->status) {
            'pending'    => 'Menunggu konfirmasi kasir...',
            'confirmed'  => 'Pembayaran dikonfirmasi! Pesanan sedang disiapkan dapur.',
            'processing' => 'Pesanan sedang dimasak oleh dapur.',
            'ready'      => 'Pesanan siap diantar ke meja Anda!',
            'completed'  => 'Pesanan selesai. Selamat menikmati!',
            'cancelled'  => 'Pesanan dibatalkan.',
            default      => ucfirst($order->status),
        };

        return response()->json([
            'status' => $order->status,
            'label'  => $label,
            'paid'   => $order->payment?->status === 'paid',
        ]);
    }

    /**
     * Menyimpan ulasan dan rating dari pelanggan setelah selesai makan.
     */
    public function review(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'comment'    => ['nullable', 'string', 'max:500'],
        ]);

        $review = Review::create($request->only('product_id', 'rating', 'comment'));

        return response()->json(['message' => 'Ulasan berhasil disimpan.', 'review' => $review]);
    }
}
