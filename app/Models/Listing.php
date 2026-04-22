<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'store_id',
        'title',
        'slug',
        'description',
        'condition',
        'estimated_value',
        'price',
        'is_for_swap',
        'is_for_sale',
        'is_service',
        'is_garage_sale',
        'status',
        'is_featured',
        'views_count',
        'latitude',
        'longitude',
        'address',
        'city',
        'province',
        'region',
        'zip_code',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'price'           => 'decimal:2',
        'latitude'        => 'decimal:7',
        'longitude'       => 'decimal:7',
        'is_for_swap'     => 'boolean',
        'is_for_sale'     => 'boolean',
        'is_service'      => 'boolean',
        'is_garage_sale'  => 'boolean',
        'is_featured'     => 'boolean',
    ];

    protected $appends = [
        'primary_image_url',
        'type_label',
    ];

    // --- Appends ---

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->images()->where('is_primary', true)->first()
                ?? $this->images()->first();

        return $primary ? asset('storage/' . $primary->image_path) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        $types = [];
        if ($this->is_for_swap)    $types[] = 'Swap';
        if ($this->is_for_sale)    $types[] = 'Sale';
        if ($this->is_service)     $types[] = 'Service';
        if ($this->is_garage_sale) $types[] = 'Garage Sale';

        return implode(' / ', $types) ?: 'Unlisted';
    }

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->hasMany(ListingTag::class);
    }

    public function wants()
    {
        return $this->hasMany(ListingWant::class);
    }

    public function swapOffersAsInitiator()
    {
        return $this->hasMany(SwapOffer::class, 'initiator_listing_id');
    }

    public function swapOffersAsReceiver()
    {
        return $this->hasMany(SwapOffer::class, 'receiver_listing_id');
    }

    public function matches()
    {
        return $this->hasMany(ListingMatch::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }
}