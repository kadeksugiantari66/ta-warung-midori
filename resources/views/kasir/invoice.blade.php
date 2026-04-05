<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->queue_number }} – Warung Midori</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 16px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .total { font-size: 14px; font-weight: bold; }
        @media print {
            body { max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="center bold" style="font-size:16px; margin-bottom:4px;">WARUNG MIDORI</div>
    <div class="center">Jl. Merdeka No. 108B, Bangli, Bali</div>
    <div class="divider"></div>

    <div class="row"><span>Meja</span><span>{{ $order->table->table_number }}</span></div>
    <div class="row"><span>No. Antrean</span><span>#{{ $order->queue_number }}</span></div>
    <div class="row"><span>Tanggal</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Pembayaran</span><span>{{ $order->payment->method === 'cash' ? 'Tunai' : 'Digital' }}</span></div>

    <div class="divider"></div>

    @foreach ($order->orderItems as $item)
        <div style="margin: 4px 0;">
            <div>{{ $item->product->name }}</div>
            <div class="row">
                <span>{{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($item->note)
                <div style="color:#555; font-size:11px;">  *{{ $item->note }}</div>
            @endif
        </div>
    @endforeach

    <div class="divider"></div>
    <div class="row total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="divider"></div>

    <div class="center" style="margin-top:8px;">Terima kasih telah berkunjung!</div>
    <div class="center">Selamat menikmati.</div>

    <div class="no-print" style="margin-top:20px; text-align:center;">
        <button onclick="window.print()"
                style="padding:8px 20px; background:#16a34a; color:white; border:none; border-radius:6px; cursor:pointer; font-size:13px;">
            🖨 Cetak
        </button>
        <button onclick="window.close()"
                style="padding:8px 20px; background:#e5e7eb; color:#374151; border:none; border-radius:6px; cursor:pointer; font-size:13px; margin-left:8px;">
            Tutup
        </button>
    </div>
</body>
</html>
