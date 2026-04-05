<x-app-layout>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Menu Terlaris</h2>
        <p class="text-on-surface-variant text-sm mt-1">Statistik menu paling banyak dipesan.</p>
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
    <form method="GET" action="{{ route('admin.reports.top-products') }}" class="flex gap-3 items-end">
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Chart --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6">
        <h4 class="font-semibold text-base text-primary mb-4">Grafik Top 10</h4>
        <canvas id="topChart" height="200"></canvas>
    </div>

    {{-- Tabel --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">#</th>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Menu</th>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Harga</th>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Dipesan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @forelse ($products as $i => $product)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-5 py-4 font-headline font-black text-lg text-on-surface-variant">{{ $i + 1 }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold">{{ $product->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $product->category->name }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right">
                            <span class="px-3 py-1 bg-secondary-container text-on-secondary-fixed text-sm font-bold rounded-full">
                                {{ $product->total_ordered }}×
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">bar_chart</span>
                            Belum ada data bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('topChart'), {
    type: 'bar',
    data: {
        labels: {!! $products->pluck('name')->toJson() !!},
        datasets: [{
            label: 'Total Dipesan',
            data: {!! $products->pluck('total_ordered')->toJson() !!},
            backgroundColor: [
                '#154212','#2d5a27','#3b6934','#506600','#546b00',
                '#bcf0ae','#caee5d','#ccf05f','#a1d494','#b1d446',
            ],
            borderRadius: 8,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { stepSize: 1 } },
            y: { grid: { display: false } }
        }
    }
});
</script>

</x-app-layout>
