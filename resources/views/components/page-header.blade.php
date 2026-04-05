@props(['title', 'subtitle' => null])

<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-headline text-3xl font-black text-primary tracking-tight">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-on-surface-variant font-medium">{{ $subtitle }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div>{{ $slot }}</div>
    @endif
</div>
