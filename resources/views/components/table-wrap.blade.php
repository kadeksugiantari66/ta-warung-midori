<div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 overflow-hidden">
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
    @isset($pagination)
        <div class="px-4 lg:px-6 py-4 border-t border-surface-container">{{ $pagination }}</div>
    @endisset
</div>
