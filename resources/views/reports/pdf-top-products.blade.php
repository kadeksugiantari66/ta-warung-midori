<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Menu Terlaris – Warung Midori</title>
    <style>
        @page { margin: 2cm 1.8cm 2cm 1.8cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.35;
            orphans: 2;
            widows: 2;
        }

        .kop {
            width: 100%;
            border-bottom: 2px solid #154212;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .kop td { vertical-align: top; padding: 0; }
        .kop-logo {
            font-size: 16pt;
            font-weight: 900;
            color: #154212;
            letter-spacing: 1px;
        }
        .kop-sub {
            font-size: 7pt;
            color: #666;
            margin-top: 1px;
        }
        .kop-info {
            font-size: 7pt;
            color: #555;
            text-align: right;
            line-height: 1.6;
        }

        .judul {
            font-size: 11pt;
            font-weight: 900;
            color: #154212;
            margin-bottom: 2px;
        }
        .periode {
            font-size: 8pt;
            color: #555;
            margin-bottom: 12px;
        }

        .ringkasan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .ringkasan td {
            width: 33.33%;
            padding: 8px 10px;
            vertical-align: top;
            border: 1px solid #d4d4d4;
            background: #f7faf5;
        }
        .ringkasan-label {
            font-size: 6.5pt;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .ringkasan-val {
            font-size: 12pt;
            font-weight: 900;
            color: #154212;
            margin-top: 1px;
        }

        .insight {
            background: #f7faf5;
            border: 1px solid #c8e6c9;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 7.5pt;
            color: #333;
            line-height: 1.6;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data thead th {
            background: #154212;
            color: #fff;
            padding: 5px 8px;
            font-size: 6.5pt;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.data tbody td {
            padding: 5px 8px;
            font-size: 7pt;
            border-bottom: 1px solid #e5e7eb;
        }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        table.data tbody tr:nth-child(odd) td { background: #fff; }
        table.data tfoot td {
            padding: 6px 8px;
            font-size: 7.5pt;
            font-weight: 900;
            background: #f0f7ee;
            color: #154212;
            border-top: 1.5px solid #154212;
        }
        .rank-emas td { background: #fff8e1 !important; }
        .rank-perak td { background: #f5f5f5 !important; }
        .rank-perunggu td { background: #fff3e0 !important; }
        .tx-r { text-align: right; }
        .tx-c { text-align: center; }
        .tx-b { font-weight: 700; }

        .r-badge {
            display: inline-block;
            width: 18px;
            height: 18px;
            line-height: 18px;
            text-align: center;
            border-radius: 50%;
            font-size: 8pt;
            font-weight: 900;
            color: #fff;
        }
        .r-1 { background: #f5a623; }
        .r-2 { background: #9b9b9b; }
        .r-3 { background: #cd7f32; }
        .r-n { background: #154212; font-size: 6.5pt; }

        .ttd {
            margin-top: 12px;
            text-align: right;
        }
        .ttd-tgl { font-size: 7.5pt; color: #555; }
        .ttd-jabatan { font-size: 7.5pt; color: #555; margin-bottom: 18px; }
        .ttd-box {
            text-align: right;
            display: inline-block;
        }
        .ttd-box .garis {
            border-bottom: 1px solid #333;
            min-width: 130px;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .ttd-nama { font-weight: 700; font-size: 9pt; color: #154212; }
        .ttd-role { font-size: 7pt; color: #777; }

        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #ddd;
            font-size: 6.5pt;
            color: #aaa;
            text-align: center;
        }
    </style>
</head>
<body>

<table class="kop" style="width:100%;">
    <tr>
        <td style="width:55%;">
            <div class="kop-logo">WARUNG MIDORI</div>
            <div class="kop-sub">Laporan Menu Terlaris</div>
        </td>
        <td style="width:45%;">
            <div class="kop-info">
                Jl. Merdeka No. 108B, Bangli, Bali<br>
                Telp: 0812-3456-7890
            </div>
        </td>
    </tr>
</table>

<p class="judul">LAPORAN MENU TERLARIS</p>
<p class="periode">Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</p>

<table class="ringkasan">
    <tr>
        <td>
            <div class="ringkasan-label">Item Terjual</div>
            <div class="ringkasan-val">{{ number_format($totalOrderedAll, 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Pendapatan</div>
            <div class="ringkasan-val">Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Menu Tersedia</div>
            <div class="ringkasan-val">{{ count($products) }}</div>
        </td>
    </tr>
</table>

<div class="insight">
    <strong>{{ $products[0]['name'] }}</strong> menu #1 dengan {{ $products[0]['total_ordered'] }} porsi, menyumbang <strong>Rp {{ number_format($products[0]['total_revenue'], 0, ',', '.') }}</strong>.
    Kategori Makanan mendominasi 6 dari 10 menu terlaris.
</div>

<table class="data">
    <thead>
        <tr>
            <th style="width:5%;" class="tx-c">#</th>
            <th style="width:28%;">Menu</th>
            <th style="width:11%;">Kategori</th>
            <th style="width:12%;" class="tx-r">Harga</th>
            <th style="width:10%;" class="tx-c">Terjual</th>
            <th style="width:17%;" class="tx-r">Pendapatan</th>
            <th style="width:17%;" class="tx-r">Kontribusi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $p)
        <tr class="{{ $p['rank'] == 1 ? 'rank-emas' : ($p['rank'] == 2 ? 'rank-perak' : ($p['rank'] == 3 ? 'rank-perunggu' : '')) }}">
            <td class="tx-c">
                @if ($p['rank'] <= 3)
                    <span class="r-badge r-{{ $p['rank'] }}">{{ $p['rank'] }}</span>
                @else
                    <span class="r-badge r-n">{{ $p['rank'] }}</span>
                @endif
            </td>
            <td class="tx-b">{{ $p['name'] }}</td>
            <td>{{ $p['category'] }}</td>
            <td class="tx-r">Rp {{ number_format($p['price'], 0, ',', '.') }}</td>
            <td class="tx-c tx-b" style="color:#154212;">{{ $p['total_ordered'] }}&times;</td>
            <td class="tx-r tx-b">Rp {{ number_format($p['total_revenue'], 0, ',', '.') }}</td>
            <td class="tx-r">{{ number_format(($p['total_revenue'] / $totalRevenueAll) * 100, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="tx-c tx-b">TOTAL</td>
            <td class="tx-c tx-b">{{ number_format($totalOrderedAll, 0, ',', '.') }}</td>
            <td class="tx-r tx-b">Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}</td>
            <td class="tx-r tx-b">100%</td>
        </tr>
    </tfoot>
</table>

<div class="ttd">
    <div class="ttd-tgl">Bangli, {{ now()->translatedFormat('d F Y') }}</div>
    <div class="ttd-jabatan">Manajer Operasional</div>
    <div class="ttd-box">
        <div class="garis"></div>
        <div class="ttd-nama">I Made Adi Wiryawan</div>
        <div class="ttd-role">Manager</div>
    </div>
</div>

<div class="footer">
    Dicetak oleh Sistem Informasi Penjualan Warung Midori &mdash; {{ now()->format('d/m/Y H:i') }} WITA
</div>

</body>
</html>
