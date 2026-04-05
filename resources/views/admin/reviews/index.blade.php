<x-app-layout>
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Ulasan Menu</h2>
        <p class="text-on-surface-variant text-sm mt-1">Pantau feedback pelanggan.</p>
    </div>
</div>
<x-flash/>
<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-surface-container-low">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Menu</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Rating</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Komentar</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Tanggal</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
            @forelse ($reviews as $review)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-sm">{{ $review->product->name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex text-yellow-400 text-sm">
                            @for($i=1;$i<=5;$i++)<span>{{ $i<=$review->rating?'★':'☆' }}</span>@endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant max-w-xs truncate">{{ $review->comment ?? '—' }}</td>
                    <td class="px-6 py-4 text-xs text-on-surface-variant">{{ $review->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline"
                              data-confirm="Hapus ulasan ini?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-error hover:bg-error-container rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Belum ada ulasan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-surface-container">{{ $reviews->links() }}</div>
</div>
</x-app-layout>
