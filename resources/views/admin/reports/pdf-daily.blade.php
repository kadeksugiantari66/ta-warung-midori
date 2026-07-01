<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian – Warung Midori</title>
    <style>
        @page { size: A4 portrait; margin: 20mm 18mm; }
        body { margin: 0; padding: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: "Courier New", monospace;
            font-size: 13px; color: #000; line-height: 1.4;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { font-size: 28px; margin-bottom: 4px; margin-top: 0; }
        .header p { font-size: 12px; margin: 0; }
        hr { border: none; border-top: 2px solid #000; margin: 10px 0 16px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; margin: 0 0 18px; }
        .info { margin-bottom: 18px; line-height: 1.7; font-size: 12px; }
        .info div { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead th { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 7px 6px; text-align: left; }
        tbody td { padding: 6px 6px; border-bottom: 1px dotted #999; vertical-align: top; }
        tfoot td { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 7px 6px; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

<div class="header">
    <h2>WARUNG MIDORI</h2>
    <p>Jl. Merdeka No.108B, Bangli, Bali | Telp: 0812-3456-7890</p>
</div>

<hr>

<div class="title">BAGIAN 1 : LAPORAN PENJUALAN HARIAN</div>

<div class="info">
    <div>Periode Laporan : {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y') }}</div>
    <div>Manajer : {{ auth()->user()?->name ?? 'Admin Midori' }}</div>
    <div>Status Pesanan : Selesai (Completed)</div>
</div>

<table>
    <thead>
        <tr>
            <th>Antrean</th>
            <th>Meja</th>
            <th>Detail Pesanan</th>
            <th>Metode</th>
            <th>Jam</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td class="center">{{ $order['queue'] }}</td>
            <td class="center">{{ $order['table'] }}</td>
            <td>{{ $order['items'] }}</td>
            <td>{{ $order['method'] }}</td>
            <td>{{ $order['time'] }}</td>
            <td class="right">{{ number_format($order['total'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTAL PENJUALAN ({{ $summary['total_orders'] }} PESANAN)</td>
            <td class="center">-</td>
            <td class="right">{{ number_format($summary['revenue'], 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

</body>
</html>
