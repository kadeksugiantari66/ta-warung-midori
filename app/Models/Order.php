<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class Order extends Model
{
    protected $primaryKey = 'id_order';

    protected $fillable = ['id_meja', 'queue_number', 'customer_email', 'status', 'total_amount'];

    protected $casts = ['total_amount' => 'decimal:2'];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'id_meja', 'id_meja');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'id_order', 'id_order');
    }

    /**
     * Selesaikan pesanan: ubah status ke completed, bebaskan meja,
     * dan rotasi QR Code meja agar siap untuk pelanggan berikutnya.
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
        $meja = $this->table()->first();
        $meja->update(['status' => 'available']);
        $meja->generateQr();
    }

    /**
     * Kirim nota pesanan ke email pelanggan (jika diisi).
     * Kegagalan pengiriman tidak mengganggu alur pembayaran.
     */
    public function sendInvoice(): void
    {
        if (empty($this->customer_email)) {
            return;
        }

        try {
            Mail::to($this->customer_email)->send(new InvoiceMail($this));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
