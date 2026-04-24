<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Warung Midori – Meja {{ $table->table_number }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-container": "#2d5a27", "primary-fixed": "#bcf0ae",
                "on-primary-fixed": "#002201", "secondary": "#506600", "secondary-container": "#caee5d",
                "secondary-fixed": "#ccf05f", "on-secondary-fixed": "#161e00", "on-secondary-container": "#546b00",
                "tertiary": "#553112", "tertiary-fixed": "#ffdcc5", "on-tertiary-fixed": "#301400",
                "on-tertiary-fixed-variant": "#653d1e", "error": "#ba1a1a", "error-container": "#ffdad6",
                "on-error-container": "#93000a", "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f2f4f2", "surface-container": "#eceeec",
                "surface-variant": "#e1e3e1", "on-surface": "#191c1b", "on-surface-variant": "#42493e",
                "outline-variant": "#c2c9bb", "background": "#f8faf8",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
        body { min-height: max(884px, 100dvh); }
    </style>
</head>
<body class="bg-background text-on-background font-body antialiased" x-data="orderApp()" x-init="init()">

{{-- Redirect otomatis jika meja occupied --}}
@isset($redirect)
<script>window.location.href = "{{ $redirect }}";</script>
@endisset

{{-- ── Header ──────────────────────────────────────────────────────────── --}}
<header class="fixed top-0 w-full z-50 bg-[#f8faf8]/80 backdrop-blur-md shadow-[0_4px_24px_rgba(21,66,18,0.08)]">
    <div class="flex justify-between items-center px-5 py-4 max-w-screen-xl mx-auto">
        <div>
            <span class="text-2xl font-headline font-black text-primary">Warung Midori</span>
            <span class="block text-[10px] font-bold tracking-widest uppercase text-secondary">Meja {{ $table->table_number }}</span>
        </div>
        <div class="flex items-center gap-2">
            {{-- Search toggle --}}
            <button @click="showSearch = !showSearch" class="p-2 rounded-full hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-primary">search</span>
            </button>
            {{-- Cart button --}}
            <button @click="showCart = true" class="p-2 rounded-full hover:bg-surface-container-low transition-colors relative">
                <span class="material-symbols-outlined text-primary">shopping_cart</span>
                <span x-show="totalItems > 0" x-text="totalItems"
                      class="absolute top-0 right-0 text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full"
                      style="background:#ccf05f; color:#161e00"></span>
            </button>
        </div>
    </div>
    {{-- Search bar --}}
    <div x-show="showSearch" x-cloak class="px-5 pb-3 max-w-screen-xl mx-auto">
        <div class="relative">
            <input x-model="search" type="text" placeholder="Cari menu favoritmu..."
                   autofocus
                   class="w-full rounded-2xl py-3 pl-11 pr-10 text-sm outline-none transition-all"
                   style="background:#eceeec; border:none; box-shadow:0 0 0 2px transparent"
                   @focus="$el.style.boxShadow='0 0 0 2px #ccf05f'"
                   @blur="$el.style.boxShadow='0 0 0 2px transparent'">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-base" style="color:#72796e">search</span>
            <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2">
                <span class="material-symbols-outlined text-base" style="color:#72796e">close</span>
            </button>
        </div>
    </div>
</header>

{{-- ── Main ─────────────────────────────────────────────────────────────── --}}
<main class="pt-24 px-4 max-w-screen-xl mx-auto md:px-8" style="padding-bottom: 9rem">

    {{-- Greeting --}}
    <section class="mb-8" x-show="!search">
        <h1 class="font-headline text-3xl font-bold text-primary leading-tight mb-1">Mau pesan apa?</h1>
        <p class="text-on-surface-variant text-sm">Pilih menu, masukkan ke keranjang, selesai.</p>
    </section>

    {{-- Kategori tabs --}}
    <section class="mb-8 -mx-5 px-5 overflow-x-auto hide-scrollbar flex space-x-3" x-show="!search">
        <button @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-variant'"
                class="flex-shrink-0 px-6 py-2.5 rounded-full font-semibold text-sm transition-colors">
            Semua
        </button>
        @foreach ($categories as $category)
            <button @click="activeCategory = '{{ $category->id }}'"
                    :class="activeCategory === '{{ $category->id }}' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-variant'"
                    class="flex-shrink-0 px-6 py-2.5 rounded-full font-semibold text-sm transition-colors">
                {{ $category->name }}
            </button>
        @endforeach
    </section>

    {{-- Search results --}}
    <section x-show="search" x-cloak class="mb-4">
        <p class="text-sm text-on-surface-variant mb-4">
            Hasil: "<span x-text="search" class="font-semibold" style="color:#154212"></span>"
            — <span x-text="searchResults.length" class="font-semibold"></span> menu ditemukan
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="p in searchResults" :key="p.id">
                <div class="bg-[#ffffff] rounded-[1.5rem] p-4 shadow-[0_2px_12px_rgba(21,66,18,0.06)] flex gap-4 items-center min-h-[100px]">
                    <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-[#eceeec] flex items-center justify-center">
                        <template x-if="p.image">
                            <img :src="p.image" :alt="p.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!p.image">
                            <span class="material-symbols-outlined text-2xl" style="color:#c2c9bb">restaurant</span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-headline font-bold text-[#191c1b] text-sm leading-tight" x-text="p.name"></h3>
                        <p class="text-xs mt-0.5 line-clamp-2" style="color:#72796e" x-text="p.description"></p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="font-black text-sm" style="color:#154212" x-text="'Rp ' + formatRp(p.price)"></span>
                            <div x-show="getQty(p.id) > 0" class="flex items-center gap-1.5">
                                <button @click="decrease(p.id)"
                                        class="w-7 h-7 rounded-full font-bold flex items-center justify-center text-sm"
                                        style="background:#eceeec; color:#191c1b">−</button>
                                <span x-text="getQty(p.id)" class="font-bold text-sm w-5 text-center"></span>
                                <button @click="increase(p.id, p.name, p.price)"
                                        class="w-7 h-7 rounded-full font-bold flex items-center justify-center text-sm"
                                        style="background:#ccf05f; color:#161e00">+</button>
                            </div>
                            <button x-show="getQty(p.id) === 0"
                                    @click="increase(p.id, p.name, p.price)"
                                    class="w-8 h-8 rounded-full font-bold flex items-center justify-center"
                                    style="background:#ccf05f; color:#161e00">
                                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">add</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="searchResults.length === 0">
                <div class="col-span-3 text-center py-12 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">search_off</span>
                    <p class="text-sm">Tidak ada menu yang cocok.</p>
                </div>
            </template>
        </div>
    </section>

    {{-- Menu Sections --}}
    <div class="space-y-12">
        @foreach ($categories as $category)
            <section x-show="(activeCategory === 'all' || activeCategory === '{{ $category->id }}') && !search">
                <div class="flex justify-between items-end mb-6">
                    <h2 class="font-headline text-2xl font-bold text-primary">{{ $category->name }}</h2>
                    @if($category->description)
                        <span class="text-secondary text-xs font-bold tracking-widest uppercase">{{ $category->description }}</span>
                    @endif
                </div>

                @php $products = $category->products; $first = $products->first(); @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($products as $i => $product)
                        {{-- Semua card format sama: horizontal dengan gambar kiri --}}
                        <div class="bg-[#ffffff] rounded-[1.5rem] p-4 shadow-[0_2px_12px_rgba(21,66,18,0.06)] flex gap-4 items-center min-h-[100px]">
                            {{-- Gambar --}}
                            <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-[#eceeec]">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl" style="color:#c2c9bb">restaurant</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-headline font-bold text-[#191c1b] text-sm leading-tight">{{ $product->name }}</h3>
                                        @if($product->description)
                                            <p class="text-[#72796e] text-xs mt-0.5 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                                        @endif
                                        @if($product->reviews->count() > 0)
                                            <p class="text-xs mt-1" style="color:#ccf05f">
                                                ★ {{ number_format($product->reviews->avg('rating'), 1) }}
                                                <span style="color:#72796e">({{ $product->reviews->count() }})</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="font-black text-sm" style="color:#154212">Rp {{ number_format($product->price, 0, ',', '.') }}</span>

                                    {{-- Qty control --}}
                                    <div x-show="getQty({{ $product->id }}) > 0" class="flex items-center gap-1.5">
                                        <button @click="decrease({{ $product->id }})"
                                                class="w-7 h-7 rounded-full font-bold flex items-center justify-center text-sm transition-colors"
                                                style="background:#eceeec; color:#191c1b">−</button>
                                        <span x-text="getQty({{ $product->id }})" class="font-bold text-sm w-5 text-center"></span>
                                        <button @click="increase({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                                                class="w-7 h-7 rounded-full font-bold flex items-center justify-center text-sm transition-all"
                                                style="background:#ccf05f; color:#161e00">+</button>
                                    </div>
                                    <button x-show="getQty({{ $product->id }}) === 0"
                                            @click="increase({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                                            class="w-8 h-8 rounded-full font-bold flex items-center justify-center transition-all hover:scale-110 active:scale-95"
                                            style="background:#ccf05f; color:#161e00">
                                        <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- Search results sudah di atas --}}
    </div>

    <footer class="mt-20 text-center pb-4 border-t border-outline-variant/15 pt-8">
        <span class="text-2xl font-headline font-black text-primary block mb-1">Warung Midori</span>
        <p class="text-xs text-on-surface-variant uppercase tracking-[0.2em]">Warung Midori • Bangli, Bali</p>
    </footer>
</main>

{{-- ── Floating Cart Button ────────────────────────────────────────────── --}}
<div x-show="totalItems > 0 && !showCart" x-cloak
     class="fixed left-4 right-4 z-30 md:bottom-6 md:left-auto md:right-6 md:w-auto" style="bottom: calc(7rem + env(safe-area-inset-bottom))">
    <button @click="showCart = true"
            class="w-full md:w-auto flex items-center justify-between md:justify-start gap-3 px-5 py-3.5 rounded-full shadow-[0_4px_24px_rgba(21,66,18,0.3)] active:scale-95 transition-transform"
            style="background:#154212; color:white">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">shopping_basket</span>
            <span class="font-bold text-sm" x-text="totalItems + ' Item'"></span>
        </div>
        <span class="font-black text-sm" x-text="'Rp ' + formatRp(totalPrice)"></span>
        <span class="material-symbols-outlined text-base opacity-70">chevron_right</span>
    </button>
</div>

{{-- ── Bottom Nav (mobile) ─────────────────────────────────────────────── --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-40 border-t"
     style="background:rgba(248,250,248,0.95); backdrop-filter:blur(16px); border-color:#c2c9bb30">
    <div class="flex justify-around items-center px-2 pb-safe pt-2" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom))">

        {{-- Home --}}
        <button @click="activeTab = 'home'; search = ''; showSearch = false; scrollToTop()"
                class="flex flex-col items-center p-2 min-w-[60px] transition-colors"
                :style="activeTab === 'home' ? 'color:#154212' : 'color:#72796e'">
            <span class="material-symbols-outlined"
                  :style="activeTab === 'home' ? 'font-variation-settings:\'FILL\' 1' : ''">home</span>
            <span class="text-[10px] font-semibold uppercase tracking-widest mt-0.5">Home</span>
        </button>

        {{-- Menu --}}
        <button @click="activeTab = 'menu'; activeCategory = 'all'; search = ''; showSearch = false"
                class="flex flex-col items-center rounded-2xl p-3 -translate-y-2 shadow-lg transition-all min-w-[60px]"
                :style="activeTab === 'menu' ? 'background:#154212; color:white' : 'background:#eceeec; color:#42493e'">
            <span class="material-symbols-outlined"
                  :style="activeTab === 'menu' ? 'font-variation-settings:\'FILL\' 1' : ''">menu_book</span>
            <span class="text-[10px] font-semibold uppercase tracking-widest mt-0.5">Menu</span>
        </button>

        {{-- Cart --}}
        <button @click="activeTab = 'cart'; showCart = true"
                class="flex flex-col items-center p-2 min-w-[60px] relative transition-colors"
                :style="activeTab === 'cart' ? 'color:#154212' : 'color:#72796e'">
            <span class="material-symbols-outlined"
                  :style="totalItems > 0 ? 'font-variation-settings:\'FILL\' 1' : ''">shopping_basket</span>
            <span x-show="totalItems > 0" x-text="totalItems"
                  class="absolute top-1 right-2 text-[9px] font-black w-4 h-4 flex items-center justify-center rounded-full"
                  style="background:#ccf05f; color:#161e00"></span>
            <span class="text-[10px] font-semibold uppercase tracking-widest mt-0.5">Cart</span>
        </button>

        {{-- Info --}}
        <button @click="activeTab = 'info'; showInfo = true"
                class="flex flex-col items-center p-2 min-w-[60px] transition-colors"
                :style="activeTab === 'info' ? 'color:#154212' : 'color:#72796e'">
            <span class="material-symbols-outlined"
                  :style="activeTab === 'info' ? 'font-variation-settings:\'FILL\' 1' : ''">info</span>
            <span class="text-[10px] font-semibold uppercase tracking-widest mt-0.5">Info</span>
        </button>
    </div>
</nav>

{{-- ── Info Panel ───────────────────────────────────────────────────────── --}}
<div x-show="showInfo" x-cloak class="fixed inset-0 z-50 flex items-end md:items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="showInfo = false; activeTab = 'menu'"></div>
    <div class="relative w-full max-w-md rounded-t-[2rem] md:rounded-[2rem] shadow-2xl overflow-hidden"
         style="background:#ffffff"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-y-full opacity-0"
         x-transition:enter-end="transform translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="transform translate-y-0 opacity-100"
         x-transition:leave-end="transform translate-y-full opacity-0">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1 md:hidden">
            <div class="w-10 h-1 rounded-full" style="background:#e1e3e1"></div>
        </div>

        <div class="px-6 py-5">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-headline font-bold text-xl" style="color:#154212">Info Warung</h3>
                <button @click="showInfo = false; activeTab = 'menu'" class="p-2 rounded-xl" style="background:#f2f4f2">
                    <span class="material-symbols-outlined text-base" style="color:#42493e">close</span>
                </button>
            </div>

            {{-- Meja --}}
            <div class="flex items-center gap-4 p-4 rounded-2xl mb-3" style="background:#f2f4f2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#bcf0ae">
                    <span class="material-symbols-outlined" style="color:#154212; font-variation-settings:'FILL' 1">table_restaurant</span>
                </div>
                <div>
                    <p class="text-xs font-semibold" style="color:#72796e">Meja Anda</p>
                    <p class="font-headline font-black text-2xl" style="color:#154212">{{ $table->table_number }}</p>
                </div>
            </div>

            {{-- Info restoran --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f2f4f2">
                    <span class="material-symbols-outlined text-base" style="color:#506600; font-variation-settings:'FILL' 1">location_on</span>
                    <div>
                        <p class="text-xs font-semibold" style="color:#72796e">Lokasi</p>
                        <p class="text-sm font-medium" style="color:#191c1b">Jl. Merdeka No. 108B, Bangli, Bali</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f2f4f2">
                    <span class="material-symbols-outlined text-base" style="color:#506600; font-variation-settings:'FILL' 1">schedule</span>
                    <div>
                        <p class="text-xs font-semibold" style="color:#72796e">Jam Buka</p>
                        <p class="text-sm font-medium" style="color:#191c1b">08.00 – 22.00 WITA</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f2f4f2">
                    <span class="material-symbols-outlined text-base" style="color:#506600; font-variation-settings:'FILL' 1">payments</span>
                    <div>
                        <p class="text-xs font-semibold" style="color:#72796e">Pembayaran</p>
                        <p class="text-sm font-medium" style="color:#191c1b">Digital (QRIS, Transfer via Midtrans)</p>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs mt-5" style="color:#72796e">Warung Midori · Bangli, Bali</p>
        </div>
    </div>
</div>

{{-- ── Cart Drawer ──────────────────────────────────────────────────────── --}}
<div x-show="showCart" x-cloak class="fixed inset-0 z-50 flex justify-end">
    <div class="absolute inset-0 bg-black/40" @click="showCart = false; activeTab = 'menu'"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-md h-full flex flex-col shadow-2xl">

        <div class="flex justify-between items-center px-6 py-5 border-b border-outline-variant/20">
            <h3 class="font-headline font-bold text-xl text-primary">Pesanan Saya</h3>
            <button @click="showCart = false; activeTab = 'menu'" class="p-2 hover:bg-surface-container rounded-xl transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
            <template x-if="cart.length === 0">
                <div class="text-center py-16 text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">shopping_basket</span>
                    <p class="text-sm">Keranjang masih kosong.</p>
                </div>
            </template>
            <template x-for="item in cart" :key="item.product_id">
                <div class="flex justify-between items-start gap-3 p-4 bg-surface-container-low rounded-2xl">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-on-surface" x-text="item.name"></p>
                        <p class="text-xs text-secondary font-bold mt-0.5" x-text="'Rp ' + formatRp(item.price)"></p>
                        <input type="text" x-model="item.note" placeholder="Catatan (opsional)"
                               class="mt-2 w-full text-xs border-outline-variant rounded-xl bg-surface-container px-3 py-1.5 focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="decrease(item.product_id)"
                                class="w-8 h-8 rounded-full bg-[#eceeec] font-bold flex items-center justify-center hover:bg-[#e1e3e1] transition-colors">−</button>
                        <span x-text="item.quantity" class="font-bold text-sm w-5 text-center"></span>
                        <button @click="increase(item.product_id, item.name, item.price)"
                                class="w-8 h-8 rounded-full bg-[#ccf05f] text-[#161e00] font-bold flex items-center justify-center hover:opacity-90 transition-all">+</button>
                    </div>
                </div>
            </template>
        </div>

        <div class="border-t border-outline-variant/20 px-6 py-5 space-y-4">
            {{-- Total --}}
            <div class="flex justify-between items-center">
                <span class="text-sm text-on-surface-variant font-medium">Total</span>
                <span class="font-headline font-black text-xl text-primary" x-text="'Rp ' + formatRp(totalPrice)"></span>
            </div>

            {{-- Submit --}}
            <form method="POST" action="{{ route('order.store', $table) }}" id="orderForm">
                @csrf
                <div id="orderItems"></div>
                
                {{-- Pemilihan Metode Pembayaran (FR-04.1) --}}
                <div class="mb-5 space-y-3">
                    <p class="text-sm text-on-surface-variant font-semibold">Pilih Metode Pembayaran</p>
                    
                    <label class="flex items-center gap-3 p-3 border border-outline-variant/30 rounded-xl cursor-pointer hover:bg-surface-container-highest transition-colors"
                           :class="paymentMethod === 'tunai' ? 'ring-2 ring-primary bg-primary/5 border-transparent' : ''">
                        <input type="radio" name="payment_method" value="tunai" x-model="paymentMethod" class="text-primary focus:ring-primary w-4 h-4">
                        <div class="flex-1">
                            <p class="font-bold text-sm text-on-surface">Tunai di Kasir</p>
                            <p class="text-xs text-on-surface-variant">Bayar di kasir.</p>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant" :class="paymentMethod === 'tunai' ? 'text-primary' : ''">payments</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 border border-outline-variant/30 rounded-xl cursor-pointer hover:bg-surface-container-highest transition-colors"
                           :class="paymentMethod === 'midtrans' ? 'ring-2 ring-primary bg-primary/5 border-transparent' : ''">
                        <input type="radio" name="payment_method" value="midtrans" x-model="paymentMethod" class="text-primary focus:ring-primary w-4 h-4">
                        <div class="flex-1">
                            <p class="font-bold text-sm text-on-surface">Digital (QRIS, Transfer)</p>
                            <p class="text-xs text-on-surface-variant">QRIS / transfer via Midtrans.</p>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant" :class="paymentMethod === 'midtrans' ? 'text-primary' : ''">qr_code_2</span>
                    </label>
                </div>

                <button type="button" @click="submitOrder()"
                        :disabled="cart.length === 0"
                        class="w-full py-4 bg-[#154212] text-white font-headline font-bold text-base rounded-2xl shadow-lg hover:opacity-90 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    Konfirmasi Pesanan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Data semua produk untuk search
const allProducts = @json($allProducts);

function orderApp() {
    return {
        activeCategory: 'all',
        cart: [],
        showCart: false,
        showSearch: false,
        search: '',
        paymentMethod: 'midtrans',
        activeTab: 'menu',
        showInfo: false,

        init() {},

        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get searchResults() {
            if (!this.search.trim()) return [];
            const q = this.search.toLowerCase();
            return allProducts.filter(p =>
                p.name.toLowerCase().includes(q) ||
                p.description.toLowerCase().includes(q) ||
                p.category.toLowerCase().includes(q)
            );
        },

        getQty(productId) {
            const item = this.cart.find(i => i.product_id === productId);
            return item ? item.quantity : 0;
        },

        increase(productId, name, price) {
            const item = this.cart.find(i => i.product_id === productId);
            if (item) { item.quantity++; }
            else { this.cart.push({ product_id: productId, name, price, quantity: 1, note: '' }); }
        },

        decrease(productId) {
            const idx = this.cart.findIndex(i => i.product_id === productId);
            if (idx === -1) return;
            if (this.cart[idx].quantity > 1) { this.cart[idx].quantity--; }
            else { this.cart.splice(idx, 1); }
        },

        get totalItems() { return this.cart.reduce((s, i) => s + i.quantity, 0); },
        get totalPrice() { return this.cart.reduce((s, i) => s + (i.price * i.quantity), 0); },

        formatRp(val) { return new Intl.NumberFormat('id-ID').format(val); },

        submitOrder() {
            if (this.cart.length === 0) return;
            const container = document.getElementById('orderItems');
            container.innerHTML = '';
            this.cart.forEach((item, idx) => {
                container.innerHTML += `
                    <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${idx}][quantity]"   value="${item.quantity}">
                    <input type="hidden" name="items[${idx}][note]"       value="${item.note || ''}">
                `;
            });
            document.getElementById('orderForm').submit();
        }
    }
}
</script>
</body>
</html>
