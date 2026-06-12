<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\GarageSale;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Public profile — no dedicated page yet, so send visitors to the
     * member's garage sale storefront when one exists.
     */
    public function show(string $username)
    {
        return $this->store($username);
    }

    /**
     * Garage sale storefront: /store/{username} → the member's active sale.
     */
    public function store(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $sale = GarageSale::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('updated_at')
            ->first();

        if ($sale) {
            return redirect()->route('garage-sale.show', $sale->id);
        }

        return redirect()->route('garage-sale')
            ->with('status', $user->name . ' has no active garage sale right now.');
    }

    public function myProfile()
    {
        return redirect()->route('dashboard');
    }
}
