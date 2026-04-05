<x-app-layout>
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Manajemen Menu</h2>
        <p class="text-on-surface-variant text-sm mt-1">Kelola item menu, harga, dan ketersediaan.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.products.create') }}"
           class="flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-base">add</span> Tambah Menu
        </a>
    </div>
</div>

<x-flash/>

<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-surface-container-low">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Menu</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Kategori</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Harga</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Dipesan</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
            @forelse ($products as $product)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-11 h-11 rounded-xl object-cover shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-on-surface-variant text-base">image</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-sm">{{ $product->name }}</p>
                                <p class="text-xs text-on-surface-variant truncate max-w-[180px]">{{ $product->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $product->category->name }}</td>
                    <td class="px-6 py-4 text-sm font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $product->order_items_count }}×</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full
                            {{ $product->is_available ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-error-container text-on-error-container' }}">
                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-1">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.products.toggle-availability', $product) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors"
                                        title="{{ $product->is_available ? 'Tandai Habis' : 'Tandai Tersedia' }}">
                                    <span class="material-symbols-outlined text-base">{{ $product->is_available ? 'remove_shopping_cart' : 'add_shopping_cart' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                                  data-confirm="Hapus menu {{ $product->name }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-error hover:bg-error-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">Belum ada menu.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-surface-container">{{ $products->links() }}</div>
</div>
</x-app-layout>
