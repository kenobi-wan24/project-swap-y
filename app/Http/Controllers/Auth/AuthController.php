<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Show login/register page ──────────────────────────────────────────────

    public function showLogin()
    {
        return view('pages.auth');
    }

    public function showRegister()
    {
        return view('pages.auth');
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return response()->json([
            'redirect' => route('onboarding.step1'),
        ]);
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            return response()->json([
                'errors' => [
                    'email' => ['These credentials do not match our records.'],
                ]
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'redirect' => route('dashboard'),
        ]);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // ── Username availability check ───────────────────────────────────────────

    public function checkUsername(Request $request)
    {
        $username = $request->query('username', '');

        if (strlen($username) < 3) {
            return response()->json(['available' => false, 'message' => 'Too short.']);
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json(['available' => false, 'message' => 'Only letters, numbers, and underscores.']);
        }

        $taken = User::where('username', $username)->exists();

        return response()->json([
            'available' => !$taken,
        ]);
    }
}