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
        $cities = City::orderBy('name')->get();
        $localities = \App\Models\Locality::orderBy('name')->get();

        return view('sale_person.index', compact('salesPeople','cities','localities'));
    }

    // return JSON details for a sales person (cities/localities/salary/incentive)
    public function details($id)
    {
        $sp = SalesPerson::with(['cities','localities'])->findOrFail($id);

        return response()->json([
            'id' => $sp->id,
            'name' => $sp->name,
            'phone' => $sp->phone,
            'email' => $sp->email,
            'city_ids' => $sp->cities->pluck('id')->all(),
            'locality_ids' => $sp->localities->pluck('id')->all(),
            'base_salary' => $sp->base_salary,
            'allowance' => $sp->allowance,
            'bonus_percent' => $sp->bonus_percent,
            'target_sales' => $sp->target_sales,
            'incentive_percent' => $sp->incentive_percent,
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
}
