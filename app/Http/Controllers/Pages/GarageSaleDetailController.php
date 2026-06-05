<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class GarageSaleDetailController extends Controller
{
    public function show($id)
    {
        // TODO: Replace with real query once GarageSale model exists
        // $sale = GarageSale::with(['seller', 'items.images'])
        //     ->findOrFail($id);
        // return view('pages.garage-sale-detail', ['sale' => $this->shape($sale)]);

        return view('pages.garage-sale-detail', ['sale' => []]);
    }
}