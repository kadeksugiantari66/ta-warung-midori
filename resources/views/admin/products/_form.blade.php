{{-- Partial form: dipakai di create & edit --}}

<x-form-field label="Kategori" name="category_id" required>
    <select name="category_id" id="category_id"
            class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('category_id') border-error @enderror">
        <option value="">-- Pilih Kategori --</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</x-form-field>

<x-form-field label="Nama Menu" name="name" required>
    <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
           class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-error @enderror">
</x-form-field>

<x-form-field label="Deskripsi" name="description">
    <textarea name="description" id="description" rows="3"
              class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">{{ old('description', $product->description ?? '') }}</textarea>
</x-form-field>

<x-form-field label="Harga (Rp)" name="price" required>
    <input type="number" name="price" id="price" value="{{ old('price', $product->price ?? '') }}" min="0" step="500"
           class="w-full border-outline-variant rounded-xl bg-surface-container-low px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary @error('price') border-error @enderror">
</x-form-field>

<x-form-field label="Foto Menu" name="image" hint="Format JPG/PNG, maks 2MB.">
    @if (!empty($product->image))
        <img src="{{ Storage::url($product->image) }}" class="w-24 h-24 object-cover rounded-xl mb-3">
    @endif
    <input type="file" name="image" id="image" accept="image/*"
           class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-secondary-container file:text-on-secondary-fixed file:font-semibold hover:file:opacity-90">
</x-form-field>

<div class="flex items-center gap-3 mb-6 p-4 bg-surface-container-low rounded-xl">
    <input type="checkbox" name="is_available" id="is_available" value="1"
           {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}
           class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4">
    <label for="is_available" class="text-sm font-medium">Tersedia untuk dipesan</label>
</div>
