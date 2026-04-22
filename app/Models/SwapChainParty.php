<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapChainParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'swap_offer_id',
        'user_id',
        'listing_id',
        'chain_position',
        'status',
    ];

    protected $casts = [
        'chain_position' => 'integer',
    ];

    public function swapOffer()
    {
        return $this->belongsTo(SwapOffer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}