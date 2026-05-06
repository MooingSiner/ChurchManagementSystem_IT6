<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showlogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'username' => "Too many login attempts. Try again in {$seconds} seconds.",
            ])->onlyInput('username');
        }

        $administratorRow = DB::table('administrators')->where('username', $request->username)->first();

        if (! $administratorRow || ! Hash::check($request->password, $administratorRow->password)) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'username' => 'Invalid credentials.',
            ])->onlyInput('username');
        }

        RateLimiter::clear($throttleKey);
        Auth::login(Administrator::findOrFail($administratorRow->administrator_id));
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
