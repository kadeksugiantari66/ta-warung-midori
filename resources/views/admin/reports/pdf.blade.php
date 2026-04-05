<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { color: #555; margin-bottom: 16px; }
        .summary { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { background: #f3f4f6; padding: 10px 16px; border-radius: 6px; }
        .card p { margin: 0; }
        .card .val { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #16a34a; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan – Warung Midori</h1>
    <p class="subtitle">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>

    <div class="summary">
        <div class="card">
            <p>Total Pesanan</p>
            <p class="val">{{ $summary['total_orders'] }}</p>
        </div>
        <div class="card">
            <p>Selesai</p>
            <p class="val">{{ $summary['completed'] }}</p>
        </div>
        <div class="card">
            <p>Pendapatan</p>
            <p class="val">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Meja</th><th>Item</th><th>Total</th><th>Bayar</th><th>Status</th><th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->queue_number }}</td>
                    <td>{{ $order->table->table_number }}</td>
                    <td>{{ $order->orderItems->map(fn($i) => $i->quantity.'× '.$i->product->name)->join(', ') }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $order->payment?->method === 'cash' ? 'Tunai' : ($order->payment ? 'Digital' : '-') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->created_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; color:#888;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak pada {{ now()->format('d/m/Y H:i') }} – SiMidori</p>
</body>
</html>
