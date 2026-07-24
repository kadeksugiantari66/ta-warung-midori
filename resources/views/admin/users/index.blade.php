<x-app-layout>
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary">Manajemen Staff</h2>
        <p class="text-on-surface-variant text-sm mt-1">Kelola akun dan hak akses staff.</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all">
        <span class="material-symbols-outlined text-base">person_add</span> Tambah Staff
    </a>
</div>

<x-flash/>

<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-surface-container-low">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nama</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Peran</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
            @forelse ($users as $user)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-on-primary-container" style="font-size:16px">person</span>
                            </div>
                            <span class="font-semibold text-sm">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php $roleStyle = match($user->role) {
                            'admin' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                            'kasir' => 'bg-secondary-container text-on-secondary-container',
                            'dapur' => 'bg-primary-fixed text-on-primary-fixed',
                        }; @endphp
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $roleStyle }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full
                            {{ $user->is_active ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-error-container text-on-error-container' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end items-center gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors"
                               title="Edit">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            @if ($user->id_staff !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="contents">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-lg transition-colors"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <span class="material-symbols-outlined text-base">{{ $user->is_active ? 'block' : 'check_circle' }}</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="contents"
                                      data-confirm="Hapus akun {{ $user->name }}? Tindakan ini tidak bisa dibatalkan.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-error hover:bg-error-container rounded-lg transition-colors"
                                            title="Hapus">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Belum ada staff.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-surface-container">{{ $users->links() }}</div>
</div>
</x-app-layout>
