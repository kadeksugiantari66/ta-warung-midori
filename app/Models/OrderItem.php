<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $primaryKey = 'id_order_item';

    protected $fillable = ['id_order', 'id_menu', 'quantity', 'note', 'subtotal', 'status'];

    protected $casts = ['subtotal' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_menu', 'id_menu');
    }
}
