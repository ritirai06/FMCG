<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function parseGeolocation(?string $geolocation): array
    {
        $raw = trim((string) $geolocation);
        if ($raw === '' || !str_contains($raw, ',')) {
            return [null, null];
        }

        [$latRaw, $lngRaw] = array_map('trim', explode(',', $raw, 2));
        if (!is_numeric($latRaw) || !is_numeric($lngRaw)) {
            return [null, null];
        }

        $lat = (float) $latRaw;
        $lng = (float) $lngRaw;
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return [null, null];
        }

        return [$lat, $lng];
    }

    private function syncStoreFromCustomer(Customer $customer): void
    {
        [$lat, $lng] = $this->parseGeolocation($customer->geolocation);
        $status = strtolower((string) $customer->status) === 'inactive' ? 0 : 1;

        $payload = [
            'store_name' => $customer->business_name,
            'code' => $customer->code,
            'manager' => $customer->contact_person,
            'phone' => $customer->mobile,
            'address' => $customer->shipping_address ?: $customer->billing_address,
            'status' => $status,
            'latitude' => $lat,
            'longitude' => $lng,
        ];

        $storeQuery = Store::query();
        if (!empty($customer->code)) {
            $storeQuery->where('code', $customer->code);
        } else {
            $storeQuery->where('store_name', $customer->business_name);
            if (!empty($customer->mobile)) {
                $storeQuery->where('phone', $customer->mobile);
            }
        }

        $store = $storeQuery->first();
        if ($store) {
            $store->update($payload);
        } else {
            Store::create($payload);
        }
    }

    public function index(Request $request)
    {
        $query = Customer::with('createdBy')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%");
            });
        }

        if ($role = $request->get('created_by_role')) {
            if ($role === 'admin') {
                $query->where(function ($q) {
                    $q->whereNull('created_by')
                      ->orWhereHas('createdBy', fn($q2) => $q2->where('role', 'admin'));
                });
            } elseif ($role === 'sales') {
                $query->whereHas('createdBy', fn($q2) => $q2->where('role', 'sales'));
            }
        }

        $customers = $query->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'mobile'        => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'geolocation'   => 'nullable|string|max:100',
            'gstin'         => 'nullable|string|max:20',
            'document'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['_token', 'document']);
        $data['verified']   = $request->has('verified') ? 1 : 0;
        $data['created_by'] = auth()->id();

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('customers', 'public');
        }

        $customer = Customer::create($data);
        $this->syncStoreFromCustomer($customer);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'mobile'        => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'geolocation'   => 'nullable|string|max:100',
            'gstin'         => 'nullable|string|max:20',
            'document'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'document']);
        $data['verified'] = $request->has('verified') ? 1 : 0;

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('customers', 'public');
        }

        $customer->update($data);
        $this->syncStoreFromCustomer($customer->fresh());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
