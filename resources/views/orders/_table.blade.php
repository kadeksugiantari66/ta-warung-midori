{{-- Partial daftar pesanan + filter. Dipakai oleh admin & kasir. Variabel: $orders, $statuses, $status, $from, $to, $search, $prefix --}}

{{-- Filter: status & rentang tanggal --}}
<form method="GET" action="{{ route($prefix.'.orders.index') }}"
      class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 shadow-sm p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Cari</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="No. antrean / meja"
               class="w-full border-outline-variant rounded-xl bg-surface-container-low px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Status</label>
        <select name="status"
                class="w-full border-outline-variant rounded-xl bg-surface-container-low px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
            <option value="">Semua status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $from }}"
               class="w-full border-outline-variant rounded-xl bg-surface-container-low px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $to }}"
               class="w-full border-outline-variant rounded-xl bg-surface-container-low px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="flex-1 bg-primary text-white font-semibold py-2 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
            Filter
        </button>
        <a href="{{ route($prefix.'.orders.index') }}"
           class="px-3 py-2 rounded-xl border border-outline-variant/40 text-on-surface-variant hover:bg-surface-container text-sm flex items-center">
            Reset
        </a>
    </div>
</form>

{{-- Tabel pesanan --}}
<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Antrean</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Meja</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Item Pesanan</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Bayar</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Waktu</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @forelse ($orders as $order)
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
                        $bayar = match ($order->payment?->method) {
                            'tunai'    => 'Tunai',
                            'midtrans' => 'Digital',
                            default    => '—',
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-sm whitespace-nowrap">#{{ $order->queue_number }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $order->table->table_number }}</td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant max-w-xs truncate">
                            {{ $order->orderItems->map(fn ($i) => $i->quantity.'× '.$i->product->name)->join(', ') ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            {{ $bayar }}
                            @if ($order->payment?->status === 'paid')
                                <span class="text-[10px] font-bold text-primary">• Lunas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap {{ $badge[0] }}">{{ $badge[1] }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-on-surface-variant whitespace-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route($prefix.'.orders.show', $order) }}"
                               class="inline-flex items-center gap-1 text-sm text-primary hover:underline font-medium">
                                <span class="material-symbols-outlined text-base">visibility</span> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada pesanan yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-surface-container">{{ $orders->links() }}</div>
</div>
