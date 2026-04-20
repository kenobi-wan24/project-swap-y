<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function show(Request $request, $id)
    {
        // ── Replace this stub with a real DB/model lookup when ready ──────────
        $item = [
            'id'             => $id,
            'title'          => 'MacBook Pro 16" M2 Max (32GB RAM, 1TB SSD)',
            'category'       => 'Electronics',
            'condition'      => 'Mint Condition',
            'value'          => 2450,
            'distance'       => '12.4',
            'description'    => 'Barely used MacBook Pro, purchased 6 months ago. Comes with original box, charger, and AppleCare+ until 2026.',
            'promoted'       => true,
            'image'          => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&q=80',
            'images'         => [
                'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&q=80',
                'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400&q=80',
                'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&q=80',
                'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80',
            ],
            'owner_name'     => 'Marcus Chen',
            'owner_username' => 'mchen_tech',
            'owner_avatar'   => 'https://i.pravatar.cc/80?img=3',
            'owner_rating'   => 4.7,
            'wants_title'    => 'High-end Camera Gear',
            'wants_desc'     => 'Sony A7 series or equivalent lens kits',
            'wants_min'      => '2,200',
            'wants_max'      => '2,600',
            'match_score'    => 92,
            'match_label'    => "Matches your 'Sony A7R IV' listing",
        ];

        return view('pages.item', compact('item'));
    }
}