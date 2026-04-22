<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingWant extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'category_id',
        'description',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}