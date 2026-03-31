<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryPerson;
use App\Models\DeliveryPartnerLocality;
use App\Models\City;
use App\Models\Locality;
use App\Models\Order;

class DeliveryPersonController extends Controller
{
    public function index()
    {
        $partners = DeliveryPerson::with(['cities', 'localities'])->latest()->get();
        $cities = City::with('localities')->get();
        $allOrders = Order::with('store')
            ->whereNotIn('status', ['Delivered', 'Cancelled'])
            ->latest()->get(['id','order_number','store_id','customer_name','status','assigned_delivery_person_id']);

        foreach ($partners as $partner) {
            $assignedOrders = Order::where('assigned_delivery_person_id', $partner->id)
                ->whereNotIn('status', ['Delivered', 'Cancelled'])
                ->get(['id','order_number','status','total_amount','amount']);

            $partner->order_total = $assignedOrders->sum(fn($o) => (float)($o->total_amount ?? $o->amount ?? 0));
            $partner->order_count = $assignedOrders->count();
            $partner->assigned_orders = $assignedOrders;

            // Build zone label: "CityName > LocalityName" or just "CityName"
            $zoneLabels = collect();
            foreach ($partner->localities as $loc) {
                $zoneLabels->push(($loc->city->name ?? '') . ' › ' . $loc->name);
            }
            foreach ($partner->cities as $city) {
                // Only add city-level if no localities from that city
                if (!$partner->localities->contains(fn($l) => $l->city_id == $city->id)) {
                    $zoneLabels->push($city->name);
                }
            }
            $partner->zone_labels = $zoneLabels->unique()->values();
        }

        return view('delivery.index', compact('partners', 'cities', 'allOrders'));
    }

    public function details($id)
    {
        $p = DeliveryPerson::findOrFail($id);
        
        $assignedCities = DeliveryPartnerLocality::where('delivery_partner_id', $id)
            ->whereNull('locality_id')
            ->pluck('city_id')
            ->toArray();
        
        $assignedLocalities = DeliveryPartnerLocality::where('delivery_partner_id', $id)
            ->whereNotNull('locality_id')
            ->pluck('locality_id')
            ->toArray();
        
        $p->city_ids = $assignedCities;
        $p->locality_ids = $assignedLocalities;
        
        return response()->json($p);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'vehicle' => 'nullable|string',
            'vehicle_number' => 'nullable|string|max:50',
            'status' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('delivery_persons', 'public');
        }

        $p = DeliveryPerson::create($data);
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $p]);
        }
        return redirect()->back()->with('success','Delivery person added');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:delivery_persons,id',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'vehicle' => 'nullable|string',
            'vehicle_number' => 'nullable|string|max:50',
            'status' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $p = DeliveryPerson::findOrFail($data['id']);
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('delivery_persons', 'public');
        }
        $p->update($data);
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $p]);
        }
        return redirect()->back()->with('success','Updated');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:delivery_persons,id']);
        DeliveryPerson::destroy($data['id']);
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->back()->with('success','Deleted');
    }

    public function assignZones(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:delivery_persons,id',
            'city_ids' => 'nullable|array',
            'city_ids.*' => 'integer|exists:cities,id',
            'locality_ids' => 'nullable|array',
            'locality_ids.*' => 'integer|exists:localities,id'
        ]);
        
        $partnerId = $data['id'];
        
        DeliveryPartnerLocality::where('delivery_partner_id', $partnerId)->delete();
        
        if (!empty($data['city_ids'])) {
            foreach ($data['city_ids'] as $cityId) {
                DeliveryPartnerLocality::create([
                    'delivery_partner_id' => $partnerId,
                    'city_id' => $cityId,
                    'locality_id' => null
                ]);
            }
        }
        
        if (!empty($data['locality_ids'])) {
            foreach ($data['locality_ids'] as $localityId) {
                $locality = Locality::find($localityId);
                if ($locality) {
                    DeliveryPartnerLocality::create([
                        'delivery_partner_id' => $partnerId,
                        'city_id' => $locality->city_id,
                        'locality_id' => $localityId
                    ]);
                }
            }
        }
        
        return response()->json(['ok'=>true]);
    }

    public function assignOrders(Request $request)
    {
        $data = $request->validate([
            'id'        => 'required|integer|exists:delivery_persons,id',
            'order_ids' => 'nullable|array',
            'order_ids.*' => 'integer|exists:orders,id',
        ]);

        $partnerId = $data['id'];
        $dp = DeliveryPerson::findOrFail($partnerId);

        // Find the linked delivery User — try email first, then phone
        $deliveryUser = null;
        if ($dp->email) {
            $deliveryUser = \App\Models\User::where('email', $dp->email)->where('role', 'delivery')->first();
        }
        if (!$deliveryUser && $dp->phone) {
            $deliveryUser = \App\Models\User::where(function ($q) use ($dp) {
                $q->where('phone', $dp->phone);
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'mobile')) {
                    $q->orWhere('mobile', $dp->phone);
                }
            })->where('role', 'delivery')->first();
        }
        if (!$deliveryUser && $dp->name) {
            $deliveryUser = \App\Models\User::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim((string) $dp->name))])
                ->where('role', 'delivery')
                ->first();
        }

        \Illuminate\Support\Facades\Log::info('assignOrders: dp_id=' . $partnerId
            . ' dp_name=' . $dp->name
            . ' linked_user_id=' . ($deliveryUser?->id ?? 'NULL'));
        // Unassign previously assigned orders for this partner that are not in new list
        Order::where('assigned_delivery_person_id', $partnerId)
            ->whereNotIn('id', $data['order_ids'] ?? [])
            ->update(['assigned_delivery_person_id' => null, 'assigned_delivery' => null]);

        // Assign new orders
        if (!empty($data['order_ids'])) {
            foreach ($data['order_ids'] as $orderId) {
                Order::where('id', $orderId)->update([
                    'assigned_delivery_person_id' => $partnerId,
                    'assigned_delivery'           => $deliveryUser?->id,
                    'status'                      => 'Assigned',
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function toggleStatus(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:delivery_persons,id',
            'action' => 'required|string|in:activate,deactivate'
        ]);
        $status = $data['action'] === 'activate' ? 'Active' : 'Inactive';
        DeliveryPerson::whereIn('id', $data['ids'])->update(['status' => $status]);
        return response()->json(['ok'=>true]);
    }
}
