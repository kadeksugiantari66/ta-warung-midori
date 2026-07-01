<x-app-layout>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Laporan Bulanan</h2>
        <p class="text-on-surface-variant text-sm mt-1">Rekap penjualan & tren per bulan.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.export.pdf.monthly', ['month' => $month]) }}" target="_blank"
           class="flex items-center gap-2 bg-error-container text-on-error-container text-sm font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span> PDF
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
    <form method="GET" action="{{ route('admin.reports.monthly') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Bulan</label>
            <input type="month" name="month" value="{{ $month }}"
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
    <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-[0_4px_24px_rgba(21,66,18,0.04)] p-5 col-span-2">
        <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Total Pendapatan</p>
        <p class="text-2xl font-headline font-black text-primary mt-1">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</p>
        <p class="text-xs text-on-surface-variant mt-1">
            Tunai: Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}
            &nbsp;·&nbsp;
            Digital: Rp {{ number_format($summary['digital_revenue'], 0, ',', '.') }}
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Grafik --}}
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6">
        <h4 class="font-semibold text-base text-primary mb-4">Tren Pendapatan Harian</h4>
        <canvas id="monthlyChart" height="120"></canvas>
    </div>

    {{-- Tabel ringkas --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-container">
            <h4 class="font-semibold text-base text-primary">Per Hari</h4>
        </div>
        <div class="overflow-y-auto max-h-72">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low sticky top-0">
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Tanggal</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Order</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse ($dailyTrend as $day)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.reports.daily', ['date' => $day->date]) }}"
                                   class="text-xs font-semibold text-secondary hover:underline">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d M') }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-xs text-on-surface-variant">{{ $day->total_orders }}</td>
                            <td class="px-5 py-3 text-xs font-semibold">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-xs text-on-surface-variant">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: {!! $dailyTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toJson() !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! $dailyTrend->pluck('revenue')->toJson() !!},
            borderColor: '#154212',
            backgroundColor: 'rgba(21,66,18,0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#154212',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>

</x-app-layout>
