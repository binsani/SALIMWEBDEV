<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'download_token',
        'downloads_count',
        'expires_at',
        'last_downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'downloads_count' => 'integer',
            'expires_at' => 'datetime',
            'last_downloaded_at' => 'datetime',
        ];
    }

    /**
     * Get the order item that owns the download.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the user that owns the download.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if download has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Check if download limit reached.
     */
    public function limitReached(): bool
    {
        $limit = $this->orderItem->download_limit;
        if (!$limit) {
            return false;
        }
        return $this->downloads_count >= $limit;
    }

    /**
     * Check if download is valid (not expired and limit not reached).
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->limitReached();
    }

    /**
     * Increment download count and update last downloaded timestamp.
     */
    public function recordDownload(): void
    {
        $this->increment('downloads_count');
        $this->update(['last_downloaded_at' => now()]);
    }
}