<x-app-layout>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Laporan Harian</h2>
        <p class="text-on-surface-variant text-sm mt-1">Detail transaksi per hari.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.export.pdf', ['date' => $date]) }}"
           class="flex items-center gap-2 bg-error-container text-on-error-container text-sm font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span> PDF
        </a>
        <a href="{{ route('admin.reports.export.excel', ['date' => $date]) }}"
           class="flex items-center gap-2 bg-secondary-container text-on-secondary-fixed text-sm font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-base">table_view</span> Excel
        </a>
    </div>
</div>

{{-- Tab Navigasi --}}
<div class="flex flex-wrap gap-2 mb-6 border-b border-surface-container pb-4">
    <a href="{{ route('admin.reports.daily') }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('admin.reports.daily') ? 'bg-primary text-white shadow-md' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
        Laporan Harian
    </a>
    <a href="{{ route('admin.reports.monthly') }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('admin.reports.monthly') ? 'bg-primary text-white shadow-md' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
        Laporan Bulanan
    </a>
    <a href="{{ route('admin.reports.top-products') }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('admin.reports.top-products') ? 'bg-primary text-white shadow-md' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
        Menu Terlaris
    </a>
</div>

{{-- Filter --}}
<div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.daily') }}" class="flex gap-3 items-end flex-wrap">
        <div>
            <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}"
                   class="border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
        <button type="submit"
                class="bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all">
            Filter
        </button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5">
        <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Total Pesanan</p>
        <p class="text-3xl font-headline font-black text-primary mt-1">{{ $summary['total_orders'] }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5">
        <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Selesai</p>
        <p class="text-3xl font-headline font-black text-secondary mt-1">{{ $summary['completed'] }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5">
        <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Pendapatan</p>
        <p class="text-xl font-headline font-black text-primary mt-1">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5">
        <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Tunai / Digital</p>
        <p class="text-sm font-bold mt-1">Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}</p>
        <p class="text-sm font-bold text-secondary">Rp {{ number_format($summary['digital_revenue'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Tabel Transaksi --}}
<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-surface-container-low">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Antrean</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Meja</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Item</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Bayar</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Waktu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
            @forelse ($orders as $order)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-4 font-headline font-bold text-lg text-primary">#{{ $order->queue_number }}</td>
                    <td class="px-6 py-4 text-sm font-semibold">{{ $order->table->table_number }}</td>
                    <td class="px-6 py-4 text-xs text-on-surface-variant max-w-[200px] truncate">
                        {{ $order->orderItems->map(fn($i) => $i->quantity.'× '.$i->product->name)->join(', ') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">
                        {{ $order->payment?->method === 'cash' ? 'Tunai' : ($order->payment ? 'Digital' : '—') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full
                            {{ $order->status === 'completed' ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-secondary-container text-on-secondary-container' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-on-surface-variant">{{ $order->created_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">receipt_long</span>
                        Tidak ada transaksi pada tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-app-layout>
