<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SalesPerson;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\AdminSetting;

class AuthController extends Controller
{
    private function hasUserColumn(string $column): bool
    {
        return Schema::hasColumn('users', $column);
    }

    public function showRegister()
    {
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'FMCG';
        return view('sale.register', compact('companySettings', 'companyName'));
    }

    public function showLogin()
    {
        // Only redirect if a sales/admin user is already logged in
        if (Auth::check() && in_array(Auth::user()->role, ['sales', 'admin'])) {
            return redirect()->route('sale.panel.dashboard');
        }
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'FMCG';
        return view('sale.login', compact('companySettings', 'companyName'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|digits:10',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        if ($this->hasUserColumn('role')) {
            $payload['role'] = 'sales';
        }
        if ($this->hasUserColumn('status')) {
            $payload['status'] = true;
        }

        if (Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $request->phone;
        }
        if (Schema::hasColumn('users', 'mobile')) {
            $payload['mobile'] = $request->phone;
        }

        $user = User::create($payload);

        // Keep sales master in sync for order/store mapping and OTP flow.
        $salesPerson = SalesPerson::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if (!$salesPerson) {
            SalesPerson::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'Active',
            ]);
        } else {
            $salesPerson->name = $request->name;
            $salesPerson->email = $request->email;
            $salesPerson->phone = $request->phone;
            $salesPerson->status = $salesPerson->status ?: 'Active';
            $salesPerson->save();
        }

        // Do not auto-login sales user after registration.
        // Redirect them to the sale login page so they can authenticate explicitly.
        return redirect()->route('sale.login')->with('success', 'Registration successful. Please sign in to continue.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        $user = Auth::user();

        // Only sales (or admin) can access sales panel
        if (!in_array($user->role, ['sales', 'admin'])) {
            Auth::logout();
            return back()->withInput()->with('error', 'This account does not have sales access.');
        }

        $request->session()->regenerate();
        return redirect()->route('sale.panel.dashboard');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10'
        ]);

        $mobile = $request->mobile;

        // check sales_persons table for the phone
        $sales = SalesPerson::where('phone', $mobile)->first();
        if (!$sales) {
            return response()->json(['success' => false, 'message' => 'Mobile number not registered'], 422);
        }

        $otp = random_int(100000, 999999);
        Cache::put('sale_otp:'.$mobile, $otp, now()->addMinutes(5));

        // TODO: integrate SMS provider here. For now log the OTP for debugging.
        Log::info('Sale OTP for '.$mobile.': '.$otp);

        return response()->json(['success' => true, 'message' => 'OTP sent (check logs in dev)']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6'
        ]);

        $mobile = $request->mobile;
        $otp = $request->otp;

        $cached = Cache::get('sale_otp:'.$mobile);
        if (!$cached || (string)$cached !== (string)$otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 422);
        }

        // find sales person
        $sales = SalesPerson::where('phone', $mobile)->first();
        if (!$sales) {
            return response()->json(['success' => false, 'message' => 'Mobile number not registered'], 422);
        }

        // find or create mapped sales user
        $user = null;
        if (!empty($sales->email)) {
            $user = User::where('email', $sales->email)->first();
        }
        if (!$user) {
            $user = User::query()
                ->when($this->hasUserColumn('role'), fn ($q) => $q->where('role', 'sales'))
                ->where(function ($query) use ($mobile) {
                    $applied = false;
                    if (Schema::hasColumn('users', 'phone')) {
                        $query->where('phone', $mobile);
                        $applied = true;
                    }
                    if (Schema::hasColumn('users', 'mobile')) {
                        if ($applied) {
                            $query->orWhere('mobile', $mobile);
                        } else {
                            $query->where('mobile', $mobile);
                        }
                    }
                })
                ->first();
        }
        if (!$user) {
            $email = !empty($sales->email) ? $sales->email : ('sale_' . $sales->id . '@local');
            $payload = [
                'name' => $sales->name ?? 'Sale User',
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
            ];
            if ($this->hasUserColumn('role')) {
                $payload['role'] = 'sales';
            }
            if ($this->hasUserColumn('status')) {
                $payload['status'] = true;
            }
            if (Schema::hasColumn('users', 'phone')) {
                $payload['phone'] = $mobile;
            }
            if (Schema::hasColumn('users', 'mobile')) {
                $payload['mobile'] = $mobile;
            }
            $user = User::create([
                ...$payload,
            ]);
        }

        Cache::forget('sale_otp:'.$mobile);
        Auth::login($user);

        return response()->json(['success' => true, 'redirect' => route('sale.dashboard')]);
    }
}
