<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Table extends Model
{
    protected $table = 'meja';

    protected $primaryKey = 'id_meja';

    protected $fillable = ['table_number', 'qr_code_path', 'qr_token', 'status'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_meja', 'id_meja');
    }

    /**
     * Generate & simpan QR Code dinamis dengan token acak baru.
     * Token lama menjadi tidak valid (rotasi) — mencegah pemesanan dari luar restoran
     * memakai QR hasil foto pelanggan sebelumnya.
     */
    public function generateQr(): void
    {
        // Set token dulu agar orderUrl() ikut memakai token BARU ini.
        $this->qr_token = Str::random(40);
        $path = "qrcodes/table_{$this->id_meja}.svg";

        // QR berisi URL LENGKAP beserta token — pemesanan hanya bisa lewat scan QR ini.
        $svg = QrCode::format('svg')->size(300)->generate($this->orderUrl());

        Storage::disk('public')->put($path, $svg);

        $this->update(['qr_code_path' => $path, 'qr_token' => $this->qr_token]);
    }

    /**
     * URL tujuan QR (halaman menu meja) beserta token-nya.
     * Root URL SELALU diambil dari config('app.url') (APP_URL), bukan host request,
     * supaya konsisten baik digenerate lewat web maupun CLI (seeder/command/tinker)
     * dan tidak ikut 'localhost' dari environment dev. Skema (https) dipastikan ada
     * supaya QR tetap valid meski APP_URL lupa diberi 'https://'. Token disertakan
     * agar akses manual (mengetik URL) tanpa scan ditolak.
     */
    public function orderUrl(): string
    {
        $base = rtrim((string) config('app.url'), '/');

        if (! str_starts_with($base, 'http://') && ! str_starts_with($base, 'https://')) {
            $base = 'https://'.$base;
        }

        return $base.route('order.menu', ['table' => $this->id_meja, 'token' => $this->qr_token], false);
    }
}
