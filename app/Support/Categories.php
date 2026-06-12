<?php

namespace App\Support;

/**
 * Canonical item categories. Single source of truth shared by the
 * item create form, browse filters, onboarding preferences, and the
 * match scorer. Mirrored in resources/js/constants/categories.js.
 */
final class Categories
{
    public const ITEMS = [
        'Electronics',
        'Clothing',
        'Collectibles',
        'Furniture',
        'Books',
        'Outdoor',
        'Gaming',
        'Photography',
        'Home',
        'Fashion',
    ];

    public const ITEMS_WITH_OTHER = [...self::ITEMS, 'Other'];
}
