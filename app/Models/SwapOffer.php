<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'initiator_listing_id',
        'receiver_listing_id',
        'type',
        'status',
        'message',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected $appends = [
        'is_completed',
    ];

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    // --- Relationships ---

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function initiatorListing()
    {
        return $this->belongsTo(Listing::class, 'initiator_listing_id');
    }

    public function receiverListing()
    {
        return $this->belongsTo(Listing::class, 'receiver_listing_id');
    }

    public function chainParties()
    {
        return $this->hasMany(SwapChainParty::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}