<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function show($id)
    {
        $item = Item::with(['images', 'user'])->findOrFail($id);
        return view('pages.item', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'estimated_value'  => ['nullable', 'integer', 'min:0'],
            'category'         => ['required', 'string'],
            'condition'        => ['required', 'in:new,like_new,good,fair'],
            'location'         => ['nullable', 'string', 'max:255'],
            'looking_for'      => ['nullable', 'string', 'max:255'],
            'swap_conditions'  => ['nullable', 'array'],
            'images'           => ['nullable', 'array', 'max:5'],
            'images.*'         => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $item = Item::create([
            'user_id'          => Auth::id(),
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'estimated_value'  => $validated['estimated_value'] ?? null,
            'category'         => $validated['category'],
            'condition'        => $validated['condition'],
            'location'         => Auth::user()->location ?? $validated['location'] ?? null,
            'looking_for'      => $validated['looking_for'] ?? null,
            'swap_conditions'  => $validated['swap_conditions'] ?? [],
            'status'           => 'active',
            'latitude'         => $validated['latitude']  ?? null,
            'longitude'        => $validated['longitude'] ?? null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('items', 'public');
                ItemImage::create([
                    'item_id'    => $item->id,
                    'path'       => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'item'    => $item->load('images'),
        ]);
    }
}