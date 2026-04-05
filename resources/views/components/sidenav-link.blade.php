@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
          {{ $active
              ? 'bg-secondary-container text-on-secondary-fixed font-semibold shadow-sm'
              : 'text-on-surface-variant hover:bg-surface-variant' }}">
    <span class="material-symbols-outlined text-[20px]"
          style="{{ $active ? 'font-variation-settings: \'FILL\' 1' : '' }}">
        {{ $icon }}
    </span>
    <span>{{ $slot }}</span>
</a>
