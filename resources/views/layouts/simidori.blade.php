<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Serif:wght@700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "tertiary-container": "#704727",
                    "primary-container": "#2d5a27",
                    "secondary-fixed-dim": "#b1d446",
                    "surface-container-high": "#e6e9e7",
                    "on-tertiary-fixed-variant": "#653d1e",
                    "surface-container": "#eceeec",
                    "primary": "#154212",
                    "error-container": "#ffdad6",
                    "on-background": "#191c1b",
                    "secondary-container": "#caee5d",
                    "on-tertiary-container": "#f0b78f",
                    "on-error": "#ffffff",
                    "secondary-fixed": "#ccf05f",
                    "on-tertiary-fixed": "#301400",
                    "surface-container-low": "#f2f4f2",
                    "outline": "#72796e",
                    "secondary": "#506600",
                    "on-secondary-fixed": "#161e00",
                    "tertiary": "#553112",
                    "primary-fixed": "#bcf0ae",
                    "error": "#ba1a1a",
                    "on-primary-container": "#9dd090",
                    "inverse-primary": "#a1d494",
                    "surface-container-lowest": "#ffffff",
                    "tertiary-fixed-dim": "#f4bb92",
                    "background": "#f8faf8",
                    "on-surface": "#191c1b",
                    "inverse-surface": "#2e3130",
                    "tertiary-fixed": "#ffdcc5",
                    "on-error-container": "#93000a",
                    "surface-variant": "#e1e3e1",
                    "surface-bright": "#f8faf8",
                    "on-secondary-fixed-variant": "#3c4d00",
                    "on-secondary-container": "#546b00",
                    "inverse-on-surface": "#eff1ef",
                    "on-tertiary": "#ffffff",
                    "outline-variant": "#c2c9bb",
                    "surface-dim": "#d8dad9",
                    "surface": "#f8faf8",
                    "surface-tint": "#3b6934",
                    "on-primary-fixed": "#002201",
                    "on-secondary": "#ffffff",
                    "on-surface-variant": "#42493e",
                    "primary-fixed-dim": "#a1d494",
                },
                fontFamily: {
                    "headline": ["Noto Serif"],
                    "body": ["Inter"],
                    "label": ["Inter"],
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px",
                },
            },
        },
    }
    </script>

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-surface font-body text-on-surface flex h-screen overflow-hidden">

{{-- Sidebar --}}
<aside class="h-screen w-64 bg-surface-container-low flex flex-col py-6 border-r border-outline-variant/10 shrink-0">
    <div class="px-6 mb-8">
        <h1 class="text-xl font-headline font-bold text-primary">Warung Midori</h1>
        <p class="text-xs font-medium text-on-surface-variant opacity-70">
            {{ match(auth()->user()->role) {
                'admin'  => 'Admin Terminal',
                'kasir'  => 'Kasir Terminal',
                'dapur'  => 'Dapur Terminal',
                default  => 'Terminal'
            } }}
        </p>
    </div>

    <nav class="flex-1 space-y-1 px-2">
        @if(auth()->user()->isAdmin())
            <x-sidenav-link href="{{ route('admin.dashboard') }}" icon="dashboard" :active="request()->routeIs('admin.dashboard')">Dashboard</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.products.index') }}" icon="restaurant_menu" :active="request()->routeIs('admin.products.*')">Menu</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.categories.index') }}" icon="category" :active="request()->routeIs('admin.categories.*')">Kategori</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.tables.index') }}" icon="table_restaurant" :active="request()->routeIs('admin.tables.*')">Meja & QR</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.reports.daily') }}" icon="bar_chart" :active="request()->routeIs('admin.reports.*')">Laporan</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.users.index') }}" icon="group" :active="request()->routeIs('admin.users.*')">Staf</x-sidenav-link>
            <x-sidenav-link href="{{ route('admin.reviews.index') }}" icon="star" :active="request()->routeIs('admin.reviews.*')">Ulasan</x-sidenav-link>
        @elseif(auth()->user()->isKasir())
            <x-sidenav-link href="{{ route('kasir.dashboard') }}" icon="dashboard" :active="request()->routeIs('kasir.dashboard')">Dashboard</x-sidenav-link>
        @elseif(auth()->user()->isDapur())
            <x-sidenav-link href="{{ route('dapur.dashboard') }}" icon="soup_kitchen" :active="request()->routeIs('dapur.dashboard')">Dashboard</x-sidenav-link>
        @endif
    </nav>

    {{-- User info + logout --}}
    <div class="px-4 mt-auto space-y-3">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-primary-container text-base">person</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold truncate leading-none">{{ auth()->user()->name }}</p>
                <p class="text-xs text-on-surface-variant capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-on-surface-variant hover:bg-surface-variant rounded-xl transition-colors">
                <span class="material-symbols-outlined text-base">logout</span>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<main class="flex-1 overflow-y-auto bg-surface p-8">
    {{ $slot }}
</main>

@stack('scripts')
</body>
</html>
