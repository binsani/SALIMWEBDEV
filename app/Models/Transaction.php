<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'reference',
        'paystack_reference',
        'amount',
        'currency',
        'status',
        'payment_channel',
        'paid_at',
        'authorization_code',
        'metadata',
        'webhook_processed',
        'paystack_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'webhook_processed' => 'boolean',
            'metadata' => 'array',
            'paystack_response' => 'array',
        ];
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that owns the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount / 100, 2);
    }

    /**
     * Scope for successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for unprocessed webhooks.
     */
    public function scopeUnprocessedWebhooks($query)
    {
        return $query->where('webhook_processed', false);
    }
}