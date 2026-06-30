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
        $token = Str::random(40);
        $path = "qrcodes/table_{$this->id_meja}.svg";

        $url = route('order.menu', ['table' => $this->id_meja, 'token' => $token]);
        $svg = QrCode::format('svg')->size(300)->generate($url);

        Storage::disk('public')->put($path, $svg);

        $this->update(['qr_code_path' => $path, 'qr_token' => $token]);
    }
}
