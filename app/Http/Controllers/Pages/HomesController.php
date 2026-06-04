<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class HomesController extends Controller
{
    public function index()
    {
        // TODO: Replace with real query once homes table exists
        // $homes = Home::with('images', 'owner')
        //     ->where('status', 'active')
        //     ->latest()
        //     ->paginate(12);

        $homes = [];

        return view('pages.homes', compact('homes'));
    }
}