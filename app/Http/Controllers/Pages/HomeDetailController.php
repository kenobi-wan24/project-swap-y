<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class HomeDetailController extends Controller
{
    public function show($id)
    {
        // Vue will use built-in demo data when home is empty
        // TODO: your friend replaces [] with real Home model query
        return view('pages.home-detail', ['home' => []]);
    }
}