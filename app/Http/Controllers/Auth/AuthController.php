<?php
// filepath: app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()    { return view('pages.auth'); }
    public function showRegister() { return view('pages.auth'); }

    /**
     * FAKE LOGIN — no database yet.
     * TODO (Backend): Replace with Auth::attempt() + real user lookup.
     *
     * Accepted fake credentials:
     *   email:    any@email.com
     *   password: password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($request->password !== 'password') {
            return response()->json([
                'errors' => ['email' => ['Invalid credentials. Use password "password" for testing.']]
            ], 422);
        }

        $fakeUser = [
            'id'       => 1,
            'name'     => 'John Doe',
            'username' => 'john.d142',
            'email'    => $request->email,
            'avatar'   => null,
            'is_new'   => true,
        ];

        Session::put('fake_user', $fakeUser);

        return response()->json([
            'redirect' => $fakeUser['is_new']
                ? route('onboarding.step1')
                : route('dashboard')
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|min:3|max:50',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        Session::put('fake_user', [
            'id'       => 1,
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'avatar'   => null,
            'is_new'   => true,
        ]);

        return response()->json([
            'redirect' => route('onboarding.step1')
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('fake_user');
        // TODO (Backend): Auth::logout(); $request->session()->invalidate();
        return redirect()->route('home');
    }
}