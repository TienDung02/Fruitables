<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Tắt auto-incrementing vì sử dụng string ID
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_status',
        'shipping_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'notes',
        'payment_request_id',
        'payment_data',
        'transaction_id',
        'paid_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . $timestamp . '-' . $random;
    }
}
