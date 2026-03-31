<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryPerson;
use App\Models\Order;

class DeliveryPersonController extends Controller
{
    public function index()
    {
        $partners = DeliveryPerson::latest()->get();
        
        // Load order details and calculate total amount for each partner
        foreach ($partners as $partner) {
            $orderIds = is_array($partner->orders_json) ? $partner->orders_json : [];
            
            if (!empty($orderIds)) {
                $totalAmount = Order::whereIn('id', $orderIds)->sum('amount');
                $orderCount = count($orderIds);
                $partner->order_total = $totalAmount;
                $partner->order_count = $orderCount;
            } else {
                $partner->order_total = 0;
                $partner->order_count = 0;
            }
        }
        
        return view('delivery.index', compact('partners'));
    }

    public function details($id)
    {
        $p = DeliveryPerson::findOrFail($id);
        return response()->json($p);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'vehicle' => 'nullable|string',
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
            'zones' => 'nullable|array',
            'zones.*' => 'string'
        ]);
        $p = DeliveryPerson::findOrFail($data['id']);
        $p->zones_json = $data['zones'] ?? [];
        $p->save();
        return response()->json(['ok'=>true]);
    }

    public function assignOrders(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:delivery_persons,id',
            'orders' => 'nullable|array'
        ]);
        $p = DeliveryPerson::findOrFail($data['id']);
        $p->orders_json = $data['orders'] ?? [];
        $p->save();
        return response()->json(['ok'=>true]);
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
