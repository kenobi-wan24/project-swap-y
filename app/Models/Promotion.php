<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'type',
        'starts_at',
        'ends_at',
        'amount_paid',
        'payment_reference',
    ];

    protected $casts = [
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    protected $appends = [
        'is_active',
    ];

    public function getIsActiveAttribute(): bool
    {
        return now()->between($this->starts_at, $this->ends_at);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
