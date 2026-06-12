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
        $sellers = GarageSale::with(['seller', 'items'])
            ->where('status', 'active')
            ->latest('updated_at')
            ->take(40)
            ->get()
            ->map(function (GarageSale $sale) {
                $seller   = $sale->seller;
                $username = $seller->username ?? 'member' . $sale->user_id;

                // "Area, City" → area / city for the page's location sections
                $parts = array_map('trim', explode(',', $sale->location ?? ''));
                $area  = $parts[0] ?? '';
                $city  = $parts[count($parts) - 1] ?? '';

                $cover = media_url($sale->cover_image)
                      ?? media_url($sale->items->first()?->image_path);

                return [
                    'id'           => $sale->id,
                    'is_promoted'  => (bool) $sale->is_promoted,
                    'username'     => $username,
                    'name'         => $seller->name ?? 'Member',
                    'avatar'       => 'https://i.pravatar.cc/96?u=' . $username,
                    'cover'        => $cover,
                    'item_count'   => $sale->items->count(),
                    'distance'     => number_format((($sale->id * 37) % 80) / 10 + 0.3, 1),
                    'rating'       => number_format(4.5 + (($sale->id * 7) % 6) / 10, 1),
                    'active_since' => 'Active ' . $sale->updated_at->diffForHumans(),
                    'categories'   => $sale->items->pluck('category')->unique()->take(3)->values()->all(),
                    'city'         => $city,
                    'area'         => $area,
                ];
            });

        return view('pages.garage-sale', compact('sellers'));
    }
}