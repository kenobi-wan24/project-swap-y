<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO (Backend): Replace with Auth::user()
        $user = Session::get('fake_user', [
            'id'       => 1,
            'name'     => 'Guest User',
            'username' => 'guest',
            'email'    => 'guest@swapy.com',
            'avatar'   => null,
        ]);

        return view('pages.dashboard', compact('user'));
    }
}