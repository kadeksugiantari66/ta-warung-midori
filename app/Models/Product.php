<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'menu';

    protected $primaryKey = 'id_menu';

    protected $fillable = ['id_category', 'name', 'description', 'price', 'image', 'is_available'];

    protected $casts = ['is_available' => 'boolean', 'price' => 'decimal:2'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_menu', 'id_menu');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_menu', 'id_menu');
    }
}
