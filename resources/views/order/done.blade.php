<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pesanan Selesai – SiMidori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-fixed": "#bcf0ae", "on-primary-fixed": "#002201",
                "secondary": "#506600", "secondary-container": "#caee5d", "on-secondary-fixed": "#161e00",
                "tertiary-fixed": "#ffdcc5", "on-tertiary-fixed": "#301400",
                "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f2f4f2", "surface-container": "#eceeec",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e", "outline-variant": "#c2c9bb",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex flex-col items-center justify-center px-5">

    <div class="max-w-sm w-full text-center space-y-6">

        {{-- Icon selesai --}}
        <div class="w-24 h-24 rounded-full bg-primary-fixed flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-5xl text-primary" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>

        <div>
            <h1 class="font-headline text-3xl font-black text-primary mb-2">Pesanan Selesai</h1>
            <p class="text-on-surface-variant text-sm">
                #{{ $order->queue_number }} · Meja {{ $order->table->table_number }}
            </p>
        </div>

        {{-- Tombol pesan lagi --}}
        <a href="{{ route('order.menu', $order->table) }}"
           class="flex items-center justify-center gap-2 w-full py-4 bg-primary text-white font-headline font-bold text-base rounded-2xl shadow-lg hover:opacity-90 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">restaurant_menu</span>
            Pesan Lagi
        </a>

        <p class="text-xs text-on-surface-variant">Warung Midori · Bangli, Bali</p>
    </div>

</body>
</html>
