<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarageSaleItem extends Model
{
    protected $fillable = [
        'garage_sale_id', 'title', 'category', 'condition',
        'value', 'image_path', 'wants', 'swap_terms',
    ];

    public function sale()
    {
        return $this->belongsTo(GarageSale::class, 'garage_sale_id');
    }
}
