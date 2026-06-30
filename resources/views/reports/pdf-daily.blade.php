<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian – Warung Midori</title>
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

        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data thead th {
            background: #154212;
            color: #fff;
            padding: 6px 8px;
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
            margin-top: 18px;
            text-align: right;
        }
        .ttd-tgl { font-size: 7.5pt; color: #555; }
        .ttd-jabatan { font-size: 7.5pt; color: #555; margin-bottom: 22px; }
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
            margin-top: 10px;
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
            <div class="kop-sub">Laporan Penjualan Harian</div>
        </td>
        <td style="width:45%;">
            <div class="kop-info">
                Jl. Merdeka No. 108B, Bangli, Bali<br>
                Telp: 0812-3456-7890
            </div>
        </td>
    </tr>
</table>

<p class="judul">LAPORAN PENJUALAN HARIAN</p>
<p class="periode">Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>

<table class="ringkasan">
    <tr>
        <td>
            <div class="ringkasan-label">Pesanan</div>
            <div class="ringkasan-val">{{ $summary['total_orders'] }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Selesai</div>
            <div class="ringkasan-val">{{ $summary['completed'] }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Pendapatan</div>
            <div class="ringkasan-val">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="ringkasan-label">Tunai / Digital</div>
            <div class="ringkasan-val" style="font-size:10pt;">Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}</div>
            <div class="ringkasan-label" style="margin-top:1px;">Rp {{ number_format($summary['digital_revenue'], 0, ',', '.') }} Digital</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th style="width:8%;">Antrean</th>
            <th style="width:6%;">Meja</th>
            <th>Item</th>
            <th style="width:12%;" class="tx-r">Total</th>
            <th style="width:9%;" class="tx-c">Bayar</th>
            <th style="width:6%;" class="tx-c">Status</th>
            <th style="width:9%;" class="tx-r">Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td class="tx-b" style="color:#154212;">#{{ $order['queue'] }}</td>
            <td class="tx-b">{{ $order['table'] }}</td>
            <td>{{ $order['items'] }}</td>
            <td class="tx-r tx-b">Rp {{ number_format($order['total'], 0, ',', '.') }}</td>
            <td class="tx-c"><span class="badge {{ $order['method'] === 'Tunai' ? 'badge-h' : 'badge-b' }}">{{ $order['method'] }}</span></td>
            <td class="tx-c"><span class="badge badge-h">Selesai</span></td>
            <td class="tx-r">{{ $order['time'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="ttd">
    <div class="ttd-tgl">Bangli, {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</div>
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
