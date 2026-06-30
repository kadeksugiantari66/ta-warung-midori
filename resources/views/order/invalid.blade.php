<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>QR Tidak Valid – Warung Midori</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 1,'wght' 400;}</style>
</head>
<body class="bg-[#f8faf8] font-[Inter] text-[#191c1b] min-h-screen flex items-center justify-center px-5">
    <div class="max-w-sm w-full text-center bg-white rounded-[2rem] shadow-[0_8px_32px_rgba(21,66,18,0.1)] p-8">
        <div class="w-16 h-16 mx-auto rounded-full bg-[#ffdad6] flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-3xl" style="color:#ba1a1a">qr_code_scanner</span>
        </div>
        <h1 class="font-[Noto_Serif] font-black text-2xl text-[#154212] mb-2">QR Code Tidak Valid</h1>
        <p class="text-sm text-[#42493e] leading-relaxed">
            QR Code yang Anda gunakan sudah kedaluwarsa atau tidak sesuai.
            Untuk memesan, silakan <strong>scan ulang QR Code terbaru</strong> yang tertera di
            @if(isset($table)) meja <strong>{{ $table->table_number }}</strong> @else meja Anda @endif.
        </p>
        <p class="text-xs text-[#72796e] mt-5">Pemesanan hanya dapat dilakukan dari dalam Warung Midori.</p>
    </div>
</body>
</html>
