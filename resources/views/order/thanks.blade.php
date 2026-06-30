<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Terima Kasih – Warung Midori</title>
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
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex flex-col items-center justify-center px-5 py-10" x-data="thanksApp()">

    <div class="max-w-md w-full space-y-6">

        {{-- Hero Terima Kasih --}}
        <div class="text-center">
            <div class="w-24 h-24 rounded-full bg-primary-fixed flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-5xl text-primary" style="font-variation-settings:'FILL' 1">check_circle</span>
            </div>
            <h1 class="font-headline text-3xl font-black text-primary mb-2">Terima Kasih!</h1>
            <p class="text-on-surface-variant text-sm leading-relaxed">
                Pesanan #{{ $order->queue_number }} di Meja {{ $order->table->table_number }} sudah selesai.<br>
                Semoga makan siang/malamnya menyenangkan.
            </p>
        </div>

        {{-- Form Ulasan --}}
        <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.05)] p-6">
            <h3 class="font-headline font-bold text-lg text-primary mb-4">Beri Ulasan Menu</h3>

            <div x-show="reviewSent" class="text-center py-4">
                <span class="material-symbols-outlined text-4xl text-secondary block mb-2" style="font-variation-settings:'FILL' 1">check_circle</span>
                <p class="font-semibold text-on-surface">Ulasan terkirim!</p>
                <p class="text-xs text-on-surface-variant mt-1">Terima kasih, mengalihkan ke halaman utama...</p>
            </div>

            <div x-show="!reviewSent" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Pilih Menu</label>
                    <select x-model="selectedProduct"
                            class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary">
                        <option value="">-- Pilih menu --</option>
                        @foreach ($order->orderItems as $item)
                            <option value="{{ $item->id_menu }}">{{ $item->product->name }}</option>
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
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Komentar</label>
                    <textarea x-model="comment" rows="2" placeholder="Bagaimana rasanya?"
                              class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary"></textarea>
                </div>

                <button @click="submitReview()" :disabled="!selectedProduct || rating === 0"
                        class="w-full py-3 bg-primary text-white font-semibold rounded-2xl hover:opacity-90 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed text-sm">
                    Kirim Ulasan
                </button>
            </div>
        </div>

        <p class="text-center text-xs text-on-surface-variant">Terima kasih telah memesan di Warung Midori.</p>
    </div>

    <script>
    function thanksApp() {
        return {
            selectedProduct: '',
            rating: 0,
            comment: '',
            reviewSent: false,

            async submitReview() {
                if (!this.selectedProduct || this.rating === 0) return;
                const res = await fetch('{{ route('order.review') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ id_menu: this.selectedProduct, rating: this.rating, comment: this.comment }),
                });
                if (res.ok) {
                    this.reviewSent = true;
                    // Beri jeda 2 detik lalu kembali ke halaman awal
                    setTimeout(() => { window.location.href = '/'; }, 2000);
                }
            }
        }
    }
    </script>
</body>
</html>
