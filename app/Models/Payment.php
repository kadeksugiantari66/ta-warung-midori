<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $primaryKey = 'id_payment';

    protected $fillable = ['id_order', 'method', 'amount', 'status', 'transaction_id', 'snap_token'];

    protected $casts = ['amount' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
