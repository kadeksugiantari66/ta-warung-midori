@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false, 'hint' => null])

<div class="mb-5">
    <label for="{{ $name }}" class="block text-sm font-semibold text-on-surface mb-1.5">
        {{ $label }}@if($required)<span class="text-error ml-1">*</span>@endif
    </label>
    {{ $slot }}
    @if($hint)
        <p class="mt-1 text-xs text-on-surface-variant">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-error font-medium">{{ $message }}</p>
    @enderror
</div>
