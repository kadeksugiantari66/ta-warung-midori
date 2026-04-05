<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pesanan Dikonfirmasi – SiMidori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-fixed": "#bcf0ae", "on-primary-fixed": "#002201",
                "secondary": "#506600", "secondary-container": "#caee5d", "on-secondary-fixed": "#161e00",
                "tertiary-fixed": "#ffdcc5", "on-tertiary-fixed": "#301400",
                "error": "#ba1a1a", "error-container": "#ffdad6", "on-error-container": "#93000a",
                "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f2f4f2", "surface-container": "#eceeec",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e", "outline-variant": "#c2c9bb",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen" x-data="pageApp()">

<header class="bg-[#f8faf8]/80 backdrop-blur-md border-b border-outline-variant/20 px-5 py-4 sticky top-0 z-10">
    <div class="max-w-lg mx-auto flex items-center gap-3">
        <span class="text-xl font-headline font-black text-primary">SiMidori</span>
        <span class="text-xs font-bold text-on-surface-variant">· Meja {{ $order->table->table_number }}</span>
    </div>
</header>

<main class="max-w-lg mx-auto px-5 py-8 space-y-5">

    {{-- Nomor Antrean --}}
    <div class="bg-primary rounded-[2rem] p-8 text-center text-white shadow-[0_8px_32px_rgba(21,66,18,0.2)] relative overflow-hidden">
        <p class="text-primary-fixed text-sm font-semibold mb-1 uppercase tracking-widest">Nomor Antrean</p>
        <p class="text-8xl font-headline font-black leading-none">{{ $order->queue_number }}</p>
        <p class="text-primary-fixed/70 text-sm mt-2">Meja {{ $order->table->table_number }}</p>
        <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[8rem] opacity-10 pointer-events-none"
              style="font-variation-settings:'FILL' 1">eco</span>
    </div>

    {{-- Status + Tombol Bayar --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.05)] p-6">

        {{-- Status indicator --}}
        <div class="flex items-center gap-3 mb-5">
            <span class="w-2.5 h-2.5 rounded-full animate-pulse"
                  :class="paid ? 'bg-[#bcf0ae]' : 'bg-[#caee5d]'"></span>
            <p class="font-semibold text-sm text-on-surface" x-text="statusLabel"></p>
        </div>

        <div class="space-y-2 mb-5">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-on-surface-variant">
                        {{ $item->quantity }}× {{ $item->product->name }}
                        @if($item->note) <span class="text-xs opacity-60">({{ $item->note }})</span> @endif
                    </span>
                    <span class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-outline-variant/20 pt-4 flex justify-between items-center mb-5">
            <span class="font-semibold text-sm">Total</span>
            <span class="font-headline font-black text-xl text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>

        {{-- Tombol bayar — hanya tampil jika belum bayar --}}
        <div x-show="!paid">
            @if($paymentMethod === 'midtrans')
                <button @click="loadSnap()" :disabled="loading"
                        class="w-full py-4 font-headline font-bold text-base rounded-2xl shadow-lg active:scale-95 transition-all disabled:opacity-50"
                        style="background:#154212; color:white">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">qr_code_2</span>
                        Bayar Sekarang
                    </span>
                    <span x-show="loading">Memuat...</span>
                </button>
                <p x-show="paymentError" x-text="paymentError" class="mt-2 text-xs text-center" style="color:#ba1a1a"></p>
            @else
                <div class="w-full py-4 px-6 text-center rounded-2xl border border-primary/20" style="background:#bcf0ae40">
                    <span class="material-symbols-outlined text-3xl block mb-2" style="color:#154212; font-variation-settings:'FILL' 1">point_of_sale</span>
                    <p class="font-bold text-base" style="color:#154212">Menunggu Pembayaran Tunai</p>
                    <p class="text-sm mt-1 text-on-surface-variant">Bayar di kasir.</p>
                </div>
            @endif
        </div>

        {{-- Sudah bayar --}}
        <div x-show="paid" class="text-center py-2">
            <span class="material-symbols-outlined text-3xl block mb-1" style="color:#154212; font-variation-settings:'FILL' 1">check_circle</span>
            <p class="font-bold text-sm" style="color:#154212" x-text="orderCompleted ? 'Pesanan Selesai' : 'Pembayaran Berhasil'"></p>
            <p class="text-xs text-on-surface-variant mt-1" x-text="orderCompleted ? 'Selamat makan!' : 'Pesanan sedang disiapkan.'"></p>
        </div>
    </div>

    {{-- Rating & Ulasan — muncul dinamis saat completed --}}
    <div x-show="orderCompleted" x-cloak
         class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.05)] p-6">
        <h3 class="font-headline font-bold text-lg text-primary mb-4">Beri Ulasan Menu</h3>

        <div x-show="reviewSent" class="text-center py-4">
            <span class="material-symbols-outlined text-4xl text-secondary block mb-2" style="font-variation-settings:'FILL' 1">check_circle</span>
            <p class="font-semibold text-on-surface">Ulasan terkirim!</p>
        </div>

        <div x-show="!reviewSent" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Pilih Menu</label>
                <select x-model="selectedProduct"
                        class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary">
                    <option value="">-- Pilih menu --</option>
                    @foreach ($order->orderItems as $item)
                        <option value="{{ $item->product_id }}">{{ $item->product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Rating</label>
                <div class="flex gap-1">
                    <template x-for="star in [1,2,3,4,5]" :key="star">
                        <button @click="rating = star" type="button"
                                :class="star <= rating ? 'text-yellow-400' : 'text-outline-variant'"
                                class="text-4xl leading-none hover:text-yellow-400 transition-colors">★</button>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Komentar (opsional)</label>
                <textarea x-model="comment" rows="2" placeholder="Bagaimana rasanya?"
                          class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary"></textarea>
            </div>

            <button @click="submitReview()" :disabled="!selectedProduct || rating === 0"
                    class="w-full py-3 bg-primary text-white font-semibold rounded-2xl hover:opacity-90 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed text-sm">
                Kirim Ulasan
            </button>
        </div>
    </div>

    {{-- Tombol Pesan Lagi — muncul saat completed --}}
    <div x-show="orderCompleted" x-cloak>
        <a href="{{ route('order.menu', $order->table) }}"
           class="flex items-center justify-center gap-2 w-full py-4 bg-primary text-white font-headline font-bold text-base rounded-2xl shadow-lg hover:opacity-90 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">restaurant_menu</span>
            Pesan Lagi
        </a>
    </div>

    <p class="text-center text-xs text-on-surface-variant pb-4">Warung Midori · Bangli, Bali</p>
</main>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
function pageApp() {
    return {
        // payment state
        statusLabel: 'Selesaikan pembayaran untuk memproses pesanan.',
        paid: {{ $order->payment?->status === 'paid' ? 'true' : 'false' }},
        orderCompleted: {{ $order->status === 'completed' ? 'true' : 'false' }},
        loading: false,
        snapToken: null,
        paymentError: null,
        timer: null,

        // review state
        selectedProduct: '',
        rating: 0,
        comment: '',
        reviewSent: false,

        init() {
            this.startPolling();
        },

        startPolling() {
            this.timer = setInterval(() => this.pollStatus(), 5000);
        },

        async pollStatus() {
            try {
                const res  = await fetch('{{ route('order.status', $order) }}');
                const data = await res.json();

                this.statusLabel = data.label;
                if (data.paid) this.paid = true;
                if (data.status === 'completed') {
                    this.orderCompleted = true;
                    clearInterval(this.timer);
                }
            } catch(e) {}
        },

        async loadSnap() {
            this.loading = true;
            this.paymentError = null;
            try {
                const res  = await fetch('{{ route('midtrans.snap-token', $order) }}');
                const data = await res.json();
                if (data.snap_token) {
                    this.snapToken = data.snap_token;
                    this.openSnap();
                } else {
                    this.paymentError = 'Gagal memuat pembayaran. Coba lagi.';
                }
            } catch(e) {
                this.paymentError = 'Terjadi kesalahan. Coba lagi.';
            } finally {
                this.loading = false;
            }
        },

        openSnap() {
            window.snap.pay(this.snapToken, {
                onSuccess: async (result) => {
                    try {
                        await fetch('{{ route('midtrans.verify', $order) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ transaction_id: result.transaction_id, order_id: result.order_id })
                        });
                    } catch(e) {}
                    this.paid = true;
                    this.startPolling();
                },
                onPending: () => {
                    this.statusLabel = 'Menunggu konfirmasi pembayaran...';
                    this.startPolling();
                },
                onError:   () => { this.paymentError = 'Pembayaran gagal. Silakan coba lagi.'; },
                onClose:   () => {},
            });
        },

        async submitReview() {
            if (!this.selectedProduct || this.rating === 0) return;
            const res = await fetch('{{ route('order.review') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ product_id: this.selectedProduct, rating: this.rating, comment: this.comment }),
            });
            if (res.ok) this.reviewSent = true;
        }
    }
}
</script>
</body>
</html>
