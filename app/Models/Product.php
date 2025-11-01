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
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'product_type',
        'stock_quantity',
        'digital_file_path',
        'digital_file_size',
        'download_limit',
        'download_expiry_hours',
        'weight',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock_quantity' => 'integer',
            'digital_file_size' => 'integer',
            'download_limit' => 'integer',
            'download_expiry_hours' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the product.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    /**
     * Get the product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if product is physical.
     */
    public function isPhysical(): bool
    {
        return $this->product_type === 'physical';
    }

    /**
     * Check if product is digital.
     */
    public function isDigital(): bool
    {
        return $this->product_type === 'digital';
    }

    /**
     * Check if product is in stock.
     */
    public function inStock(): bool
    {
        if ($this->isDigital()) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include physical products.
     */
    public function scopePhysical($query)
    {
        return $query->where('product_type', 'physical');
    }

    /**
     * Scope a query to only include digital products.
     */
    public function scopeDigital($query)
    {
        return $query->where('product_type', 'digital');
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('product_type', 'digital')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Get formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 2);
    }

    /**
     * Get primary image URL or placeholder.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        $primaryImage = $this->primaryImage;
        if ($primaryImage) {
            return asset('storage/' . $primaryImage->image_path);
        }
        return asset('images/placeholder.jpg');
    }
}