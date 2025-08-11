<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
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
     * Get the Categories that owns the Products.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the cart items for the Products.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the order items for the Products.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the media files for the Products.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->ordered();
    }

    /**
     * Get the reviews for the Products.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews only.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->approved();
    }

    /**
     * Get the primary image for the Products.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->where('is_primary', true)->images();
    }

    /**
     * Get all images for the Products.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->images()->ordered();
    }

    /**
     * Get all videos for the Products.
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
     * Check if Products is on sale
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

    /**
     * Get the average rating for the product.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of reviews for the product.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get the rating distribution (count of each star rating 1-5).
     */
    public function getRatingDistributionAttribute(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->approvedReviews()->withRating($i)->count();
        }
        return $distribution;
    }

    /**
     * Check if the product has any reviews.
     */
    public function getHasReviewsAttribute(): bool
    {
        return $this->reviews_count > 0;
    }
}
