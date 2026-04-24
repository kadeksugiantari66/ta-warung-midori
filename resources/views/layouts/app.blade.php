<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Warung Midori') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Serif:wght@700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: { extend: {
            colors: {
                "primary":                    "#154212",
                "primary-container":          "#2d5a27",
                "primary-fixed":              "#bcf0ae",
                "primary-fixed-dim":          "#a1d494",
                "on-primary-fixed":           "#002201",
                "inverse-primary":            "#a1d494",
                "secondary":                  "#506600",
                "secondary-container":        "#caee5d",
                "secondary-fixed":            "#ccf05f",
                "secondary-fixed-dim":        "#b1d446",
                "on-secondary-container":     "#546b00",
                "on-secondary-fixed":         "#161e00",
                "on-secondary-fixed-variant": "#3c4d00",
                "tertiary":                   "#553112",
                "tertiary-container":         "#704727",
                "tertiary-fixed":             "#ffdcc5",
                "tertiary-fixed-dim":         "#f4bb92",
                "on-tertiary-fixed":          "#301400",
                "on-tertiary-fixed-variant":  "#653d1e",
                "on-tertiary-container":      "#f0b78f",
                "error":                      "#ba1a1a",
                "error-container":            "#ffdad6",
                "on-error-container":         "#93000a",
                "surface":                    "#f8faf8",
                "surface-dim":                "#d8dad9",
                "surface-bright":             "#f8faf8",
                "surface-variant":            "#e1e3e1",
                "surface-container-lowest":   "#ffffff",
                "surface-container-low":      "#f2f4f2",
                "surface-container":          "#eceeec",
                "surface-container-high":     "#e6e9e7",
                "on-surface":                 "#191c1b",
                "on-surface-variant":         "#42493e",
                "on-background":              "#191c1b",
                "outline":                    "#72796e",
                "outline-variant":            "#c2c9bb",
                "inverse-surface":            "#2e3130",
                "inverse-on-surface":         "#eff1ef",
                "surface-tint":               "#3b6934",
            },
            fontFamily: {
                "headline": ["Noto Serif", "serif"],
                "body":     ["Inter", "sans-serif"],
                "label":    ["Inter", "sans-serif"],
            },
        }},
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface font-body text-on-surface"
      x-data="{ sidebarOpen: false }"
      @keydown.escape="sidebarOpen = false">

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar Overlay (mobile) ──────────────────────────────────── --}}
    <div x-show="sidebarOpen"
         x-cloak
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"></div>

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-surface-container-low flex flex-col py-5 border-r border-outline-variant/20 transition-transform duration-300 ease-in-out
                  lg:static lg:translate-x-0 lg:shrink-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Brand + close (mobile) --}}
        <div class="px-5 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-headline font-bold text-primary leading-tight">Warung Midori</h1>
                <p class="text-xs font-medium text-on-surface-variant opacity-70">
                    {{ match(auth()->user()?->role) {
                        'admin'  => 'Admin Terminal',
                        'kasir'  => 'Kasir Terminal',
                        'dapur'  => 'Dapur Terminal',
                        default  => 'Terminal'
                    } }}
                </p>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg hover:bg-surface-variant text-on-surface-variant">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 space-y-0.5 px-2 overflow-y-auto">
            @auth
                @if(auth()->user()->isAdmin())
                    @include('layouts.sidenav', ['links' => [
                        ['route' => 'admin.dashboard',       'icon' => 'dashboard',        'label' => 'Dashboard'],
                        ['route' => 'admin.products.index',  'icon' => 'restaurant_menu',  'label' => 'Menu'],
                        ['route' => 'admin.categories.index','icon' => 'category',         'label' => 'Kategori'],
                        ['route' => 'admin.tables.index',    'icon' => 'table_restaurant', 'label' => 'Meja & QR'],
                        ['route' => 'admin.reports.daily',   'icon' => 'bar_chart',        'label' => 'Laporan', 'active' => 'admin.reports.*'],
                        ['route' => 'admin.users.index',     'icon' => 'group',            'label' => 'Staf'],
                        ['route' => 'admin.reviews.index',   'icon' => 'star',             'label' => 'Ulasan'],
                    ]])
                @elseif(auth()->user()->isKasir())
                    @include('layouts.sidenav', ['links' => [
                        ['route' => 'kasir.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ]])
                @elseif(auth()->user()->isDapur())
                    @include('layouts.sidenav', ['links' => [
                        ['route' => 'dapur.dashboard', 'icon' => 'soup_kitchen', 'label' => 'Dashboard'],
                    ]])
                @endif
            @endauth
        </nav>

        {{-- User + logout --}}
        @auth
        <div class="px-4 mt-4 space-y-2 border-t border-outline-variant/20 pt-4">
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary-container" style="font-size:18px">person</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-on-surface-variant capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-on-surface-variant hover:bg-surface-variant rounded-xl transition-colors">
                    <span class="material-symbols-outlined" style="font-size:18px">logout</span>
                    Keluar
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Mobile topbar --}}
        <header class="lg:hidden bg-surface-container-lowest border-b border-outline-variant/20 px-4 py-3 flex items-center gap-3 shrink-0">
            <button @click="sidebarOpen = true"
                    class="p-2 rounded-xl hover:bg-surface-container text-on-surface-variant transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="font-headline font-bold text-primary">Warung Midori</span>
        </header>

        {{-- Page heading slot --}}
        @isset($header)
            <div class="hidden lg:block bg-surface-container-lowest border-b border-outline-variant/20 px-8 py-4 shrink-0">
                {{ $header }}
            </div>
        @endisset

        <main class="flex-1 overflow-y-auto bg-surface p-4 lg:p-8" style="view-transition-name: main-content">
            {{ $slot }}
        </main>
    </div>
</div>

<x-confirm-modal/>
@stack('scripts')
</body>
</html>
