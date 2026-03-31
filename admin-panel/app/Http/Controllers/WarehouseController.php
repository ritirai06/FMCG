<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Locality;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::latest()->get();

        $total    = Warehouse::count();
        $active   = Warehouse::where('status','Active')->count();
        $inactive = Warehouse::where('status','Inactive')->count();
        $managers = Warehouse::distinct()->count('manager_name');
        $localities = Locality::where('status','Active')->orderBy('name')->get();

        return view('warehouse.warehouse_create', compact(
            'warehouses',
            'total',
            'active',
            'inactive',
            'managers',
            'localities'
        ));
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required',
            'manager_name' => 'required',
            'contact'      => 'required',
            'location'     => 'required',
            'status'       => 'required'
        ]);

        Warehouse::create($request->all());

        return redirect()->route('warehouse.create')
            ->with('success','Warehouse Added Successfully');
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $warehouse->update($request->all());

        return redirect()->back()
            ->with('success','Warehouse Updated Successfully');
    }

    public function destroy($id)
    {
        Warehouse::findOrFail($id)->delete();

        return redirect()->back()
            ->with('success','Warehouse Deleted Successfully');
    }

    // ✅ STATUS PAGE (Region Removed)
    public function status(Request $request)
    {
        $query = Warehouse::query();

        // 🔍 Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('manager_name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }

        // 🔄 Status Filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $warehouses = $query->latest()->paginate(10);

        return view('warehouse.status', compact('warehouses'));
    }

    // ✅ TOGGLE STATUS
    public function toggleStatus($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $warehouse->status = $warehouse->status === 'Active' ? 'Inactive' : 'Active';
        $warehouse->save();

        return response()->json([
            'status' => $warehouse->status
        ]);
    }
}
