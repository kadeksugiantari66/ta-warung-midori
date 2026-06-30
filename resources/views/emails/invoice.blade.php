<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pesanan</title>
</head>
<body style="margin:0;padding:0;background:#f2f4f2;font-family:Arial,Helvetica,sans-serif;color:#191c1b;">
    <div style="max-width:480px;margin:0 auto;padding:24px 16px;">
        <div style="background:#154212;color:#ffffff;border-radius:16px 16px 0 0;padding:24px;text-align:center;">
            <h1 style="margin:0;font-size:22px;">Warung Midori</h1>
            <p style="margin:4px 0 0;font-size:13px;color:#bcf0ae;">Nota Pembayaran</p>
        </div>

        <div style="background:#ffffff;border-radius:0 0 16px 16px;padding:24px;">
            <table style="width:100%;font-size:14px;margin-bottom:16px;">
                <tr>
                    <td style="color:#42493e;padding:2px 0;">No. Antrean</td>
                    <td style="text-align:right;font-weight:bold;">#{{ $order->queue_number }}</td>
                </tr>
                <tr>
                    <td style="color:#42493e;padding:2px 0;">Meja</td>
                    <td style="text-align:right;">{{ $order->table->table_number }}</td>
                </tr>
                <tr>
                    <td style="color:#42493e;padding:2px 0;">Tanggal</td>
                    <td style="text-align:right;">{{ $order->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="color:#42493e;padding:2px 0;">Metode</td>
                    <td style="text-align:right;">{{ $order->payment?->method === 'midtrans' ? 'Digital (Midtrans)' : 'Tunai' }}</td>
                </tr>
            </table>

            <hr style="border:none;border-top:1px solid #e1e3e1;margin:0 0 12px;">

            <table style="width:100%;font-size:14px;">
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td style="padding:4px 0;">
                            <strong>{{ $item->quantity }}&times;</strong> {{ $item->product->name }}
                            @if ($item->note)
                                <br><span style="font-size:12px;color:#72796e;">Catatan: {{ $item->note }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;padding:4px 0;white-space:nowrap;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>

            <hr style="border:none;border-top:1px solid #e1e3e1;margin:12px 0;">

            <table style="width:100%;font-size:16px;">
                <tr>
                    <td style="font-weight:bold;">Total</td>
                    <td style="text-align:right;font-weight:bold;color:#154212;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div style="margin-top:20px;padding:12px;background:#bcf0ae;border-radius:12px;text-align:center;color:#154212;font-weight:bold;font-size:14px;">
                Pembayaran Lunas &middot; Terima kasih telah memesan!
            </div>

            <p style="margin:20px 0 0;font-size:12px;color:#72796e;text-align:center;">
                Warung Midori &middot; Jl. Merdeka No. 108B, Bangli, Bali
            </p>
        </div>
    </div>
</body>
</html>
