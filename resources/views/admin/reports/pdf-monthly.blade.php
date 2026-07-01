<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan – Warung Midori</title>
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
        .rekap {
            border: 1px solid #000; padding: 10px 14px; margin-bottom: 18px;
            line-height: 1.8; font-size: 12px;
        }
        .rekap .judul { font-weight: bold; margin-bottom: 4px; }
        .split { width: 100%; }
        .split td { vertical-align: top; width: 50%; padding: 0; }
        .split td.kiri { padding-right: 14px; }
        .split td.kanan { padding-left: 14px; }
        table.d { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.d thead th { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 4px; text-align: left; }
        table.d tbody td { padding: 5px 4px; border-bottom: 1px dotted #999; }
        table.d tfoot td { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 4px; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

@php
    $bulan = \Carbon\Carbon::parse($month.'-01')->locale('id')->translatedFormat('F Y');
    $bulanUpper = strtoupper($bulan);
    $kiri = $dailyTrend->slice(0, 16)->values();
    $kanan = $dailyTrend->slice(16)->values();
    $twoCols = $kanan->count() > 0; // kolom kanan hanya dipakai bila hari > 16
    $totalOrders = collect($dailyTrend)->sum('total_orders');
    $totalRevenue = collect($dailyTrend)->sum('revenue');
@endphp

<div class="header">
    <h2>WARUNG MIDORI</h2>
    <p>Jl. Merdeka No.108B, Bangli, Bali | Telp: 0812-3456-7890</p>
</div>

<hr>

<div class="title">BAGIAN 2 : TREN PENJUALAN BULANAN ({{ $bulanUpper }})</div>

<div class="rekap">
    <div class="judul">REKAPITULASI KEUANGAN BULAN {{ $bulanUpper }} :</div>
    - Total Transaksi : {{ $summary['total_orders'] }} Pesanan ({{ $summary['completed'] }} Sukses)<br>
    - Total Pendapatan : Rp {{ number_format($summary['revenue'], 0, ',', '.') }}<br>
    &nbsp;&nbsp;&nbsp;&nbsp;Tunai : Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}<br>
    &nbsp;&nbsp;&nbsp;&nbsp;Digital : Rp {{ number_format($summary['digital_revenue'], 0, ',', '.') }}
</div>

<table class="split">
    <tr>
        <td class="kiri">
            <table class="d">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="center">Order</th>
                        <th class="right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kiri as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->locale('id')->translatedFormat('d M') }}</td>
                        <td class="center">{{ $day->total_orders }}</td>
                        <td class="right">{{ number_format($day->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @unless ($twoCols)
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="center">{{ $totalOrders }}</td>
                        <td class="right">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endunless
            </table>
        </td>
        <td class="kanan">
            @if ($twoCols)
            <table class="d">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="center">Order</th>
                        <th class="right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kanan as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->locale('id')->translatedFormat('d M') }}</td>
                        <td class="center">{{ $day->total_orders }}</td>
                        <td class="right">{{ number_format($day->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="center">{{ $totalOrders }}</td>
                        <td class="right">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </td>
    </tr>
</table>

</body>
</html>
