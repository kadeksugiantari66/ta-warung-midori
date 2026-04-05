<x-app-layout>
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.products.index') }}" class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-xl transition-colors">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <h2 class="font-headline text-3xl font-black text-primary">Detail Menu</h2>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}"
           class="flex items-center gap-2 bg-primary text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-base">edit</span> Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Produk --}}
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-8">
        <div class="flex gap-6 mb-6">
            @if ($product->image)
                <img src="{{ Storage::url($product->image) }}" class="w-32 h-32 rounded-2xl object-cover shrink-0">
            @else
                <div class="w-32 h-32 rounded-2xl bg-surface-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-30">image</span>
                </div>
            @endif
            <div>
                <span class="text-xs font-semibold text-secondary bg-secondary-container px-2.5 py-1 rounded-full">
                    {{ $product->category->name }}
                </span>
                <h3 class="font-headline text-2xl font-bold text-primary mt-2">{{ $product->name }}</h3>
                <p class="text-2xl font-black text-secondary mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <span class="inline-flex items-center mt-2 px-2.5 py-1 text-xs font-bold rounded-full
                    {{ $product->is_available ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-error-container text-on-error-container' }}">
                    {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                </span>
            </div>
        </div>

        @if ($product->description)
            <div class="border-t border-surface-container pt-5">
                <p class="text-sm font-semibold text-on-surface-variant mb-1">Deskripsi</p>
                <p class="text-sm text-on-surface">{{ $product->description }}</p>
            </div>
        @endif

        {{-- Toggle ketersediaan --}}
        <div class="border-t border-surface-container pt-5 mt-5">
            <form method="POST" action="{{ route('admin.products.toggle-availability', $product) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all
                               {{ $product->is_available
                                   ? 'bg-error-container text-on-error-container hover:opacity-90'
                                   : 'bg-primary-fixed text-on-primary-fixed hover:opacity-90' }}">
                    <span class="material-symbols-outlined text-base">{{ $product->is_available ? 'remove_shopping_cart' : 'add_shopping_cart' }}</span>
                    {{ $product->is_available ? 'Tandai Habis' : 'Tandai Tersedia' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik & Ulasan --}}
    <div class="space-y-5">
        <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6">
            <h4 class="font-semibold text-sm text-on-surface-variant mb-4">Statistik</h4>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-on-surface-variant">Total dipesan</span>
                    <span class="font-bold">{{ $product->orderItems->count() }}×</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-on-surface-variant">Rating rata-rata</span>
                    <span class="font-bold text-secondary">
                        @if($product->reviews->count())
                            ★ {{ number_format($product->reviews->avg('rating'), 1) }}
                            <span class="text-on-surface-variant font-normal">({{ $product->reviews->count() }})</span>
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Ulasan terbaru --}}
        <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6">
            <h4 class="font-semibold text-sm text-on-surface-variant mb-4">Ulasan Terbaru</h4>
            @forelse ($product->reviews->take(5) as $review)
                <div class="mb-3 pb-3 border-b border-surface-container last:border-0 last:mb-0 last:pb-0">
                    <div class="flex text-yellow-400 text-sm mb-1">
                        @for($i=1;$i<=5;$i++)<span>{{ $i<=$review->rating?'★':'☆' }}</span>@endfor
                    </div>
                    <p class="text-xs text-on-surface-variant">{{ $review->comment ?? '—' }}</p>
                    <p class="text-xs text-outline mt-1">{{ $review->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="text-sm text-on-surface-variant">Belum ada ulasan.</p>
            @endforelse
        </div>
    </div>
</div>
</x-app-layout>
