<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingMatch extends Model
{
    use HasFactory;  
    
    protected $fillable = [
        'listing_id',
        'matched_listing_id',
        'score',
        'match_reasons',
    ];

    protected $casts = [
        'score'         => 'decimal:4',
        'match_reasons' => 'array',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function matchedListing()
    {
        return $this->belongsTo(Listing::class, 'matched_listing_id');
    }
}