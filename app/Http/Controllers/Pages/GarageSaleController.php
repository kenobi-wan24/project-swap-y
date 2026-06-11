<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\GarageSale;
use App\Models\GarageSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GarageSaleController extends Controller
{
    public function create()
    {
        return view('pages.garage-sale-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:500',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'cover_image' => 'nullable|image|max:10240',
            'items'       => 'nullable|array',
            'items.*.title'     => 'required_with:items|string|max:255',
            'items.*.category'  => 'required_with:items|string',
            'items.*.condition' => 'required_with:items|in:new,like_new,good,fair',
            'items.*.value'     => 'nullable|integer|min:0',
            'items.*.wants'     => 'nullable|string|max:255',
            'items.*.swap_terms'=> 'nullable|string',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('garage-sales/covers', 'public');
        }

        $sale = GarageSale::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'cover_image' => $coverPath,
            'location'    => $request->location,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'status'      => 'active',
        ]);

        $itemImages = $request->file('item_images') ?? [];

        foreach ($request->input('items', []) as $index => $itemData) {
            $imagePath = null;
            if (isset($itemImages[$index])) {
                $imagePath = $itemImages[$index]->store('garage-sales/items', 'public');
            }

            GarageSaleItem::create([
                'garage_sale_id' => $sale->id,
                'title'          => $itemData['title'],
                'category'       => $itemData['category'],
                'condition'      => $itemData['condition'],
                'value'          => $itemData['value'] ?? 0,
                'image_path'     => $imagePath,
                'wants'          => $itemData['wants'] ?? null,
                'swap_terms'     => $itemData['swap_terms'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'sale'    => $sale->load('items'),
        ]);
    }

    public function index()
    {
        $sellers = [
            [
                'username'     => 'julian_design',
                'name'         => 'Julian M.',
                'avatar'       => 'https://i.pravatar.cc/80?img=1',
                'cover'        => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=600&q=80',
                'item_count'   => 14,
                'distance'     => '1.2',
                'rating'       => 4.9,
                'active_since' => 'Active today',
                'categories'   => ['Electronics', 'Cameras', 'Accessories'],
            ],
            [
                'username'     => 'marcus_k',
                'name'         => 'Marcus K.',
                'avatar'       => 'https://i.pravatar.cc/80?img=3',
                'cover'        => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80',
                'item_count'   => 8,
                'distance'     => '0.8',
                'rating'       => 4.7,
                'active_since' => 'Active today',
                'categories'   => ['Watches', 'Jewelry', 'Clothing'],
            ],
            [
                'username'     => 'sarah_listening',
                'name'         => 'Sarah L.',
                'avatar'       => 'https://i.pravatar.cc/80?img=5',
                'cover'        => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80',
                'item_count'   => 21,
                'distance'     => '3.5',
                'rating'       => 5.0,
                'active_since' => 'Active 2h ago',
                'categories'   => ['Audio', 'Electronics', 'Books'],
            ],
            [
                'username'     => 'alex_rides',
                'name'         => 'Alex R.',
                'avatar'       => 'https://i.pravatar.cc/80?img=11',
                'cover'        => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80',
                'item_count'   => 6,
                'distance'     => '5.1',
                'rating'       => 4.6,
                'active_since' => 'Active yesterday',
                'categories'   => ['Outdoor', 'Sports', 'Bikes'],
            ],
            [
                'username'     => 'david_k',
                'name'         => 'David K.',
                'avatar'       => 'https://i.pravatar.cc/80?img=7',
                'cover'        => 'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=600&q=80',
                'item_count'   => 11,
                'distance'     => '7.3',
                'rating'       => 4.8,
                'active_since' => 'Active 3h ago',
                'categories'   => ['Gaming', 'Electronics', 'PC Parts'],
            ],
            [
                'username'     => 'mchen_tech',
                'name'         => 'Marcus Chen',
                'avatar'       => 'https://i.pravatar.cc/80?img=3',
                'cover'        => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=600&q=80',
                'item_count'   => 3,
                'distance'     => '9.0',
                'rating'       => 4.7,
                'active_since' => 'Active today',
                'categories'   => ['Laptops', 'Gadgets'],
            ],
        ];

        return view('pages.garage-sale', compact('sellers'));
    }
}