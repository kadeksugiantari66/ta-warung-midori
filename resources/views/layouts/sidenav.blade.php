@foreach ($links as $link)
    @php 
        $pattern = $link['active'] ?? str_replace('.index', '.*', $link['route']);
        $active  = request()->routeIs($link['route']) || request()->routeIs($pattern); 
    @endphp
    <a href="{{ route($link['route']) }}"
       @click="sidebarOpen = false"
       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
              {{ $active
                  ? 'bg-secondary-container text-on-secondary-fixed font-semibold'
                  : 'text-on-surface-variant hover:bg-surface-variant' }}">
        <span class="material-symbols-outlined"
              style="font-size:20px; {{ $active ? 'font-variation-settings: \'FILL\' 1' : '' }}">
            {{ $link['icon'] }}
        </span>
        {{ $link['label'] }}
    </a>
@endforeach
