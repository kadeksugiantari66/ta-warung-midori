<x-app-layout>
<x-form-card title="Tambah Akun Staf" :back="route('admin.users.index')">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <x-form-field label="Nama" name="name" required>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-error @enderror">
        </x-form-field>

        <x-form-field label="Email" name="email" required>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('email') border-error @enderror">
        </x-form-field>

        <x-form-field label="Peran" name="role" required>
            <select name="role" id="role"
                    class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('role') border-error @enderror">
                <option value="">-- Pilih Peran --</option>
                <option value="admin"  {{ old('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                <option value="kasir"  {{ old('role') === 'kasir'  ? 'selected' : '' }}>Kasir</option>
                <option value="dapur"  {{ old('role') === 'dapur'  ? 'selected' : '' }}>Dapur</option>
            </select>
        </x-form-field>

        <x-form-field label="Kata Sandi" name="password" required>
            <input type="password" name="password" id="password"
                   class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('password') border-error @enderror">
        </x-form-field>

        <x-form-field label="Konfirmasi Kata Sandi" name="password_confirmation">
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
        </x-form-field>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
                Simpan
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-on-surface-variant hover:underline">Batal</a>
        </div>
    </form>
</x-form-card>
</x-app-layout>
