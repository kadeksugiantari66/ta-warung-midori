<x-app-layout>
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Kategori Menu</h2>
        <p class="text-on-surface-variant text-sm mt-1">Kelompokkan menu berdasarkan kategori.</p>
    </div>
</div>

<x-flash/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Tambah --}}
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-6 h-fit">
        <h3 class="font-semibold text-base mb-5">Tambah Kategori</h3>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <x-form-field label="Nama Kategori" name="name" required>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-error @enderror">
            </x-form-field>
            <x-form-field label="Deskripsi" name="description">
                <input type="text" name="description" value="{{ old('description') }}"
                       class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
            </x-form-field>
            <button type="submit"
                    class="w-full bg-primary text-white font-semibold py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
                Tambah Kategori
            </button>
        </form>
    </div>

    {{-- Daftar Kategori --}}
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden" x-data="{ editId: null, editName: '', editDesc: '' }">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nama</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Deskripsi</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Menu</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @forelse ($categories as $category)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-sm">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $category->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $category->products_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <button @click="editId = {{ $category->id }}; editName = '{{ addslashes($category->name) }}'; editDesc = '{{ addslashes($category->description ?? '') }}'"
                                        class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                      data-confirm="Hapus kategori {{ $category->name }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-error hover:bg-error-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-surface-container">{{ $categories->links() }}</div>

        {{-- Modal Edit --}}
        <div x-show="editId !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-xl p-8 w-full max-w-md mx-4" @click.outside="editId = null">
                <h3 class="font-headline font-bold text-xl text-primary mb-6">Edit Kategori</h3>
                <form method="POST" :action="`/admin/categories/${editId}`">
                    @csrf <input type="hidden" name="_method" value="PUT">
                    <x-form-field label="Nama" name="name" required>
                        <input type="text" name="name" x-model="editName"
                               class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    </x-form-field>
                    <x-form-field label="Deskripsi" name="description">
                        <input type="text" name="description" x-model="editDesc"
                               class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    </x-form-field>
                    <div class="flex gap-3 mt-2">
                        <button type="submit" class="bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 text-sm">Simpan</button>
                        <button type="button" @click="editId = null" class="bg-surface-container text-on-surface-variant font-semibold px-6 py-2.5 rounded-xl hover:bg-surface-variant text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
