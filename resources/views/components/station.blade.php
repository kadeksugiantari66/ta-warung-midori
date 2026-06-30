@props(['title' => 'Warung Midori', 'pollUrl' => null, 'pollHash' => ''])
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Warung Midori' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-container": "#2d5a27",
                "primary-fixed": "#bcf0ae", "on-primary-fixed": "#002201",
                "secondary": "#506600", "secondary-container": "#caee5d",
                "secondary-fixed": "#ccf05f", "on-secondary-fixed": "#161e00",
                "on-secondary-container": "#546b00",
                "tertiary": "#553112", "tertiary-fixed": "#ffdcc5", "on-tertiary-fixed": "#301400",
                "error": "#ba1a1a", "error-container": "#ffdad6", "on-error-container": "#93000a",
                "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f2f4f2", "surface-container": "#eceeec",
                "surface-container-high": "#e6e9e7", "surface-variant": "#e1e3e1",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e", "outline-variant": "#c2c9bb",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface-container-low font-body text-on-surface min-h-screen flex flex-col">

    {{-- Header simpel --}}
    <header class="bg-primary text-white px-4 lg:px-6 py-3 lg:py-4 flex justify-between items-center shrink-0 shadow-lg">
        <div class="flex items-center gap-3">
            <span class="font-headline font-black text-lg lg:text-xl">Warung Midori</span>
            <span class="text-primary-fixed/70 text-sm hidden sm:inline">·</span>
            <span class="text-primary-fixed text-sm font-semibold hidden sm:inline">{{ $title ?? '' }}</span>

            {{-- Navigasi terminal (kasir & dapur) --}}
            @auth
                <nav class="hidden sm:flex items-center gap-1 ml-2 pl-2 border-l border-white/20">
                    @if(auth()->user()->isKasir())
                        <a href="{{ route('kasir.dashboard') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kasir.dashboard') ? 'bg-white/15 text-white' : 'text-primary-fixed/80 hover:bg-white/10' }}">Dashboard</a>
                        <a href="{{ route('kasir.orders.index') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kasir.orders.*') ? 'bg-white/15 text-white' : 'text-primary-fixed/80 hover:bg-white/10' }}">Pesanan</a>
                    @elseif(auth()->user()->isDapur())
                        <a href="{{ route('dapur.dashboard') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dapur.dashboard') ? 'bg-white/15 text-white' : 'text-primary-fixed/80 hover:bg-white/10' }}">Dashboard</a>
                    @endif
                </nav>
            @endauth
        </div>
        <div class="flex items-center gap-2 lg:gap-4">
            {{-- Live indicator --}}
            <div class="flex items-center gap-1.5 text-primary-fixed/80 text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                <span class="hidden sm:inline">Live</span>
            </div>
            {{-- User + logout --}}
            <div class="flex items-center gap-2 lg:gap-3">
                <span class="text-sm text-primary-fixed/80 hidden md:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1 text-xs text-primary-fixed/70 hover:text-white transition-colors px-2 lg:px-3 py-1.5 rounded-lg hover:bg-white/10">
                        <span class="material-symbols-outlined text-sm">logout</span>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mx-6 mt-4 p-3 bg-primary-fixed text-on-primary-fixed rounded-xl text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 overflow-y-auto p-4 lg:p-6">
        {{ $slot }}
    </main>

    @stack('scripts')

    @if($pollUrl)
    <script>
    (function() {
        let lastHash = '{{ $pollHash }}';
        setInterval(async () => {
            try {
                const res  = await fetch('{{ $pollUrl }}');
                const data = await res.json();
                if (data.hash && data.hash !== lastHash) {
                    window.location.reload();
                }
            } catch(e) {}
        }, 8000);
    })();
    </script>
    @endif
</body>
</html>
