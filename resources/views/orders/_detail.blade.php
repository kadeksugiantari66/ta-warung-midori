{{-- Partial detail pesanan. Dipakai oleh admin & kasir. Variabel: $order, $prefix --}}

<a href="{{ route($prefix.'.orders.index') }}"
   class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors mb-5">
    <span class="material-symbols-outlined text-base">arrow_back</span>
    Kembali ke Daftar Pesanan
</a>

@php
    $badge = match ($order->status) {
        'pending'    => ['bg-[#ffe082] text-[#5f4700]', 'Menunggu'],
        'confirmed'  => ['bg-secondary-container text-on-secondary-container', 'Dikonfirmasi'],
        'processing' => ['bg-tertiary-fixed text-on-tertiary-fixed', 'Dimasak'],
        'ready'      => ['bg-primary-fixed text-on-primary-fixed', 'Siap'],
        'completed'  => ['bg-[#d7f5cd] text-[#0f3d0c]', 'Selesai'],
        'cancelled'  => ['bg-error-container text-on-error-container', 'Dibatalkan'],
        default      => ['bg-surface-container text-on-surface-variant', ucfirst($order->status)],
    };
    $metode = match ($order->payment?->method) {
        'tunai'    => 'Tunai',
        'midtrans' => 'Digital (Midtrans)',
        default    => '—',
    };
@endphp

<div class="max-w-2xl space-y-5">
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border-l-4 border-primary">
        {{-- Header --}}
        <div class="flex justify-between items-center px-6 py-5 border-b border-surface-container">
            <div class="flex items-center gap-4">
                <span class="font-headline font-black text-4xl text-primary">{{ $order->table->table_number }}</span>
                <div>
                    <p class="text-xs text-on-surface-variant">Nomor Antrean</p>
                    <p class="font-bold text-2xl leading-none">#{{ $order->queue_number }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                <p class="text-xs text-on-surface-variant mt-2">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        {{-- Item --}}
        <div class="px-6 py-4 space-y-2">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span>
                        <span class="font-bold">{{ $item->quantity }}×</span>
                        {{ $item->product->name }}
                        @if ($item->note) <span class="text-xs text-on-surface-variant">({{ $item->note }})</span> @endif
                    </span>
                    <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        {{-- Pembayaran & total --}}
        <div class="px-6 py-4 border-t border-surface-container flex justify-between items-center">
            <div class="text-sm">
                <span class="text-on-surface-variant">Metode: </span>
                <span class="font-semibold">{{ $metode }}</span>
                @if ($order->payment)
                    <span class="ml-2 text-xs font-bold {{ $order->payment->status === 'paid' ? 'text-primary' : 'text-on-surface-variant' }}">
                        {{ $order->payment->status === 'paid' ? 'Lunas' : ucfirst($order->payment->status) }}
                    </span>
                @endif
            </div>
            <div class="text-right">
                <p class="text-xs text-on-surface-variant">Total</p>
                <p class="font-headline font-black text-2xl text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Aksi kontekstual untuk kasir --}}
    @if ($prefix === 'kasir')
        <div class="flex flex-col sm:flex-row gap-3">
            @if ($order->payment?->status !== 'paid' && ! in_array($order->status, ['completed', 'cancelled']))
                <a href="{{ route('kasir.payment.show', $order) }}"
                   class="flex-1 text-center bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
                    Proses Pembayaran
                </a>
            @endif
            @if ($order->payment?->status === 'paid')
                <a href="{{ route('kasir.invoice', $order) }}" target="_blank"
                   class="flex-1 inline-flex items-center justify-center gap-2 bg-secondary text-white font-bold py-3 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
                    <span class="material-symbols-outlined text-base">print</span>
                    Cetak Invoice
                </a>
            @endif
        </div>
    @endif
</div>
