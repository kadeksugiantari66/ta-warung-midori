<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja Digunakan – Warung Midori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-container": "#2d5a27",
                "primary-fixed": "#bcf0ae", "on-primary-fixed": "#002201",
                "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e",
                "error": "#ba1a1a", "error-container": "#ffdad6", "on-error-container": "#93000a",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex items-center justify-center p-6">

<div class="text-center max-w-sm">
    <div class="text-6xl mb-6">🪑</div>
    <h1 class="font-headline font-black text-2xl text-primary mb-3">Meja Sedang Digunakan</h1>
    <p class="text-on-surface-variant text-sm leading-relaxed mb-6">
        Meja <strong class="text-primary">{{ $table->table_number }}</strong> sedang digunakan oleh pelanggan lain.
        Silakan tunggu hingga meja tersedia atau hubungi staf kami.
    </p>
    <a href="{{ route('order.menu', $table) }}"
       class="inline-block bg-primary text-white font-semibold px-6 py-3 rounded-2xl hover:opacity-90 transition-all text-sm">
        Coba Lagi
    </a>
</div>

</body>
</html>
