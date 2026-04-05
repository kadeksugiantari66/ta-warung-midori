<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: "DejaVu Sans", sans-serif;
    background: #ffffff;
    width: 595px;
    padding-top: 80px;
    text-align: center;
}
.card {
    width: 280px;
    margin: 0 auto;
    border: 1px solid #e1e3e1;
}
.header {
    background-color: #154212;
    text-align: center;
    padding: 18px 16px 16px;
}
.brand {
    font-size: 20px;
    font-weight: bold;
    color: #bcf0ae;
}
.brand-sub {
    font-size: 9px;
    color: #7ab87a;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 3px;
}
.accent {
    width: 28px;
    height: 2px;
    background-color: #ccf05f;
    margin: 10px auto 0;
}
.qr-section {
    background: #ffffff;
    text-align: center;
    padding: 18px 16px 14px;
}
.scan-label {
    font-size: 9px;
    font-weight: bold;
    color: #72796e;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}
.qr-img {
    width: 200px;
    height: 200px;
    display: block;
    margin: 0 auto;
    border: 1px solid #e1e3e1;
    padding: 6px;
}
.footer {
    background-color: #f2f4f2;
    text-align: center;
    padding: 14px 16px 18px;
    border-top: 1px solid #e1e3e1;
}
.meja-label {
    font-size: 9px;
    font-weight: bold;
    color: #72796e;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.meja-number {
    font-size: 52px;
    font-weight: bold;
    color: #154212;
    line-height: 1.1;
    margin-top: 2px;
}
.instruksi {
    font-size: 9px;
    color: #42493e;
    line-height: 1.6;
    margin-top: 8px;
}
.url {
    font-size: 7px;
    color: #72796e;
    margin-top: 8px;
    word-break: break-all;
}
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="brand">Warung Midori</div>
        <div class="brand-sub">Bangli &middot; Bali</div>
        <div class="accent"></div>
    </div>
    <div class="qr-section">
        <div class="scan-label">Scan untuk memesan</div>
        <img class="qr-img" src="{{ $qrBase64 }}" alt="QR Meja {{ $table->table_number }}"/>
    </div>
    <div class="footer">
        <div class="meja-label">Nomor Meja</div>
        <div class="meja-number">{{ $table->table_number }}</div>
        <div class="instruksi">Arahkan kamera HP ke QR Code<br/>untuk melihat menu &amp; memesan</div>
        <div class="url">{{ $orderUrl }}</div>
    </div>
</div>
</body>
</html>