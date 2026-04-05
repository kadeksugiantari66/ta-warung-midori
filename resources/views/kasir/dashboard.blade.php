<x-station title="Kasir" :poll-url="route('kasir.poll')" :poll-hash="$pollHash">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Transaksi Hari Ini</p>
            <p class="text-4xl font-headline font-black text-primary">{{ $todayOrders->count() }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Pendapatan</p>
            <p class="text-xl font-headline font-black text-secondary">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm col-span-2 lg:col-span-1">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Sedang Diproses Dapur</p>
            <p class="text-4xl font-headline font-black text-tertiary">{{ $activeOrders->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Menunggu Pembayaran --}}
        <div>
            <h3 class="font-headline font-bold text-lg text-primary mb-4">Menunggu Pembayaran</h3>
            @if($pendingOrders->isEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-20 block mb-2" style="font-variation-settings:'FILL' 1">payments</span>
                    <p class="text-on-surface-variant text-sm">Tidak ada tagihan tertunda.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($pendingOrders as $order)
                        <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm border border-secondary-container">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="font-headline font-black text-2xl text-primary">{{ $order->table->table_number }}</span>
                                    <span class="text-on-surface-variant text-sm ml-2">#{{ $order->queue_number }}</span>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md bg-secondary text-white">{{ $order->payment?->method === 'midtrans' ? 'Digital' : 'Tunai' }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="font-bold text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                <a href="{{ route('kasir.payment.show', $order) }}" class="text-xs font-bold text-primary hover:underline bg-primary-fixed px-3 py-1.5 rounded-lg flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">point_of_sale</span> Proses
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Status Dapur --}}
        <div>
            <h3 class="font-headline font-bold text-lg text-primary mb-4">Status Dapur</h3>
            @if($activeOrders->isEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-20 block mb-2" style="font-variation-settings:'FILL' 1">soup_kitchen</span>
                    <p class="text-on-surface-variant text-sm">Tidak ada pesanan aktif.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($activeOrders as $order)
                        @php $badge = match($order->status) {
                            'confirmed'  => ['bg-[#caee5d] text-[#161e00]', 'Baru Masuk'],
                            'processing' => ['bg-[#ffdcc5] text-[#301400]', 'Dimasak'],
                            'ready'      => ['bg-[#bcf0ae] text-[#002201]', 'Siap Diantar'],
                            default      => ['bg-[#eceeec] text-[#42493e]', ucfirst($order->status)],
                        }; @endphp
                        <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex justify-between items-center">
                            <div>
                                <span class="font-headline font-black text-2xl text-primary">{{ $order->table->table_number }}</span>
                                <span class="text-on-surface-variant text-sm ml-2">#{{ $order->queue_number }}</span>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Riwayat Transaksi Hari Ini --}}
        <div>
            <h3 class="font-headline font-bold text-lg text-primary mb-4">Transaksi Hari Ini</h3>
            @if($todayOrders->isEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-20 block mb-2" style="font-variation-settings:'FILL' 1">receipt_long</span>
                    <p class="text-on-surface-variant text-sm">Belum ada transaksi hari ini.</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach ($todayOrders as $order)
                        <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex justify-between items-center">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-headline font-bold text-lg text-primary">{{ $order->table->table_number }}</span>
                                    <span class="text-on-surface-variant text-xs">#{{ $order->queue_number }}</span>
                                </div>
                                <p class="text-xs text-on-surface-variant mt-0.5">{{ $order->created_at->format('H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-sm text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <a href="{{ route('kasir.invoice', $order) }}" target="_blank"
                                   class="text-xs text-secondary hover:underline">Invoice</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-station>
