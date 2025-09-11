<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'total_quantity',
        'session_id',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'total_quantity' => 'integer',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for this cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
