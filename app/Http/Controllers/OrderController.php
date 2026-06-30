<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman menu pelanggan setelah scan QR.
     */
    public function menu(Request $request, Table $table): View
    {
        // QR dinamis: tolak akses jika token tidak cocok
        // (mencegah pemesanan dari luar restoran memakai QR lama/hasil foto)
        if ($table->qr_token && $request->query('token') !== $table->qr_token) {
            return view('order.invalid', compact('table'));
        }

        // Catatan: pengecekan ketersediaan meja DIHAPUS sesuai revisi penguji.
        // Tampilkan SEMUA menu (termasuk yang habis) beserta ulasannya.
        $categories = Category::with(['products' => function ($q) {
            $q->with('reviews')->orderBy('name');
        }])->get()->filter(fn ($c) => $c->products->isNotEmpty());

        // Flatten semua produk untuk search di client-side (+ status habis & rating)
        $allProducts = $categories->flatMap(function ($c) {
            return $c->products->map(function ($p) use ($c) {
                return [
                    'id' => $p->id_menu,
                    'name' => $p->name,
                    'description' => $p->description ?? '',
                    'price' => (float) $p->price,
                    'image' => $p->image ? Storage::url($p->image) : null,
                    'category' => $c->name,
                    'available' => (bool) $p->is_available,
                    'rating' => round($p->reviews->avg('rating') ?: 0, 1),
                    'review_count' => $p->reviews->count(),
                ];
            });
        })->values();

        // Data ulasan agar dapat dibaca pelanggan (dikelompokkan per menu)
        $reviewsData = $categories->flatMap(fn ($c) => $c->products)->mapWithKeys(fn ($p) => [
            $p->id_menu => [
                'name' => $p->name,
                'avg' => round($p->reviews->avg('rating') ?: 0, 1),
                'count' => $p->reviews->count(),
                'items' => $p->reviews->sortByDesc('created_at')->take(20)->map(fn ($r) => [
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                ])->values(),
            ],
        ]);

        return view('order.menu', compact('table', 'categories', 'allProducts', 'reviewsData'));
    }

    /**
     * Menyimpan data pesanan baru dari pelanggan.
     */
    public function store(Request $request, Table $table): RedirectResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_menu' => ['required', 'exists:menu,id_menu'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:midtrans,tunai'],
            'customer_email' => ['nullable', 'email', 'max:255'],
        ]);

        // Nomor antrean: hitung pesanan hari ini + 1
        $queueNumber = Order::whereDate('created_at', today())->count() + 1;

        $order = Order::create([
            'id_meja' => $table->id_meja,
            'queue_number' => $queueNumber,
            'customer_email' => $request->customer_email,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['id_menu']);
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $product->id_menu,
                'quantity' => $item['quantity'],
                'note' => $item['note'] ?? null,
                'subtotal' => $subtotal,
            ]);
        }

        $order->update(['total_amount' => $total]);

        // Tandai meja sebagai occupied
        $table->update(['status' => 'occupied']);

        // Simpan info ke session untuk halaman konfirmasi
        session([
            'order_id' => $order->id_order,
            'payment_method' => $request->payment_method,
        ]);

        // Simpan record payment awal (terutama untuk tunai)
        Payment::create([
            'id_order' => $order->id_order,
            'method' => $request->payment_method,
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        return redirect()->route('order.confirm', $order);
    }

    /**
     * Menampilkan halaman konfirmasi dan nomor antrean.
     * Bisa diakses ulang selama order belum completed.
     */
    public function confirm(Order $order): View|RedirectResponse
    {
        // Pesanan selesai -> arahkan ke halaman terima kasih + ulasan
        if ($order->status === 'completed') {
            return redirect()->route('order.thanks', $order);
        }

        if ($order->status === 'cancelled') {
            $order->load('table');

            return view('order.done', compact('order'));
        }

        $order->load(['orderItems.product', 'table']);
        $paymentMethod = session('payment_method', 'tunai');

        return view('order.confirm', compact('order', 'paymentMethod'));
    }

    /**
     * Halaman "Terima Kasih" + form ulasan, tampil setelah pesanan selesai.
     */
    public function thanks(Order $order): View|RedirectResponse
    {
        if ($order->status !== 'completed') {
            return redirect()->route('order.confirm', $order);
        }

        $order->load(['orderItems.product', 'table']);

        return view('order.thanks', compact('order'));
    }

    /**
     * Polling endpoint: cek status order terkini untuk halaman konfirmasi pelanggan.
     */
    public function status(Order $order): JsonResponse
    {
        $order->load('payment');

        $label = match ($order->status) {
            'pending' => 'Menunggu konfirmasi kasir...',
            'confirmed' => 'Pembayaran dikonfirmasi! Pesanan sedang disiapkan dapur.',
            'processing' => 'Pesanan sedang dimasak oleh dapur.',
            'ready' => 'Pesanan siap diantar ke meja Anda!',
            'completed' => 'Pesanan selesai. Selamat menikmati!',
            'cancelled' => 'Pesanan dibatalkan.',
            default => ucfirst($order->status),
        };

        return response()->json([
            'status' => $order->status,
            'label' => $label,
            'paid' => $order->payment?->status === 'paid',
        ]);
    }

    /**
     * Pelanggan menandai pesanan sudah selesai (sudah diterima di meja).
     * Hanya untuk pesanan yang sudah dibayar dan sedang dalam proses.
     */
    public function complete(Order $order): JsonResponse
    {
        $order->load('payment');

        if ($order->payment?->status !== 'paid' || ! in_array($order->status, ['confirmed', 'processing', 'ready'])) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diselesaikan.'], 422);
        }

        $order->complete();

        return response()->json(['success' => true]);
    }

    /**
     * Menyimpan ulasan dan rating dari pelanggan setelah selesai makan.
     */
    public function review(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'exists:menu,id_menu'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $review = Review::create($request->only('id_menu', 'rating', 'comment'));

        return response()->json(['message' => 'Ulasan berhasil disimpan.', 'review' => $review]);
    }
}
