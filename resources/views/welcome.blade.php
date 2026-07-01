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
        html, body { height: 100%; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="h-full overflow-hidden bg-black text-white font-['Inter']" x-data="scannerApp()" x-init="start()">

    {{-- Kamera full layar --}}
    <video id="qr-video" class="fixed inset-0 h-full w-full object-cover" playsinline autoplay muted></video>
    <canvas id="qr-canvas" class="hidden"></canvas>

    {{-- Header atas --}}
    <div x-show="!cameraError"
         class="fixed inset-x-0 top-0 z-10 bg-gradient-to-b from-black/70 to-transparent px-5 pb-12 pt-7 text-center">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-white/70">Pelanggan</p>
        <h1 class="mt-1 text-xl font-bold">Scan QR Meja</h1>
    </div>

    {{-- Keterangan bawah --}}
    <div x-show="!cameraError"
         class="fixed inset-x-0 bottom-0 z-10 bg-gradient-to-t from-black/75 to-transparent px-5 pb-9 pt-14 text-center">
        <p class="inline-flex items-center gap-2 text-base font-semibold">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">qr_code_scanner</span>
            Scan QR di meja
        </p>
        <p class="mt-1 text-xs text-white/70" x-text="scanStatus"></p>
    </div>

    {{-- Overlay error / izin kamera (full layar) --}}
    <div x-show="cameraError" x-cloak
         class="fixed inset-0 z-20 flex flex-col items-center justify-center gap-4 bg-slate-900 px-6 text-center">
        <span class="material-symbols-outlined text-5xl text-white/80">no_photography</span>
        <p class="max-w-xs text-sm text-white/90" x-text="cameraError"></p>
        <button @click="start()"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 font-semibold hover:bg-emerald-700 active:scale-95 transition">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">photo_camera</span>
            Izinkan Kamera
        </button>
    </div>

    <script>
        function scannerApp() {
            return {
                cameraError: null,
                scanStatus: 'Menyalakan kamera...',
                stream: null,
                animFrame: null,

                start() {
                    this.cameraError = null;
                    this.scanStatus = 'Menyalakan kamera...';
                    this.$nextTick(() => this.startCamera());
                },

                async startCamera() {
                    this.stopCamera();
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
                        this.cameraError = 'Tidak bisa akses kamera. Ketuk "Izinkan Kamera" dan izinkan aksesnya.';
                        this.scanStatus = '';
                    }
                },

                stopCamera() {
                    if (this.animFrame) {
                        cancelAnimationFrame(this.animFrame);
                        this.animFrame = null;
                    }
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                    this.stream = null;
                },

                scanFrame() {
                    const video = document.getElementById('qr-video');
                    const canvas = document.getElementById('qr-canvas');

                    if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) {
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
