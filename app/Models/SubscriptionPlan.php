<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'billing_cycle',
        'max_listings',
        'can_feature',
        'can_three_way_swap',
        'description',
    ];

    protected $casts = [
        'price'              => 'decimal:2',
        'can_feature'        => 'boolean',
        'can_three_way_swap' => 'boolean',
    ];

    protected $appends = [
        'is_free',
    ];

    public function getIsFreeAttribute(): bool
    {
        return $this->price == 0;
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }
}