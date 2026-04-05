<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>QR Meja {{ $table->table_number }} – Warung Midori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,700;0,900;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        /* Hilangkan header/footer bawaan browser saat print */
        @page {
            size: 80mm 120mm;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 32px 16px;
        }

        /* Kartu cetak — ukuran struk 80mm */
        .card {
            width: 300px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 48px rgba(21,66,18,0.15);
        }

        .card-header {
            background: #154212;
            padding: 22px 20px 18px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card-header::before {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            top: -80px; right: -50px;
        }
        .brand {
            font-family: 'Noto Serif', serif;
            font-size: 22px;
            font-weight: 900;
            color: #bcf0ae;
            letter-spacing: -0.3px;
            position: relative;
            z-index: 1;
        }
        .brand-tagline {
            font-size: 10px;
            color: rgba(188,240,174,0.55);
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-top: 3px;
            position: relative;
            z-index: 1;
        }
        .brand-accent {
            width: 28px; height: 2px;
            background: #ccf05f;
            border-radius: 2px;
            margin: 12px auto 0;
            position: relative;
            z-index: 1;
        }

        .qr-area {
            padding: 22px 20px 16px;
            text-align: center;
            background: #fff;
        }
        .scan-hint {
            font-size: 10px;
            font-weight: 700;
            color: #72796e;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .qr-frame {
            display: inline-flex;
            padding: 10px;
            background: #fff;
            border: 1.5px solid #e1e3e1;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(21,66,18,0.07);
        }
        #qr-canvas {
            width: 180px;
            height: 180px;
            display: block;
            border-radius: 6px;
        }

        .card-footer {
            background: #f2f4f2;
            padding: 14px 20px 18px;
            text-align: center;
            border-top: 1px solid #e1e3e1;
        }
        .table-eyebrow {
            font-size: 9px;
            font-weight: 700;
            color: #72796e;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .table-number {
            font-family: 'Noto Serif', serif;
            font-size: 48px;
            font-weight: 900;
            color: #154212;
            line-height: 1;
        }
        .instruction {
            font-size: 10px;
            color: #42493e;
            line-height: 1.6;
            margin-top: 10px;
        }

        /* Tombol kontrol — tidak ikut cetak */
        .controls {
            margin-top: 24px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: 'Inter', sans-serif;
            transition: opacity 0.15s, transform 0.1s;
        }
        .btn:active { transform: scale(0.97); }
        .btn:hover  { opacity: 0.85; }
        .btn-print  { background: #154212; color: #fff; }
        .btn-back   { background: #e1e3e1; color: #191c1b; }

        @media print {
            html, body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
            }
            .controls { display: none !important; }
            .card {
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-header">
            <div class="brand">Warung Midori</div>
            <div class="brand-tagline">Bangli · Bali</div>
            <div class="brand-accent"></div>
        </div>

        <div class="qr-area">
            <p class="scan-hint">Scan untuk memesan</p>
            <div class="qr-frame">
                <canvas id="qr-canvas"></canvas>
            </div>
        </div>

        <div class="card-footer">
            <p class="table-eyebrow">Nomor Meja</p>
            <p class="table-number">{{ $table->table_number }}</p>
            <p class="instruction">
                Arahkan kamera HP ke QR Code<br>
                untuk melihat menu &amp; memesan
            </p>
        </div>
    </div>

    <div class="controls">
        <button class="btn btn-print" onclick="window.print()">Cetak</button>
        <button class="btn btn-back"  onclick="window.history.back()">← Kembali</button>
    </div>

    {{-- Render SVG QR ke canvas agar tercetak sempurna --}}
    <script>
    (function() {
        const svgUrl = "{{ Storage::url($table->qr_code_path) }}";
        const canvas = document.getElementById('qr-canvas');
        const ctx    = canvas.getContext('2d');
        const SIZE   = 360; // render 2x untuk ketajaman
        canvas.width  = SIZE;
        canvas.height = SIZE;

        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, SIZE, SIZE);
            ctx.drawImage(img, 0, 0, SIZE, SIZE);
        };
        img.onerror = function() {
            // Fallback: tampilkan SVG langsung
            canvas.style.display = 'none';
            const fallback = document.createElement('img');
            fallback.src   = svgUrl;
            fallback.style.cssText = 'width:180px;height:180px;display:block;border-radius:6px;';
            canvas.parentNode.appendChild(fallback);
        };
        img.src = svgUrl;
    })();
    </script>
</body>
</html>
