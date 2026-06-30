<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan – Warung Midori</title>
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
            width: 25%;
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

        .seksi {
            font-size: 8pt;
            font-weight: 900;
            color: #154212;
            margin: 10px 0 4px;
            padding-bottom: 3px;
            border-bottom: 1px solid #154212;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
            padding: 4px 8px;
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
        .tx-r { text-align: right; }
        .tx-c { text-align: center; }
        .tx-b { font-weight: 700; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 6pt;
            font-weight: 700;
        }
        .badge-h { background: #dcfce7; color: #166534; }
        .badge-b { background: #dbeafe; color: #1e40af; }

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
            <div class="kop-sub">Laporan Penjualan Bulanan</div>
        </td>
        <td style="width:45%;">
            <div class="kop-info">
                Jl. Merdeka No. 108B, Bangli, Bali<br>
                Telp: 0812-3456-7890
            </div>
        </td>
    </tr>
</table>

<p class="judul">LAPORAN PENJUALAN BULANAN</p>
<p class="periode">Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</p>

<table class="ringkasan">
    <tr>
        <td>
            <div class="ringkasan-label">Total Pesanan</div>
            <div class="ringkasan-val">{{ $summary['total_orders'] }}</div>
            <div class="ringkasan-label" style="margin-top:1px;">Selesai: {{ $summary['completed'] }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Pendapatan</div>
            <div class="ringkasan-val">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Tunai</div>
            <div class="ringkasan-val" style="font-size:10pt;">Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Digital</div>
            <div class="ringkasan-val" style="font-size:10pt;">Rp {{ number_format($summary['digital_revenue'], 0, ',', '.') }}</div>
        </td>
    </tr>
</table>

<p class="seksi">Rekap Harian</p>
<table class="data">
    <thead>
        <tr>
            <th style="width:8%;" class="tx-c">No</th>
            <th style="width:22%;">Tanggal</th>
            <th style="width:10%;" class="tx-c">Pesanan</th>
            <th style="width:22%;" class="tx-r">Pendapatan</th>
            <th style="width:18%;" class="tx-r">Rata-rata</th>
            <th style="width:20%;" class="tx-c">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dailyTrend as $i => $day)
        <tr>
            <td class="tx-c">{{ $i + 1 }}</td>
            <td class="tx-b">{{ \Carbon\Carbon::parse($day['date'])->translatedFormat('d M Y') }}</td>
            <td class="tx-c">{{ $day['total_orders'] }}</td>
            <td class="tx-r tx-b">Rp {{ number_format($day['revenue'], 0, ',', '.') }}</td>
            <td class="tx-r">Rp {{ number_format($day['total_orders'] > 0 ? intval($day['revenue'] / $day['total_orders']) : 0, 0, ',', '.') }}</td>
            <td class="tx-c"><span class="badge badge-h">Selesai</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="tx-c tx-b">TOTAL</td>
            <td class="tx-c">{{ collect($dailyTrend)->sum('total_orders') }}</td>
            <td class="tx-r tx-b">Rp {{ number_format(collect($dailyTrend)->sum('revenue'), 0, ',', '.') }}</td>
            <td class="tx-r">Rp {{ number_format(collect($dailyTrend)->sum('total_orders') > 0 ? intval(collect($dailyTrend)->sum('revenue') / collect($dailyTrend)->sum('total_orders')) : 0, 0, ',', '.') }}</td>
            <td class="tx-c"><span class="badge badge-h">{{ $summary['completed'] }} OK</span></td>
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
