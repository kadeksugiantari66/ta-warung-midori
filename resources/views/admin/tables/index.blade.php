<x-app-layout>
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Meja & QR Code</h2>
        <p class="text-on-surface-variant text-sm mt-1">Kelola meja dan generate QR Code pemesanan.</p>
    </div>
</div>

<x-flash/>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ activeQr: null }">
    {{-- Form Tambah --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6 h-fit">
        <h3 class="font-semibold text-base mb-5">Tambah Meja</h3>
        <form method="POST" action="{{ route('admin.tables.store') }}">
            @csrf
            <x-form-field label="Nomor Meja" name="table_number" required hint="Contoh: A1, B2, VIP-1">
                <input type="text" name="table_number" value="{{ old('table_number') }}"
                       class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('table_number') border-error @enderror">
            </x-form-field>
            <button type="submit"
                    class="w-full bg-primary text-white font-semibold py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">qr_code_2</span>
                Tambah & Generate QR
            </button>
        </form>
    </div>

    {{-- Grid Meja --}}
    <div class="lg:col-span-3">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @forelse ($tables as $table)
                <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-5 text-center">
                    <div class="flex justify-between items-start mb-3">
                        <span class="font-headline font-black text-xl text-primary">{{ $table->table_number }}</span>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $table->status === 'available' ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-error-container text-on-error-container' }}">
                            {{ $table->status === 'available' ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>

                    @if ($table->qr_code_path)
                        {{-- QR clickable → popup --}}
                        <button @click="activeQr = {
                                    src: '{{ Storage::url($table->qr_code_path) }}',
                                    table: '{{ $table->table_number }}',
                                    download: '{{ Storage::url($table->qr_code_path) }}',
                                    url: '{{ route('order.menu', $table) }}',
                                    printUrl: '{{ route('admin.tables.print-qr', $table) }}'
                                }"
                                class="block mx-auto my-3 hover:scale-105 transition-transform cursor-zoom-in">
                            <img src="{{ Storage::url($table->qr_code_path) }}"
                                 alt="QR Meja {{ $table->table_number }}"
                                 class="w-28 h-28 mx-auto rounded-xl">
                        </button>
                        <a href="{{ Storage::url($table->qr_code_path) }}"
                           download="qr_meja_{{ $table->table_number }}.svg"
                           class="text-xs font-semibold text-secondary hover:underline block mb-3">
                            Download QR
                        </a>
                    @else
                        <div class="w-28 h-28 mx-auto my-3 bg-surface-container rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-on-surface-variant opacity-30">qr_code</span>
                        </div>
                    @endif

                    <div class="flex gap-2 justify-center">
                        <form method="POST" action="{{ route('admin.tables.regenerate-qr', $table) }}">
                            @csrf
                            <button type="submit"
                                    class="text-xs px-3 py-1.5 bg-secondary-container text-on-secondary-fixed font-semibold rounded-lg hover:opacity-90 transition-all">
                                Buat Ulang QR
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.tables.destroy', $table) }}"
                              data-confirm="Hapus meja {{ $table->table_number }}?">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs px-3 py-1.5 bg-error-container text-on-error-container font-semibold rounded-lg hover:opacity-90 transition-all">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">table_restaurant</span>
                    Belum ada meja.
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $tables->links() }}</div>
    </div>

    {{-- QR Popup Modal --}}
    <div x-show="activeQr" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="activeQr = null"
         @keydown.escape.window="activeQr = null">

        {{-- Backdrop blur --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md"></div>

        {{-- Modal card --}}
        <div class="relative bg-surface-container-lowest rounded-[2rem] shadow-2xl p-8 w-full max-w-xs text-center"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">

            {{-- Close --}}
            <button @click="activeQr = null"
                    class="absolute top-4 right-4 p-2 hover:bg-surface-container rounded-xl transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined text-base">close</span>
            </button>

            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">QR Code</p>
            <p class="font-headline font-black text-2xl text-primary mb-5" x-text="'Meja ' + activeQr?.table"></p>

            {{-- QR besar --}}
            <div class="bg-white rounded-2xl p-4 mb-5 inline-block shadow-sm">
                <img :src="activeQr?.src" :alt="'QR Meja ' + activeQr?.table" class="w-56 h-56">
            </div>

            {{-- URL meja --}}
            <p class="text-xs text-on-surface-variant mb-5 break-all" x-text="activeQr?.url"></p>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a :href="activeQr?.download"
                   :download="'qr_meja_' + activeQr?.table + '.svg'"
                   class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-secondary-container text-on-secondary-fixed text-sm font-semibold rounded-xl hover:opacity-90 transition-all">
                    <span class="material-symbols-outlined text-base">download</span>
                    Download
                </a>
                <a :href="activeQr?.printUrl" target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-all">
                    <span class="material-symbols-outlined text-base">print</span>
                    Cetak
                </a>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
