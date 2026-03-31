<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesPerson;
use App\Models\City;
use Illuminate\Http\Request;

class SalesPersonController extends Controller
{
    public function index()
    {
        $salesPeople = SalesPerson::with(['city','cities','localities'])->latest()->get();
        
        // Calculate actual sales and incentives for each salesperson
        foreach ($salesPeople as $sp) {
            $sp->actual_sales = $this->getActualSalesForPerson($sp->id);
            $sp->calculated_incentive = $sp->calculateIncentive($sp->actual_sales);
            $sp->calculated_bonus = $sp->calculateBonus();
            $sp->total_salary = $sp->calculateTotalSalary($sp->actual_sales);
        }
        
        $cities = City::orderBy('name')->get();
        $localities = \App\Models\Locality::orderBy('name')->get();

        return view('sale_person.index', compact('salesPeople','cities','localities'));
    }

    /**
     * Get actual sales amount for a salesperson from completed orders
     */
    private function getActualSalesForPerson($salesPersonId)
    {
        return \App\Models\Order::where('sales_person_id', $salesPersonId)
            ->where('status', 'Completed')
            ->sum('total_amount') ?? 0;
    }

    // return JSON details for a sales person (cities/localities/salary/incentive)
    public function details($id)
    {
        $sp = SalesPerson::with(['cities','localities'])->findOrFail($id);

        return response()->json([
            'id'               => $sp->id,
            'name'             => $sp->name,
            'phone'            => $sp->phone,
            'email'            => $sp->email,
            'city_ids'         => $sp->cities->pluck('id')->all(),
            'locality_ids'     => $sp->localities->pluck('id')->all(),
            'base_salary'      => $sp->base_salary,
            'allowance'        => $sp->allowance,
            'bonus_percent'    => $sp->bonus_percent,
            'target_sales'     => $sp->target_sales,
            'incentive_percent'=> $sp->incentive_percent,
            'latitude'         => $sp->current_latitude,
            'longitude'        => $sp->current_longitude,
            'address'          => $sp->address,
            'last_update'      => $sp->last_location_update?->diffForHumans() ?? 'Never',
        ]);
    }

    // assign cities via pivot
    public function assignCities(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'city_ids' => 'required|array',
            'city_ids.*' => 'integer|exists:cities,id'
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);
        $sp->cities()->sync($data['city_ids']);

        return response()->json(['ok'=>true]);
    }

    // assign localities via pivot
    public function assignLocalitiesForSales(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'locality_ids' => 'required|array',
            'locality_ids.*' => 'integer|exists:localities,id'
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);
        $sp->localities()->sync($data['locality_ids']);

        return response()->json(['ok'=>true]);
    }

    public function updateSalary(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'base_salary' => 'nullable|numeric',
            'allowance' => 'nullable|numeric',
            'bonus_percent' => 'nullable|numeric'
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);
        $sp->update([
            'base_salary' => $data['base_salary'] ?? null,
            'allowance' => $data['allowance'] ?? null,
            'bonus_percent' => $data['bonus_percent'] ?? null,
        ]);

        return response()->json(['ok'=>true]);
    }

    public function updateIncentive(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'target_sales' => 'nullable|numeric',
            'incentive_percent' => 'nullable|numeric'
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);
        $sp->update([
            'target_sales' => $data['target_sales'] ?? null,
            'incentive_percent' => $data['incentive_percent'] ?? null,
        ]);

        return response()->json(['ok'=>true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:sales_persons,id',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'city_id' => 'nullable|exists:cities,id',
            'status' => 'nullable|string',
            'avatar' => 'nullable|file|image|max:2048',
            'base_salary' => 'nullable|numeric',
            'allowance' => 'nullable|numeric',
            'bonus_percent' => 'nullable|numeric',
            'target_sales' => 'nullable|numeric',
            'incentive_percent' => 'nullable|numeric'
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        if (!empty($data['id'])) {
            $sp = SalesPerson::find($data['id']);
            $sp->update($data);
            return redirect()->back()->with('success', 'Sales person updated');
        }

        SalesPerson::create($data);
        return redirect()->back()->with('success', 'Sales person added');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:sales_persons,id']);
        SalesPerson::destroy($data['id']);
        return redirect()->back()->with('success','Deleted');
    }

    public function toggleStatus(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:sales_persons,id',
            'action' => 'required|string|in:activate,deactivate'
        ]);

        $status = $data['action'] === 'activate' ? 'Active' : 'Inactive';

        $updated = SalesPerson::whereIn('id', $data['ids'])->update(['status' => $status]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['updated' => $updated]);
        }

        return redirect()->back()->with('success', 'Status updated for selected sales persons.');
    }

    /**
     * Update salesperson location
     */
    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'latitude'        => 'required|numeric|between:-90,90',
            'longitude'       => 'required|numeric|between:-180,180',
            'address'         => 'nullable|string|max:500',
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);

        // Save to main record (permanent)
        $sp->update([
            'current_latitude'          => $data['latitude'],
            'current_longitude'         => $data['longitude'],
            'address'                   => $data['address'] ?? null,
            'last_location_update'      => now(),
        ]);

        // Also log to history
        \App\Models\SalesPersonLocation::create([
            'sales_person_id' => $data['sales_person_id'],
            'latitude'        => $data['latitude'],
            'longitude'       => $data['longitude'],
            'address'         => $data['address'] ?? null,
            'activity_type'   => 'manual',
            'recorded_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully',
            'data'    => [
                'latitude'   => $sp->current_latitude,
                'longitude'  => $sp->current_longitude,
                'address'    => $sp->address,
                'updated_at' => $sp->last_location_update->diffForHumans(),
            ],
        ]);
    }

    /**
     * Get location history for a salesperson
     */
    public function locationHistory($id)
    {
        $sp = SalesPerson::findOrFail($id);
        $locations = $sp->locations()
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'sales_person' => [
                'id' => $sp->id,
                'name' => $sp->name,
                'current_latitude' => $sp->current_latitude,
                'current_longitude' => $sp->current_longitude,
                'last_update' => $sp->last_location_update
            ],
            'locations' => $locations
        ]);
    }

    /**
     * Toggle location tracking for a salesperson
     */
    public function toggleLocationTracking(Request $request)
    {
        $data = $request->validate([
            'sales_person_id' => 'required|integer|exists:sales_persons,id',
            'enabled' => 'required|boolean'
        ]);

        $sp = SalesPerson::findOrFail($data['sales_person_id']);
        $sp->update(['location_tracking_enabled' => $data['enabled']]);

        return response()->json([
            'success' => true,
            'enabled' => $sp->location_tracking_enabled
        ]);
    }
}
