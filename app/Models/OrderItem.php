<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id', // Thay đổi từ product_id thành product_variant_id
        'quantity',
        'price'
        // Loại bỏ 'total' vì không có trong database
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Get the product variant that belongs to the order item.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the product through product variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_variant_id', 'id');
    }

    /**
     * Calculate total for this item
     */
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
