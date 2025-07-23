<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'category_id',
        'weight',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the media files for the product.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->ordered();
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->where('is_primary', true)->images();
    }

    /**
     * Get all images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->images()->ordered();
    }

    /**
     * Get all videos for the product.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->videos()->ordered();
    }

    /**
     * Get the effective price (sale price if available, otherwise regular price)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if product is on sale
     */
    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    /**
     * Get the primary image URL.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primaryImage = $this->media()->primary()->images()->first();
        return $primaryImage ? $primaryImage->url : null;
    }

    /**
     * Get the primary image thumbnail URL.
     */
    public function getPrimaryImageThumbnailAttribute(): ?string
    {
        $primaryImage = $this->media()->primary()->images()->first();
        return $primaryImage ? $primaryImage->thumbnail_url : null;
    }

    /**
     * Get all image URLs as array.
     */
    public function getImageUrlsAttribute(): array
    {
        return $this->images->pluck('url')->toArray();
    }

    /**
     * Get all thumbnail URLs as array.
     */
    public function getThumbnailUrlsAttribute(): array
    {
        return $this->images->pluck('thumbnail_url')->toArray();
    }
}
