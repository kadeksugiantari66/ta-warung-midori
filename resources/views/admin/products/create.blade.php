<x-app-layout>
<x-form-card title="Tambah Menu" :back="route('admin.products.index')">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @php $product = null; @endphp
        @include('admin.products._form')
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 active:scale-95 transition-all text-sm">
                Simpan
            </button>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-on-surface-variant hover:underline">Batal</a>
        </div>
    </form>
</x-form-card>
</x-app-layout>
