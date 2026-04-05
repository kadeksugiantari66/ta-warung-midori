<x-station title="Proses Pembayaran Digital">
<div class="max-w-2xl mx-auto space-y-5" x-data="paymentApp()">

    <a href="{{ route('kasir.dashboard') }}"
       class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Kembali
    </a>

    {{-- Header pesanan --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border-l-4 border-primary">
        <div class="flex justify-between items-center px-6 py-5 border-b border-surface-container">
            <div class="flex items-center gap-4">
                <span class="font-headline font-black text-4xl text-primary">{{ $order->table->table_number }}</span>
                <div>
                    <p class="text-xs text-on-surface-variant">Nomor Antrean</p>
                    <p class="font-bold text-2xl leading-none">#{{ $order->queue_number }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-on-surface-variant mb-1">Total Tagihan</p>
                <p class="font-headline font-black text-3xl text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Item list --}}
        <div class="px-6 py-4 space-y-2">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span>
                        <span class="font-bold">{{ $item->quantity }}×</span>
                        {{ $item->product->name }}
                        @if($item->note) <span class="text-xs text-on-surface-variant">({{ $item->note }})</span> @endif
                    </span>
                    <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Status sudah bayar --}}
    @if ($order->payment?->status === 'paid')
        <div class="bg-primary-fixed rounded-2xl p-6 text-center">
            <span class="material-symbols-outlined text-4xl text-primary block mb-2" style="font-variation-settings:'FILL' 1">check_circle</span>
            <p class="font-bold text-primary text-lg">Pembayaran Lunas</p>
            <p class="text-sm text-on-surface-variant mt-1">{{ $order->payment?->method === 'midtrans' ? 'Digital (Midtrans)' : 'Tunai' }}</p>
            <a href="{{ route('kasir.invoice', $order) }}" target="_blank"
               class="inline-flex items-center gap-2 mt-4 bg-primary text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-base">print</span>
                Cetak Invoice
            </a>
        </div>

    @else
        {{-- Jika metode tunai --}}
        @if($order->payment?->method === 'tunai')
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-3xl text-primary" style="font-variation-settings:'FILL' 1">payments</span>
                </div>
                <p class="font-bold text-lg mb-1">Pembayaran Tunai</p>
                <p class="text-sm text-on-surface-variant mb-6">Pastikan Anda telah menerima uang tunai sejumlah <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> dari pelanggan sebelum mengonfirmasi.</p>

                <form action="{{ route('kasir.payment.cash', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all outline-none focus:ring-4 focus:ring-primary/30">
                        Konfirmasi Pembayaran Uang Tunai
                    </button>
                </form>
            </div>
        @else
            {{-- Pembayaran Digital --}}
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-secondary-container" style="font-variation-settings:'FILL' 1">qr_code_2</span>
                    </div>
                    <div>
                        <p class="font-bold">Pembayaran Digital</p>
                        <p class="text-xs text-on-surface-variant">QRIS, Transfer Bank via Midtrans</p>
                    </div>
                </div>

                <div x-show="!snapToken" class="space-y-3">
                    <button @click="loadSnap()" :disabled="loading"
                            class="w-full py-3 bg-secondary text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all disabled:opacity-50">
                        <span x-show="!loading">Bayar Digital</span>
                        <span x-show="loading">Memuat...</span>
                    </button>
                    <form action="{{ route('kasir.payment.cash', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 border border-outline-variant/50 text-on-surface-variant font-bold rounded-xl hover:bg-surface-container-low transition-all">
                            Selesaikan dengan Tunai (Ubah Metode)
                        </button>
                    </form>
                </div>
                <div x-show="snapToken">
                    <button @click="openSnap()"
                            class="w-full py-3 bg-secondary text-white font-bold rounded-xl hover:opacity-90 transition-all">
                        Buka Halaman Pembayaran
                    </button>
                    <button @click="snapToken = null" class="w-full mt-3 py-3 border border-outline-variant/50 text-on-surface-variant font-bold rounded-xl hover:bg-surface-container-low transition-all">Batal</button>
                </div>
                <p x-show="paymentError" x-text="paymentError" class="mt-2 text-xs text-error"></p>
            </div>
        @endif
    @endif

</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
function paymentApp() {
    return {
        snapToken: null, loading: false, paymentError: null,
        async loadSnap() {
            this.loading = true; this.paymentError = null;
            try {
                const res  = await fetch('{{ route('midtrans.snap-token', $order) }}');
                const data = await res.json();
                if (data.snap_token) { this.snapToken = data.snap_token; this.openSnap(); }
                else { this.paymentError = 'Gagal memuat pembayaran.'; }
            } catch (e) { this.paymentError = 'Terjadi kesalahan.'; }
            finally { this.loading = false; }
        },
        openSnap() {
            window.snap.pay(this.snapToken, {
                onSuccess: () => window.location.reload(),
                onPending: () => window.location.reload(),
                onError:   () => { this.paymentError = 'Pembayaran gagal.'; },
                onClose:   () => {},
            });
        }
    }
}
</script>
</x-station>
