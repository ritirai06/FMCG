<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthController extends Controller
{
    // login page
    public function showLogin()
    {
        return view('login');
    }

    // login process
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // attempt to find user with admin role
        $user = User::where('email', $request->email)->where('role','admin')->first();

        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Wrong password');
        }

        // log user in using Laravel auth
        Auth::login($user);

        // redirect to intended page (from auth middleware) or dashboard
        return redirect()->intended(route('dashboard'));
    }

    // logout
    public function logout()
    {
        Auth::logout();
        // forget any old session keys
        session()->forget(['admin_id', 'admin_name']);
        return redirect()->route('login');
    }
}
