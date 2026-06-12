<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemImage;
use App\Support\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function create()
    {
        return view('pages.item-create');
    }

    public function show($id)
    {
        $item  = Item::with(['images', 'user'])->findOrFail($id);
        $owner = $item->user;

        $images = $item->images
            ->sortByDesc('is_primary')
            ->map(fn ($img) => media_url($img->path))
            ->values()
            ->all();

        $lookingFor = collect(preg_split('/[,\n]+/', $item->looking_for ?? ''))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        $initials = collect(explode(' ', trim($owner->name ?? 'Member')))
            ->filter()
            ->take(2)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
            ->implode('');

        $data = [
            'id'          => $item->id,
            'title'       => $item->title,
            'category'    => $item->category,
            'condition'   => ucwords(str_replace('_', ' ', $item->condition ?? '')),
            'description' => $item->description,
            'value'       => $item->estimated_value,
            'location'    => $item->location,
            'looking_for' => $lookingFor,
            'images'      => $images,
            'created_at'  => $item->created_at->diffForHumans(),
            'is_promoted' => (bool) $item->is_promoted,
            'is_own'      => auth()->id() === $item->user_id,
            'owner'       => [
                'name'         => $owner->name ?? 'Member',
                'username'     => $owner->username ?? null,
                'initials'     => $initials ?: 'M',
                'member_since' => optional($owner->created_at)->format('M Y'),
            ],
        ];

        return view('pages.item', ['item' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'estimated_value'  => ['nullable', 'integer', 'min:0'],
            'category'         => ['required', 'string', Rule::in(Categories::ITEMS_WITH_OTHER)],
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