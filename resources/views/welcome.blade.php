<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Warung Midori | Authentic Balinese Cuisine</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                "surface": "#f8faf8", "background": "#f8faf8",
                "primary": "#154212", "primary-container": "#2d5a27",
                "primary-fixed": "#bcf0ae", "primary-fixed-dim": "#a1d494",
                "on-primary": "#ffffff", "on-primary-fixed": "#002201",
                "on-primary-container": "#9dd090",
                "secondary": "#506600", "secondary-container": "#caee5d",
                "secondary-fixed": "#ccf05f", "secondary-fixed-dim": "#b1d446",
                "on-secondary": "#ffffff", "on-secondary-fixed": "#161e00",
                "on-secondary-container": "#546b00",
                "tertiary": "#553112", "tertiary-container": "#704727",
                "tertiary-fixed": "#ffdcc5", "tertiary-fixed-dim": "#f4bb92",
                "on-tertiary": "#ffffff", "on-tertiary-fixed": "#301400",
                "on-tertiary-fixed-variant": "#653d1e",
                "error": "#ba1a1a", "error-container": "#ffdad6",
                "on-error": "#ffffff", "on-error-container": "#93000a",
                "surface-dim": "#d8dad9", "surface-bright": "#f8faf8",
                "surface-container-lowest": "#ffffff", "surface-container-low": "#f2f4f2",
                "surface-container": "#eceeec", "surface-container-high": "#e6e9e7",
                "surface-container-highest": "#e1e3e1",
                "surface-variant": "#e1e3e1", "surface-tint": "#3b6934",
                "on-surface": "#191c1b", "on-surface-variant": "#42493e",
                "on-background": "#191c1b",
                "outline": "#72796e", "outline-variant": "#c2c9bb",
                "inverse-surface": "#2e3130", "inverse-on-surface": "#eff1ef",
                "inverse-primary": "#a1d494",
            },
            fontFamily: { "headline": ["Noto Serif","serif"], "body": ["Inter","sans-serif"], "label": ["Inter","sans-serif"] },
            borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "1.5rem", "2xl": "2rem", "full": "9999px" },
        }}
    }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .hero-gradient { background: linear-gradient(to bottom, rgba(25,28,27,0.3), rgba(25,28,27,0.85)); }
        
        /* Reveal Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 150ms; }
        .reveal-delay-2 { transition-delay: 300ms; }
        .reveal-delay-3 { transition-delay: 450ms; }
    </style>
</head>
<body class="bg-surface text-on-surface font-body selection:bg-secondary-container" x-data="scannerApp()">

{{-- ── Navbar ──────────────────────────────────────────────────────────── --}}
<nav class="fixed top-0 w-full z-50 bg-[#f8faf8]/70 backdrop-blur-md">
    <div class="flex justify-between items-center w-full px-8 py-5 max-w-screen-2xl mx-auto">
        <div class="text-2xl font-headline font-bold text-primary tracking-tight">Warung Midori</div>
        <div class="hidden md:flex items-center space-x-10">
            <a href="#heritage" class="font-headline font-bold tracking-tight text-on-surface hover:text-primary transition-colors">Our Heritage</a>
            <a href="#menu" class="font-headline font-bold tracking-tight text-on-surface hover:text-primary transition-colors">Signature Dishes</a>
            <a href="#visit" class="font-headline font-bold tracking-tight text-on-surface hover:text-primary transition-colors">Visit Us</a>
        </div>

    </div>
</nav>

<main>

    {{-- ── Hero ────────────────────────────────────────────────────────── --}}
    <section class="relative min-h-screen flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0 bg-primary-container">
            {{-- Fallback gradient jika tidak ada gambar --}}
            <div class="absolute inset-0 hero-gradient z-10"></div>
            @php $heroProduct = $featured->first(); @endphp
            @if($heroProduct?->image)
                <img src="{{ Storage::url($heroProduct->image) }}"
                     alt="{{ $heroProduct->name }}"
                     class="w-full h-full object-cover">
            @else
                {{-- Placeholder pattern --}}
                <div class="w-full h-full bg-gradient-to-br from-primary-container via-primary to-tertiary-container opacity-80"></div>
            @endif
        </div>

        <div class="relative z-10 max-w-screen-2xl mx-auto px-8 w-full pt-24 pb-16">
            <div class="max-w-3xl">
                <span class="inline-block px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold uppercase tracking-widest mb-6 reveal">
                    Traditional Bangli Cuisine
                </span>
                <h1 class="font-headline text-5xl md:text-7xl lg:text-8xl text-surface font-bold leading-[1.1] mb-8 tracking-tight reveal reveal-delay-1">
                    Cita Rasa Tradisi Bangli dalam Setiap Sajian
                </h1>
                <p class="font-body text-surface/90 text-lg md:text-xl max-w-xl mb-10 leading-relaxed reveal reveal-delay-2">
                    Nikmati keaslian rempah pilihan dari dataran tinggi Bangli, diolah dengan teknik warisan turun-temurun untuk kebahagiaan lidah Anda.
                </p>
                <div class="flex flex-wrap gap-4 reveal reveal-delay-3">
                <a href="#menu"
                   class="inline-flex items-center gap-2 bg-secondary-fixed text-on-secondary-fixed px-10 py-4 rounded-xl font-bold hover:opacity-90 active:scale-95 transition-all">
                    Explore Menu
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                <button @click="openScanner()"
                        class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border border-white/20 px-8 py-4 rounded-xl font-bold hover:bg-white/20 active:scale-95 transition-all">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">qr_code_scanner</span>
                    Scan QR Meja
                </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Heritage ─────────────────────────────────────────────────────── --}}
    <section id="heritage" class="py-24 bg-surface">
        <div class="max-w-screen-2xl mx-auto px-8 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative reveal">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden bg-surface-container-high relative z-10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[10rem] text-on-surface-variant opacity-10">restaurant</span>
                </div>
                <div class="absolute -bottom-6 -right-6 w-64 h-64 bg-secondary-container/30 rounded-full blur-3xl -z-0"></div>
            </div>
            <div class="flex flex-col justify-center reveal reveal-delay-2">
                <h2 class="font-headline text-4xl md:text-5xl text-primary font-bold mb-8 leading-tight">
                    A Legacy of Verdant Bangli
                </h2>
                <div class="space-y-5 text-on-surface-variant text-lg leading-relaxed">
                    <p>Berawal dari sebuah dapur kecil di jantung Bangli, <span class="text-primary font-bold">Warung Midori</span> lahir dari kecintaan kami pada kekayaan hasil bumi Bali Utara. Nama "Midori" sendiri berarti hijau—melambangkan komitmen kami pada kesegaran dan keberlanjutan.</p>
                    <p>Kami percaya bahwa rasa sejati hanya bisa dicapai melalui bahan lokal terbaik. Setiap ekor ikan Mujair kami berasal dari danau Batur, dipadukan dengan bumbu <em>Base Genep</em> yang digiling manual setiap pagi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Signature Dishes (Dinamis) ──────────────────────────────────── --}}
    <section id="menu" class="py-24 bg-surface-container-low">
        <div class="max-w-screen-2xl mx-auto px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6 reveal">
                <div>
                    <h2 class="font-headline text-4xl md:text-5xl text-primary font-bold mb-4">Signature Dishes</h2>
                    <p class="font-body text-on-surface-variant text-lg max-w-xl">
                        Menu terpopuler pilihan pelanggan setia Warung Midori.
                    </p>
                </div>
            </div>

            @if($featured->count() >= 3)
                {{-- Bento layout: 1 large + 2 side --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:h-[700px]">
                    {{-- Large Feature Card --}}
                    @php $main = $featured->first(); @endphp
                    <div class="md:col-span-7 md:row-span-2 group relative overflow-hidden rounded-2xl bg-surface-container-lowest">
                        @if($main->image)
                            <img src="{{ Storage::url($main->image) }}" alt="{{ $main->name }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-primary-container flex items-center justify-center min-h-[400px]">
                                <span class="material-symbols-outlined text-[8rem] text-on-primary-container opacity-20">restaurant</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-70 group-hover:opacity-90 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 p-8 md:p-10 text-surface">
                            <span class="bg-secondary-fixed text-on-secondary-fixed text-xs font-bold px-3 py-1 rounded mb-4 inline-block uppercase tracking-wider">
                                Most Popular
                            </span>
                            <h3 class="font-headline text-2xl md:text-3xl font-bold mb-2">{{ $main->name }}</h3>
                            @if($main->description)
                                <p class="text-surface/80 text-sm max-w-md mb-4 line-clamp-2">{{ $main->description }}</p>
                            @endif
                            <span class="text-2xl font-bold font-headline">Rp {{ number_format($main->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Side Cards --}}
                    @foreach($featured->skip(1) as $product)
                        <div class="md:col-span-5 group relative overflow-hidden rounded-2xl bg-surface-container-lowest min-h-[200px]">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-tertiary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[5rem] text-on-tertiary-container opacity-20">restaurant</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-70"></div>
                            <div class="absolute bottom-0 left-0 p-6 md:p-8 text-surface">
                                <h3 class="font-headline text-xl md:text-2xl font-bold mb-1">{{ $product->name }}</h3>
                                <span class="text-lg md:text-xl font-bold font-headline">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($featured->count() > 0)
                {{-- Fallback grid jika produk < 3 --}}
                <div class="grid grid-cols-1 md:grid-cols-{{ $featured->count() }} gap-6">
                    @foreach($featured as $product)
                        <div class="group relative overflow-hidden rounded-2xl bg-surface-container-lowest h-80">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-primary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[5rem] text-on-primary-container opacity-20">restaurant</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-surface">
                                <h3 class="font-headline text-xl font-bold mb-1">{{ $product->name }}</h3>
                                <span class="text-lg font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">restaurant_menu</span>
                    <p>Menu akan segera tersedia.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ── Modern Ordering ─────────────────────────────────────────────── --}}
    <section class="py-24 bg-surface">
        <div class="max-w-screen-2xl mx-auto px-8">
            <div class="bg-primary rounded-2xl p-8 md:p-16 overflow-hidden relative reveal">
                <div class="absolute top-0 right-0 w-1/3 h-full opacity-10 pointer-events-none hidden lg:flex items-center justify-end pr-8">
                    <span class="material-symbols-outlined text-[20rem] text-white" style="font-variation-settings:'FILL' 1">qr_code_2</span>
                </div>
                <div class="relative z-10 max-w-2xl">
                    <h2 class="font-headline text-4xl md:text-5xl text-on-primary-container font-bold mb-4 reveal">Modern Way to Dine</h2>
                    <p class="text-primary-fixed-dim text-lg mb-12 reveal reveal-delay-1">Tradisi bertemu teknologi. Nikmati kemudahan memesan menu favorit Anda tanpa perlu mengantri.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                        @foreach([
                            ['qr_code_scanner', 'Scan', 'Scan kode QR yang tersedia di meja Anda.'],
                            ['touch_app', 'Pesan', 'Pilih hidangan langsung dari smartphone.'],
                            ['sentiment_satisfied', 'Nikmati', 'Pesanan diantar hangat langsung ke meja Anda.'],
                        ] as $index => [$icon, $title, $desc])
                            <div class="flex flex-col items-center sm:items-start text-center sm:text-left reveal reveal-delay-{{ $index + 1 }}">
                                <div class="w-16 h-16 rounded-2xl bg-primary-container flex items-center justify-center mb-4 border border-outline-variant/20">
                                    <span class="material-symbols-outlined text-secondary-fixed text-3xl" style="font-variation-settings:'FILL' 1">{{ $icon }}</span>
                                </div>
                                <h4 class="font-bold text-surface text-xl mb-2">{{ $title }}</h4>
                                <p class="text-surface/70 text-sm">{{ $desc }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA ──────────────────────────────────────────────────────────── --}}
    <section id="visit" class="py-24 bg-surface">
        <div class="max-w-4xl mx-auto px-8 text-center">
            <h2 class="font-headline text-5xl md:text-6xl text-primary font-bold mb-8 reveal">Ready to taste the heritage?</h2>
            <p class="font-body text-on-surface-variant text-xl mb-12 leading-relaxed reveal reveal-delay-1">
                Kunjungi kami di Bangli atau pesan langsung dari meja Anda dengan scan QR Code.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center reveal reveal-delay-2">
                <a href="https://maps.google.com/?q=Jl.+Merdeka+No.+108B+Bangli+Bali" target="_blank"
                   class="bg-primary text-on-primary px-12 py-5 rounded-2xl font-bold text-lg hover:opacity-90 active:scale-95 transition-all shadow-lg">
                    Temukan Lokasi Kami
                </a>
            </div>
        </div>
    </section>

</main>

{{-- ── QR Scanner Modal ────────────────────────────────────────────────── --}}
<div x-show="showScanner" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-surface-container-lowest rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex justify-between items-center px-6 py-5 border-b border-outline-variant/20">
            <div>
                <h3 class="font-headline font-bold text-xl text-primary">Scan QR Meja</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Arahkan kamera ke QR Code di meja Anda</p>
            </div>
            <button @click="closeScanner()" class="p-2 hover:bg-surface-container rounded-xl transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>

        {{-- Camera view --}}
        <div class="relative bg-black" style="aspect-ratio: 1">
            <video id="qr-video" class="w-full h-full object-cover" playsinline autoplay muted></video>
            <canvas id="qr-canvas" class="hidden"></canvas>

            {{-- Scan frame overlay --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-52 h-52 relative">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-secondary-fixed rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-secondary-fixed rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-secondary-fixed rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-secondary-fixed rounded-br-lg"></div>
                    {{-- Scan line animation --}}
                    <div class="absolute left-2 right-2 h-0.5 bg-secondary-fixed/70 animate-bounce" style="top: 50%"></div>
                </div>
            </div>

            {{-- Error state --}}
            <div x-show="cameraError" class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-white text-center p-6">
                <span class="material-symbols-outlined text-4xl mb-3 text-error" style="font-variation-settings:'FILL' 1">no_photography</span>
                <p class="font-semibold text-sm" x-text="cameraError"></p>
            </div>
        </div>

        {{-- Status --}}
        <div class="px-6 py-4 text-center">
            <p class="text-sm text-on-surface-variant" x-text="scanStatus"></p>
        </div>
    </div>
</div>

{{-- ── Footer ──────────────────────────────────────────────────────────── --}}<footer class="w-full py-16 px-8 bg-tertiary">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:flex lg:justify-between items-start max-w-screen-2xl mx-auto gap-8">
        <div class="flex flex-col gap-4">
            <div class="text-xl font-headline italic text-white">Warung Midori</div>
            <p class="text-sm text-white/70 max-w-xs leading-relaxed">
                © {{ date('Y') }} Warung Midori.<br>Traditional Balinese Ikan Mujair, Bangli.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-12 lg:gap-24">
            <div class="flex flex-col gap-3">
                <h5 class="text-white/60 font-bold text-xs uppercase tracking-widest">Menu</h5>
                <a href="#heritage" class="text-sm text-white/70 hover:text-secondary-fixed transition-colors">Our Heritage</a>
                <a href="#menu" class="text-sm text-white/70 hover:text-secondary-fixed transition-colors">Signature Dishes</a>
            </div>
            <div class="flex flex-col gap-3">
                <h5 class="text-white/60 font-bold text-xs uppercase tracking-widest">Staf</h5>
                <a href="{{ route('login') }}" class="text-sm text-white/70 hover:text-secondary-fixed transition-colors">Login</a>
            </div>
        </div>
        <div class="flex flex-col gap-3 max-w-sm">
            <h5 class="text-white/60 font-bold text-xs uppercase tracking-widest">Kunjungi Kami</h5>
            <p class="text-sm text-white/70 leading-relaxed">
                Jl. Merdeka No. 108B, Bangli, Bali<br>
                Buka: 08.00 – 22.00 WITA
            </p>
        </div>
    </div>
</footer>

<script>
function scannerApp() {
    return {
        showScanner: false,
        cameraError: null,
        scanStatus: 'Mendeteksi QR Code...',
        stream: null,
        animFrame: null,

        openScanner() {
            this.showScanner = true;
            this.cameraError = null;
            this.scanStatus  = 'Memulai kamera...';
            this.$nextTick(() => this.startCamera());
        },

        closeScanner() {
            this.stopCamera();
            this.showScanner = false;
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                const video = document.getElementById('qr-video');
                video.srcObject = this.stream;
                video.onloadedmetadata = () => {
                    video.play();
                    this.scanStatus = 'Arahkan ke QR Code meja...';
                    this.scanFrame();
                };
            } catch (e) {
                this.cameraError = 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.';
            }
        },

        stopCamera() {
            if (this.animFrame) cancelAnimationFrame(this.animFrame);
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        },

        scanFrame() {
            const video  = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');

            if (video.readyState !== video.HAVE_ENOUGH_DATA) {
                this.animFrame = requestAnimationFrame(() => this.scanFrame());
                return;
            }

            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                // Validasi URL dari domain yang sama
                try {
                    const url = new URL(code.data);
                    if (url.pathname.startsWith('/order/menu/')) {
                        this.scanStatus = 'QR Code terdeteksi! Mengalihkan...';
                        this.stopCamera();
                        window.location.href = code.data;
                        return;
                    } else {
                        this.scanStatus = 'QR Code tidak valid untuk meja ini.';
                    }
                } catch {
                    this.scanStatus = 'QR Code tidak dikenali.';
                }
            }

            this.animFrame = requestAnimationFrame(() => this.scanFrame());
        }
    }
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Berhenti memantau setelah animasi selesai satu kali jalan (opsional, biarkan jika ingin terus terpantau)
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('.reveal').forEach((el) => {
            observer.observe(el);
        });
    });
</script>

</body>
</html>
