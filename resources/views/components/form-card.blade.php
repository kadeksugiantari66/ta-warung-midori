@props(['title', 'back' => null, 'backLabel' => '← Kembali'])

<div class="flex items-center gap-4 mb-8">
    @if($back)
        <a href="{{ $back }}" class="p-2 text-on-surface-variant hover:bg-surface-variant rounded-xl transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    @endif
    <h2 class="font-headline text-3xl font-black text-primary">{{ $title }}</h2>
</div>

<div class="max-w-2xl">
    <div class="bg-surface-container-lowest rounded-[1.5rem] shadow-[0_4px_24px_rgba(21,66,18,0.06)] border border-outline-variant/10 p-8">
        {{ $slot }}
    </div>
</div>
