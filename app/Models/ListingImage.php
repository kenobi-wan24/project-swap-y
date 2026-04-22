<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary'  => 'boolean',
        'sort_order'  => 'integer',
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}