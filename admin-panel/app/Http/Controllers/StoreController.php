<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    public function index()
    {
        return view('store.store_index');
    }

    public function list()
    {
        $stores = Store::orderBy('store_name')->get()->map(fn($s) => [
            'id'         => 's_'.$s->id,
            'real_id'    => $s->id,
            'type'       => 'store',
            'store_name' => $s->store_name,
            'code'       => $s->code,
            'manager'    => $s->manager,
            'phone'      => $s->phone,
            'address'    => $s->address,
            'latitude'   => $s->latitude,
            'longitude'  => $s->longitude,
            'status'     => $s->status,
        ]);

        $customers = \App\Models\Customer::orderBy('business_name')->get()->map(function ($c) {
            $lat = null;
            $lng = null;
            if (!empty($c->geolocation) && str_contains((string) $c->geolocation, ',')) {
                [$latRaw, $lngRaw] = array_map('trim', explode(',', (string) $c->geolocation, 2));
                if (is_numeric($latRaw) && is_numeric($lngRaw)) {
                    $lat = (float) $latRaw;
                    $lng = (float) $lngRaw;
                }
            }

            return [
                'id'         => 'c_'.$c->id,
                'real_id'    => $c->id,
                'type'       => 'customer',
                'store_name' => $c->business_name,
                'code'       => $c->code,
                'manager'    => $c->contact_person,
                'phone'      => $c->mobile,
                'address'    => $c->billing_address,
                'latitude'   => $lat,
                'longitude'  => $lng,
                'status'     => $c->status === 'Active' ? 1 : 0,
            ];
        });

        return response()->json(['ok' => true, 'data' => $stores->merge($customers)->values()]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'store_name' => 'required|string',
            'code' => 'nullable|string',
            'manager' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = Store::create(array_merge($v->validated(), ['status' => $request->input('status', 1)]));
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function edit($id)
    {
        $s = Store::findOrFail($id);
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'store_name' => 'required|string',
            'code' => 'nullable|string',
            'manager' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = Store::findOrFail($id);
        $s->update(array_merge($v->validated(), ['status' => $request->input('status', 1)]));
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function destroy($id)
    {
        Store::destroy($id);
        return response()->json(['ok'=>true]);
    }
}
