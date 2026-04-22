<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'banner',
        'logo',
        'city',
        'province',
        'region',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'banner_url',
        'logo_url',
        'listing_count',
    ];

    // --- Appends ---

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getListingCountAttribute(): int
    {
        return $this->listings()->count();
    }

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}