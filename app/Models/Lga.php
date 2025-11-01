<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lga extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
    ];

    /**
     * Get the state that owns the LGA.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the addresses in this LGA.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}