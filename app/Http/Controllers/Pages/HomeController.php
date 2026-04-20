<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |----------------------------------------------------------------------
        | TODO (Backend): Replace $featuredItems with a real DB query.
        |
        | Expected shape per item:
        |   'id'     => int       — listing ID for the /item/{id} route
        |   'title'  => string    — listing title
        |   'image'  => string    — full image URL or storage path
        |   'value'  => int|float — estimated value in USD
        |   'wants'  => string    — what the owner wants in return
        |
        | Example query:
        |   $featuredItems = Listing::where('is_featured', true)
        |                           ->where('status', 'active')
        |                           ->latest()
        |                           ->take(6)
        |                           ->get(['id','title','image','estimated_value','wants'])
        |                           ->toArray();
        |----------------------------------------------------------------------
        */
        $featuredItems = [
            ['id' => 1, 'title' => 'Minimalist Chrono',   'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80', 'value' => 450, 'wants' => 'Vintage Camera'],
            ['id' => 2, 'title' => 'Retro Lens G2',       'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80', 'value' => 620, 'wants' => 'Studio Lights'],
            ['id' => 3, 'title' => 'Sonic Studio X',      'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80', 'value' => 380, 'wants' => 'Smart Tablet'],
            ['id' => 4, 'title' => '85mm Prime Lens',     'image' => 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&q=80', 'value' => 890, 'wants' => 'Full Body Frame'],
            ['id' => 5, 'title' => 'Artisan Boots',       'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80',  'value' => 210, 'wants' => 'Leather Jacket'],
            ['id' => 6, 'title' => '8-Channel Mixer',     'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&q=80', 'value' => 320, 'wants' => 'Condenser Mic'],
        ];

        return view('pages.home', compact('featuredItems'));
    }
}