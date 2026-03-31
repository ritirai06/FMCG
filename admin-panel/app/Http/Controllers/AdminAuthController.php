<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        // Only redirect if admin is already logged in
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withInput()->with('error', 'Invalid email or password.');
        }

        $request->session()->regenerate();
        return $this->redirectByRole(Auth::user()->role);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|digits:10',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'delivery',   // self-registration is always delivery
            'status'   => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('delivery.panel.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(?string $role)
    {
        return match ($role) {
            'admin'    => redirect()->route('dashboard'),
            'sales'    => redirect()->route('sale.panel.dashboard'),
            'delivery' => redirect()->route('delivery.panel.dashboard'),
            default    => redirect()->route('login')->with('error', 'Unknown role. Contact administrator.'),
        };
    }
}
