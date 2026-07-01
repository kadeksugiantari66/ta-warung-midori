<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Menu Terlaris – Warung Midori</title>
    <style>
        @page { size: A4 portrait; margin: 20mm 18mm; }
        body { margin: 0; padding: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: "Courier New", monospace;
            font-size: 13px; color: #000; line-height: 1.4;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { font-size: 28px; margin: 0 0 4px; }
        .header p { font-size: 12px; margin: 0; }
        hr { border: none; border-top: 2px solid #000; margin: 10px 0 16px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; margin: 0 0 18px; }
        .info { margin-bottom: 18px; line-height: 1.7; font-size: 12px; }
        .info div { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead th { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 7px 6px; text-align: left; }
        tbody td { padding: 6px 6px; border-bottom: 1px dotted #999; vertical-align: top; }
        tbody tr:first-child td { font-weight: bold; }
        tfoot td { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 7px 6px; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

@php
    $bulanUpper = strtoupper(\Carbon\Carbon::parse($month.'-01')->locale('id')->translatedFormat('F Y'));
@endphp

<div class="header">
    <h2>WARUNG MIDORI</h2>
    <p>Jl. Merdeka No.108B, Bangli, Bali | Telp: 0812-3456-7890</p>
</div>

<hr>

<div class="title">BAGIAN 3 : PERINGKAT PENJUALAN MENU ({{ $bulanUpper }})</div>

<div class="info">
    <div>Kategori Analisis : Seluruh Varian Menu Restoran</div>
    <div>Total Volume : {{ number_format($totalOrderedAll, 0, ',', '.') }} Porsi Produk Terjual</div>
</div>

<table>
    <thead>
        <tr>
            <th class="center">Rank</th>
            <th>Nama Varian Menu</th>
            <th>Kategori</th>
            <th class="right">Harga (Rp)</th>
            <th class="right">Terjual</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $p)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->category?->name ?? '-' }}</td>
            <td class="right">{{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="right">{{ $p->total_ordered ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">TOTAL PRODUK KESELURUHAN</td>
            <td class="right">-</td>
            <td class="right">{{ number_format($totalOrderedAll, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

</body>
</html>
