<x-station title="Dapur" :poll-url="route('dapur.poll')" :poll-hash="$pollHash">

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-3 lg:gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Baru Masuk</p>
            <p class="text-4xl font-headline font-black text-secondary">{{ $activeOrders->where('status','confirmed')->count() }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Sedang Dimasak</p>
            <p class="text-4xl font-headline font-black text-tertiary">{{ $activeOrders->where('status','processing')->count() }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-4 text-center shadow-sm">
            <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-1">Siap Diantar</p>
            <p class="text-4xl font-headline font-black text-primary">{{ $activeOrders->where('status','ready')->count() }}</p>
        </div>
    </div>

    @if($activeOrders->isEmpty())
        <div class="bg-surface-container-lowest rounded-2xl p-16 text-center shadow-sm">
            <span class="material-symbols-outlined text-6xl text-on-surface-variant opacity-20 block mb-3" style="font-variation-settings:'FILL' 1">soup_kitchen</span>
            <p class="text-on-surface-variant font-medium text-lg">Tidak ada pesanan aktif.</p>
            <p class="text-on-surface-variant text-sm mt-1">Pesanan baru akan muncul di sini setelah kasir konfirmasi.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($activeOrders as $order)
                @php
                    $borderColor = match($order->status) {
                        'confirmed'  => 'border-secondary',
                        'processing' => 'border-tertiary',
                        'ready'      => 'border-primary',
                        default      => 'border-outline-variant',
                    };
                    $badgeBg = match($order->status) {
                        'confirmed'  => 'bg-secondary-container text-on-secondary-container',
                        'processing' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                        'ready'      => 'bg-primary-fixed text-on-primary-fixed',
                        default      => 'bg-surface-variant text-on-surface-variant',
                    };
                    $badgeLabel = match($order->status) {
                        'confirmed'  => 'Baru Masuk',
                        'processing' => 'Sedang Dimasak',
                        'ready'      => 'Siap Diantar',
                        default      => ucfirst($order->status),
                    };
                @endphp

                <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border-l-4 {{ $borderColor }}">
                    {{-- Header --}}
                    <div class="flex justify-between items-center px-5 py-4 border-b border-surface-container">
                        <div class="flex items-center gap-3">
                            <span class="font-headline font-black text-3xl text-primary">{{ $order->table->table_number }}</span>
                            <div>
                                <p class="text-xs text-on-surface-variant">Antrean</p>
                                <p class="font-bold text-lg leading-none">#{{ $order->queue_number }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeBg }}">{{ $badgeLabel }}</span>
                            <span class="text-xs text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Item list --}}
                    <div class="px-5 py-4 space-y-2">
                        @foreach ($order->orderItems as $item)
                            <div class="flex items-start gap-3">
                                <span class="font-headline font-black text-2xl text-primary w-8 text-right shrink-0 leading-tight">{{ $item->quantity }}</span>
                                <div>
                                    <p class="font-semibold text-sm leading-tight">{{ $item->product->name }}</p>
                                    @if($item->note)
                                        <p class="text-xs text-on-surface-variant italic mt-0.5">{{ $item->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="px-5 pb-4">
                        @if ($order->status === 'confirmed')
                            <form method="POST" action="{{ route('dapur.orders.status', $order) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit"
                                        class="w-full py-3 bg-secondary text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">cooking</span>
                                    Mulai Masak
                                </button>
                            </form>
                        @elseif ($order->status === 'processing')
                            <form method="POST" action="{{ route('dapur.orders.status', $order) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit"
                                        class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">done_all</span>
                                    Siap Diantar
                                </button>
                            </form>
                        @elseif ($order->status === 'ready')
                            <form method="POST" action="{{ route('dapur.orders.status', $order) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                        class="w-full py-3 bg-surface-container text-on-surface-variant font-bold rounded-xl hover:bg-surface-variant active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
                                    Selesai & Diantar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-station>
