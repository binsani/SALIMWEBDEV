<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_type',
        'price',
        'quantity',
        'subtotal',
        'digital_file_path',
        'download_limit',
        'download_expiry_hours',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
            'download_limit' => 'integer',
            'download_expiry_hours' => 'integer',
        ];
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the download record for this item.
     */
    public function download(): HasOne
    {
        return $this->hasOne(OrderDownload::class);
    }

    /**
     * Check if item is digital.
     */
    public function isDigital(): bool
    {
        return $this->product_type === 'digital';
    }

    /**
     * Check if item is physical.
     */
    public function isPhysical(): bool
    {
        return $this->product_type === 'physical';
    }

    /**
     * Get formatted subtotal with currency.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '₦' . number_format($this->subtotal, 2);
    }
}