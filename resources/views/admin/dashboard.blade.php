<x-app-layout>

    {{-- Header --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline text-3xl font-black text-primary tracking-tight">Dashboard</h2>
            <p class="text-on-surface-variant font-medium">Monitoring real-time operasional harian SiMidori.</p>
        </div>
        <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2 rounded-2xl">
            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-on-primary-container text-base">person</span>
            </div>
            <div>
                <p class="text-sm font-bold leading-none">{{ auth()->user()->name }}</p>
                <p class="text-xs text-on-surface-variant capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 group hover:bg-primary transition-colors duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary-container rounded-xl group-hover:bg-secondary-fixed transition-colors">
                    <span class="material-symbols-outlined text-on-primary-container group-hover:text-on-secondary-fixed">shopping_bag</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold group-hover:text-white/70">Pesanan Hari Ini</p>
            <h3 class="text-3xl font-headline font-bold mt-1 group-hover:text-white">{{ $todayOrders }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 group hover:bg-primary transition-colors duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary-container rounded-xl">
                    <span class="material-symbols-outlined text-on-secondary-container">payments</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold group-hover:text-white/70">Pendapatan Hari Ini</p>
            <h3 class="text-2xl font-headline font-bold mt-1 group-hover:text-white">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 group hover:bg-primary transition-colors duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-tertiary-fixed rounded-xl">
                    <span class="material-symbols-outlined text-on-tertiary-fixed">pending_actions</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold group-hover:text-white/70">Pesanan Aktif</p>
            <h3 class="text-3xl font-headline font-bold mt-1 group-hover:text-white">{{ $pendingOrders }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 group hover:bg-primary transition-colors duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary-fixed rounded-xl">
                    <span class="material-symbols-outlined text-on-primary-fixed">table_restaurant</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold group-hover:text-white/70">Meja Terisi</p>
            <h3 class="text-3xl font-headline font-bold mt-1 group-hover:text-white">
                {{ $occupiedTables }}<span class="text-lg text-on-surface-variant group-hover:text-white/50">/{{ $totalTables }}</span>
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Grafik Tren --}}
        <div class="lg:col-span-8 bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 p-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-headline text-lg font-bold text-primary">Pendapatan 7 Hari Terakhir</h4>
                <a href="{{ route('admin.reports.monthly') }}"
                   class="text-xs font-semibold text-secondary hover:underline">Lihat Laporan →</a>
            </div>
            <canvas id="trendChart" height="100"></canvas>
        </div>

        {{-- Top Menu + Shortcut --}}
        <div class="lg:col-span-4 space-y-5">
            <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.04)] border border-outline-variant/10 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-headline text-lg font-bold text-primary">Menu Populer</h4>
                    <a href="{{ route('admin.products.popular') }}" class="text-xs font-semibold text-secondary hover:underline">Semua →</a>
                </div>
                <div class="space-y-3">
                    @forelse ($topToday as $i => $product)
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-on-surface-variant w-4">{{ $i + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $product->name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $product->category->name }}</p>
                            </div>
                            <span class="text-sm font-bold text-secondary">{{ $product->today_count }}x</span>
                        </div>
                    @empty
                        <p class="text-sm text-on-surface-variant">Belum ada pesanan hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>


    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: {!! $trendLabels->toJson() !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! $trendData->toJson() !!},
            backgroundColor: 'rgba(21, 66, 18, 0.75)',
            borderRadius: 8,
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
