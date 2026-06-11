<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Home;
use App\Models\GarageSale;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingActionController extends Controller
{
    private array $map = [
        'item'        => Item::class,
        'home'        => Home::class,
        'garage-sale' => GarageSale::class,
        'service'     => Service::class,
    ];

    /** Resolve the model and verify the current user owns it. */
    private function resolve(string $type, $id)
    {
        abort_unless(isset($this->map[$type]), 404);
        $model = $this->map[$type]::findOrFail($id);
        abort_unless($model->user_id === Auth::id(), 403);
        return $model;
    }

    /** Toggle whether a listing is promoted/boosted. */
    public function promote(string $type, $id)
    {
        $model = $this->resolve($type, $id);
        $model->is_promoted = ! $model->is_promoted;
        $model->save();

        return response()->json([
            'success'     => true,
            'is_promoted' => (bool) $model->is_promoted,
        ]);
    }

    /** Update the core editable fields of a listing. */
    public function update(Request $request, string $type, $id)
    {
        $model = $this->resolve($type, $id);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'in:active,paused,ended,draft'],
            'price'       => ['nullable', 'integer', 'min:0'],
            'category'    => ['nullable', 'string', 'max:100'],
        ]);

        $model->title = $data['title'];
        $model->description = $data['description'] ?? null;
        if (! empty($data['status'])) {
            $model->status = $data['status'];
        }

        switch ($type) {
            case 'item':
                if (array_key_exists('price', $data)) $model->estimated_value = $data['price'];
                if (! empty($data['category'])) $model->category = $data['category'];
                break;
            case 'home':
                if (array_key_exists('price', $data)) $model->value = $data['price'];
                if (! empty($data['category'])) $model->type = $data['category']; // Swap/Rent/Sell/Co-living
                break;
            case 'service':
                if (array_key_exists('price', $data)) $model->rate = $data['price'];
                if (! empty($data['category'])) $model->category = $data['category'];
                break;
            // garage-sale: title / description / status only
        }

        $model->save();

        return response()->json([
            'success'     => true,
            'title'       => $model->title,
            'description' => $model->description ?? '',
            'status'      => $model->status,
            'category'    => $this->categoryFor($type, $model),
            'price'       => $this->priceFor($type, $model),
            'price_label' => $this->priceLabel($type, $model),
        ]);
    }

    private function categoryFor(string $type, $model): string
    {
        return match ($type) {
            'home'        => $model->type,
            'garage-sale' => 'Garage Sale',
            default       => $model->category,
        };
    }

    private function priceFor(string $type, $model)
    {
        return match ($type) {
            'item'    => $model->estimated_value,
            'home'    => $model->value,
            'service' => $model->rate,
            default   => null,
        };
    }

    private function priceLabel(string $type, $model): ?string
    {
        $val = $this->priceFor($type, $model);
        if (! $val) return null;

        if ($type === 'home') {
            return $model->type === 'Sell'
                ? '₱' . number_format($val)
                : '₱' . number_format($val) . '/mo';
        }
        if ($type === 'service') {
            return '₱' . number_format($val) . ($model->rate_type ? ' · ' . $model->rate_type : '');
        }
        return '₱' . number_format($val);
    }
}
