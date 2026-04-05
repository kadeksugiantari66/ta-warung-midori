<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Staf – SiMidori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "primary": "#154212", "primary-container": "#2d5a27",
                "primary-fixed": "#bcf0ae", "on-primary-fixed": "#002201",
                "secondary": "#506600", "secondary-container": "#caee5d",
                "secondary-fixed": "#ccf05f", "on-secondary-fixed": "#161e00",
                "surface": "#f8faf8", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f2f4f2", "surface-container": "#eceeec",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e",
                "outline": "#72796e", "outline-variant": "#c2c9bb",
                "error": "#ba1a1a", "error-container": "#ffdad6", "on-error-container": "#93000a",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"] },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex">

    {{-- Left panel (branding) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-primary flex-col justify-between p-12 relative overflow-hidden">
        {{-- Brand --}}
        <div>
            <h1 class="font-headline font-black text-3xl text-primary-fixed">Warung Midori</h1>
            <p class="text-primary-fixed/60 text-sm mt-1">Sistem Informasi Penjualan</p>
        </div>

        {{-- Center content --}}
        <div>
            <p class="font-headline font-black text-5xl text-white leading-tight mb-4">
                Selamat<br>Datang<br>Kembali.
            </p>
            <p class="text-primary-fixed/70 text-base leading-relaxed max-w-sm">
                Masuk ke sistem untuk mengelola pesanan, pembayaran, dan laporan Warung Midori.
            </p>
        </div>

        {{-- Footer --}}
        <p class="text-primary-fixed/40 text-xs">© {{ date('Y') }} Warung Midori · Bangli, Bali</p>

        {{-- Decorative --}}
        <span class="material-symbols-outlined absolute -right-8 -bottom-8 text-[20rem] text-white/5 pointer-events-none select-none"
              style="font-variation-settings:'FILL' 1">eco</span>
    </div>

    {{-- Right panel (form) --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Mobile brand --}}
            <div class="lg:hidden mb-8 text-center">
                <h1 class="font-headline font-black text-2xl text-primary">Warung Midori</h1>
                <p class="text-on-surface-variant text-sm">Sistem Informasi Penjualan</p>
            </div>

            <h2 class="font-headline font-black text-3xl text-primary mb-2">Login Staf</h2>
            <p class="text-on-surface-variant text-sm mb-8">Masukkan email dan kata sandi akun Anda.</p>

            {{-- Session errors --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-2xl text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">error</span>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-4 bg-primary-fixed text-on-primary-fixed rounded-2xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-on-surface mb-1.5">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-base">mail</span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-11 pr-4 py-3 border border-outline-variant rounded-2xl bg-surface-container-low text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('email') border-error @enderror"
                               placeholder="staf@midori.com">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-on-surface mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-base">lock</span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-11 pr-4 py-3 border border-outline-variant rounded-2xl bg-surface-container-low text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('password') border-error @enderror"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4">
                        <span class="text-sm text-on-surface-variant">Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-secondary hover:underline font-medium">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3.5 bg-primary text-white font-headline font-bold text-base rounded-2xl shadow-lg hover:opacity-90 active:scale-95 transition-all">
                    Masuk
                </button>
            </form>

            <p class="text-center text-xs text-on-surface-variant mt-8">
                Belum punya akun? Hubungi admin untuk pembuatan akun.
            </p>
        </div>
    </div>

</body>
</html>
