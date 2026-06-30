<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $primaryKey = 'id_review';

    protected $fillable = ['id_menu', 'rating', 'comment'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_menu', 'id_menu');
    }
}
