<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private array $roles = ['admin', 'sales', 'delivery'];

    public function index(Request $request)
    {
        $query = User::query();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => User::count(),
            'admin'    => User::where('role', 'admin')->count(),
            'sales'    => User::where('role', 'sales')->count(),
            'delivery' => User::where('role', 'delivery')->count(),
        ];

        return view('users.index', compact('users', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => ['required', Rule::in($this->roles)],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => true,
        ]);

        return back()->with('success', "User '{$request->name}' created successfully.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'role'     => ['required', Rule::in($this->roles)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Prevent demoting the last admin
        if ($user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot change role — this is the only admin account.');
            }
        }

        $payload = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if (filled($request->password)) {
            $payload['password'] = Hash::make($request->password);
        }

        $user->update($payload);

        return back()->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-delete
        if ((int) $id === (int) Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the only admin account.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User '{$name}' deleted.");
    }
}
