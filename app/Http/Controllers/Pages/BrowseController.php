<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        /*
        |----------------------------------------------------------------------
        | TODO (Backend): Replace $listings with a real DB query.
        |
        | Accepts these query params from Vue/Axios:
        |   ?category=Electronics
        |   ?value_max=500
        |   ?distance=10
        |   ?item_type=Physical Good
        |   ?sort=recent   (recent | value_asc | value_desc | distance)
        |   ?page=1
        |
        | Expected shape per item (must match BrowsePage.vue):
        |   'id'        => int
        |   'title'     => string
        |   'image'     => string  (URL)
        |   'value'     => int
        |   'username'  => string
        |   'distance'  => float   (miles)
        |   'category'  => string
        |   'promoted'  => bool
        |
        | Example real query:
        |   $listings = Listing::with('user')
        |       ->when($request->category, fn($q, $c) => $q->where('category', $c))
        |       ->when($request->value_max, fn($q, $v) => $q->where('estimated_value', '<=', $v))
        |       ->where('status', 'active')
        |       ->latest()
        |       ->paginate(6);
        |----------------------------------------------------------------------
        */

        $listings = [
            [
                'id'       => 1,
                'title'    => 'Vintage Leica Leather Case',
                'image'    => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80',
                'value'    => 145,
                'username' => 'julian_design',
                'distance' => 1.2,
                'category' => 'Collectibles',
                'promoted' => true,
            ],
            [
                'id'       => 2,
                'title'    => 'Minimalist Series 4 Watch',
                'image'    => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80',
                'value'    => 210,
                'username' => 'marcus_k',
                'distance' => 0.8,
                'category' => 'Electronics',
                'promoted' => true,
            ],
            [
                'id'       => 3,
                'title'    => 'Studio Wireless Over-Ear',
                'image'    => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80',
                'value'    => 320,
                'username' => 'sarah_listening',
                'distance' => 3.5,
                'category' => 'Electronics',
                'promoted' => true,
            ],
            [
                'id'       => 4,
                'title'    => 'Mid-Century Accent Chair',
                'image'    => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80',
                'value'    => 180,
                'username' => 'the_reclaimed',
                'distance' => 2.1,
                'category' => 'Furniture',
                'promoted' => false,
            ],
            [
                'id'       => 5,
                'title'    => 'Custom Denim Jacket',
                'image'    => 'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=600&q=80',
                'value'    => 95,
                'username' => 'vintage_stitch',
                'distance' => 0.5,
                'category' => 'Clothing',
                'promoted' => false,
            ],
            [
                'id'       => 6,
                'title'    => 'Custom Mechanical Keyboard',
                'image'    => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=600&q=80',
                'value'    => 275,
                'username' => 'key_caps_inc',
                'distance' => 4.8,
                'category' => 'Electronics',
                'promoted' => false,
            ],
        ];

        return view('pages.browse', compact('listings'));
    }
}