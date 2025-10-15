<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'street_address',
        'city',
        'state_id',
        'lga_id',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the state for this address.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the LGA for this address.
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    /**
     * Get formatted full address.
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            "%s, %s, %s, %s State",
            $this->street_address,
            $this->city,
            $this->lga->name ?? '',
            $this->state->name ?? ''
        );
    }
}