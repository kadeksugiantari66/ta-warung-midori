<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Scan QR Meja</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-['Inter']" x-data="scannerApp()">
    <main class="min-h-screen flex items-center justify-center px-4">
        <section class="w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pelanggan</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Scan QR Meja</h1>
            <p class="mt-2 text-sm text-slate-600">Klik tombol di bawah, izinkan kamera, lalu arahkan ke QR di meja.</p>

            <button @click="openScanner()"
                    class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-white font-semibold hover:bg-emerald-800 active:scale-[0.99] transition">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">qr_code_scanner</span>
                Scan Sekarang
            </button>
        </section>
    </main>

    <div x-show="showScanner" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h2 class="text-base font-semibold text-slate-900">Arahkan ke QR Meja</h2>
                <button @click="closeScanner()" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="relative bg-black" style="aspect-ratio: 1 / 1">
                <video id="qr-video" class="h-full w-full object-cover" playsinline autoplay muted></video>
                <canvas id="qr-canvas" class="hidden"></canvas>

                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="h-52 w-52 rounded-2xl border-2 border-emerald-300/80"></div>
                </div>

                <div x-show="cameraError" class="absolute inset-0 flex items-center justify-center bg-black/80 px-6 text-center text-sm text-white">
                    <p x-text="cameraError"></p>
                </div>
            </div>

            <div class="px-4 py-3 text-center text-sm text-slate-600" x-text="scanStatus"></div>
        </div>
    </div>

    <script>
        function scannerApp() {
            return {
                showScanner: false,
                cameraError: null,
                scanStatus: 'Menunggu kamera...',
                stream: null,
                animFrame: null,

                openScanner() {
                    this.showScanner = true;
                    this.cameraError = null;
                    this.scanStatus = 'Memulai kamera...';
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
                            this.scanStatus = 'Arahkan kamera ke QR meja...';
                            this.scanFrame();
                        };
                    } catch (error) {
                        this.cameraError = 'Tidak bisa akses kamera. Pastikan izin kamera diaktifkan.';
                    }
                },

                stopCamera() {
                    if (this.animFrame) {
                        cancelAnimationFrame(this.animFrame);
                    }
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                    this.stream = null;
                },

                scanFrame() {
                    const video = document.getElementById('qr-video');
                    const canvas = document.getElementById('qr-canvas');

                    if (video.readyState !== video.HAVE_ENOUGH_DATA) {
                        this.animFrame = requestAnimationFrame(() => this.scanFrame());
                        return;
                    }

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        try {
                            const url = new URL(code.data, window.location.origin);
                            if (url.pathname.startsWith('/order/menu/')) {
                                this.scanStatus = 'QR terdeteksi. Membuka menu...';
                                this.stopCamera();
                                window.location.href = url.href;
                                return;
                            }
                            this.scanStatus = 'QR tidak valid untuk menu meja.';
                        } catch (error) {
                            this.scanStatus = 'QR tidak dikenali.';
                        }
                    }

                    this.animFrame = requestAnimationFrame(() => this.scanFrame());
                }
            };
        }
    </script>
</body>
</html>
