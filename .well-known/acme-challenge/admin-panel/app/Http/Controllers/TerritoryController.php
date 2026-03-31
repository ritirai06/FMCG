<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TerritoryController extends Controller
{
    public function index()
    {
        $cities = City::latest()->get();

        $localities = Locality::with('city')
                        ->latest()
                        ->get();

        return view('cities.index',
            compact('cities','localities'));
    }

    // STORE LOCALITY
    public function storeLocality(Request $request)
    {
        $data = $request->validate([
            'city_id' => 'nullable|exists:cities,id',
            'name' => 'required|string',
            'pincode' => 'nullable|string',
            'status' => 'nullable|string',
            'id' => 'nullable|integer|exists:localities,id'
        ]);

        if (!empty($data['id'])) {
            // update
            $locality = Locality::find($data['id']);
            $locality->update(
                array_filter([
                    'city_id' => $data['city_id'] ?? $locality->city_id,
                    'name' => $data['name'],
                    'pincode' => $data['pincode'] ?? $locality->pincode,
                    'status' => $data['status'] ?? $locality->status,
                ], function($v){ return !is_null($v); })
            );

            return redirect()->back()->with('success', 'Locality updated');
        }

        // create
        try {
            Locality::create([
                'city_id' => $data['city_id'] ?? null,
                'name' => $data['name'],
                'pincode' => $data['pincode'] ?? null,
                'status' => $data['status'] ?? 'Active'
            ]);

            return redirect()->back()->with('success','Locality Added');
        } catch (\Exception $e) {
            Log::error('Locality create failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'payload' => $data
            ]);

            return redirect()->back()->with('error', 'Failed to create locality: ' . $e->getMessage());
        }
    }

    // STORE CITY (create or update)
    public function storeCity(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'state' => 'nullable|string',
            'status' => 'nullable|string',
            'id' => 'nullable|integer|exists:cities,id'
        ]);

        if (!empty($data['id'])) {
            $city = City::find($data['id']);
            $city->update([
                'name' => $data['name'],
                'state' => $data['state'] ?? $city->state,
                'status' => $data['status'] ?? $city->status,
            ]);

            return redirect()->back()->with('success', 'City updated');
        }

        City::create([
            'name' => $data['name'],
            'state' => $data['state'] ?? null,
            'status' => $data['status'] ?? 'Active',
        ]);

        return redirect()->back()->with('success', 'City added');
    }
   public function getLocalities($city)
{
    $localities = Locality::where('city_id',$city)
                    ->where('status','Active')
                    ->get();

    return response()->json($localities);
}

    // ASSIGN selected localities to a city
    public function assignLocalities(Request $request)
    {
        $data = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'localities' => 'required|array|min:1',
            'localities.*' => 'integer|exists:localities,id'
        ]);

        $cityId = $data['city_id'];
        $localityIds = $data['localities'];

        DB::transaction(function() use ($cityId, $localityIds) {
            Locality::whereIn('id', $localityIds)
                ->update(['city_id' => $cityId]);
        });

        return redirect()->back()->with('success', 'Localities assigned successfully.');
    }

    // delete city (and detach localities)
    public function deleteCity(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:cities,id']);
        $id = $data['id'];

        DB::transaction(function() use ($id) {
            Locality::where('city_id', $id)->update(['city_id' => null]);
            City::destroy($id);
        });

        return redirect()->back()->with('success', 'City deleted');
    }

    // delete single locality
    public function deleteLocality(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:localities,id']);
        Locality::destroy($data['id']);
        return redirect()->back()->with('success', 'Locality deleted');
    }

}
