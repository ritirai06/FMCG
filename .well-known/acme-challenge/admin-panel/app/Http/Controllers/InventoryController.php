<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryLog;
use App\Models\Adjustment; // ✅ IMPORTANT
use Illuminate\Http\Request;
use App\Models\AuditLog;

class InventoryController extends Controller
{

    // =============================
    // INVENTORY PAGE
    // =============================
    public function index()
    {
        $inventories = Inventory::with(['warehouse','product'])
                        ->latest()
                        ->paginate(10);

        $warehouses = Warehouse::where('status','Active')->get();
        $products   = Product::where('status','Active')->get();

        $adjustments = Adjustment::with(['warehouse','product'])
                        ->latest()
                        ->get();

        // ✅ AUDIT LOGS
        $auditLogs = AuditLog::where('action','!=','Adjustment')
            ->latest()
            ->get();


        return view('inventory.index', compact(
            'inventories',
            'warehouses',
            'products',
            'adjustments',
            'auditLogs'
        ));
    }

    // =============================
    // STOCK IN
    // =============================
    public function stockIn(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|numeric|min:1',
        ]);

        $inventory = Inventory::where('warehouse_id',$request->warehouse_id)
            ->where('product_id',$request->product_id)
            ->first();

        if ($inventory) {
            $inventory->quantity += $request->quantity;
            $inventory->save();
        } else {
            $inventory = Inventory::create([
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'quantity'     => $request->quantity,
            ]);
        }

        // ✅ INVENTORY LOG SAVE - use correct fields from schema
        InventoryLog::create([
            'inventory_id' => $inventory->id,
            'type'         => 'in',
            'quantity'     => $request->quantity
        ]);

        // ✅ AUDIT
        AuditLog::create([
            'user'    => 'Admin',
            'action'  => 'Stock In',
            'details' => 'Added '.$request->quantity.' units'
        ]);

        return back()->with('success','Stock Added Successfully');
    }



    // =============================
    // STOCK OUT
    // =============================
    public function stockOut(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|numeric|min:1',
        ]);

        $inventory = Inventory::where('warehouse_id',$request->warehouse_id)
            ->where('product_id',$request->product_id)
            ->first();

        if (!$inventory) {
            return back()->with('error','No stock found.');
        }

        if ($inventory->quantity < $request->quantity) {
            return back()->with('error','Not enough stock.');
        }

        $inventory->quantity -= $request->quantity;
        $inventory->save();

        // ✅ INVENTORY LOG - use correct fields
        InventoryLog::create([
            'inventory_id' => $inventory->id,
            'type'         => 'out',
            'quantity'     => $request->quantity
        ]);

        // ✅ AUDIT
        AuditLog::create([
            'user'=>'Admin',
            'action'=>'Stock Out',
            'details'=>'Removed '.$request->quantity.' units'
        ]);

        return back()->with('success','Stock Out Successful');
    }



 // =============================
    // ADJUSTMENT
    // =============================
// =============================
    // ADJUSTMENT
    // =============================
    public function adjust(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'type'         => 'required|in:add,remove',
            'quantity'     => 'required|integer|min:1',
            'reason'       => 'required'
        ]);

        $inventory = Inventory::where('warehouse_id',$request->warehouse_id)
            ->where('product_id',$request->product_id)
            ->first();

        if (!$inventory) {
            return back()->with('error','Stock record not found');
        }

        if ($request->type == 'add') {
            $inventory->quantity += $request->quantity;
        } else {
            if ($inventory->quantity < $request->quantity) {
                return back()->with('error','Not enough stock');
            }
            $inventory->quantity -= $request->quantity;
        }

        $inventory->save();

        // ✅ Adjustment Table
        Adjustment::create([
            'warehouse_id'=>$request->warehouse_id,
            'product_id'=>$request->product_id,
            'type'=>$request->type,
            'quantity'=>$request->quantity,
            'reason'=>$request->reason
        ]);

        // ✅ Inventory Log - use correct fields
        InventoryLog::create([
            'inventory_id'=>$inventory->id,
            'type'=>'adjust',
            'quantity'=>$request->quantity
        ]);

        // ✅ Audit
        AuditLog::create([
            'user'=>'Admin',
            'action'=>'Adjustment',
            'details'=>ucfirst($request->type).' '.$request->quantity.' units'
        ]);

        return back()->with('success','Stock adjusted successfully');
    }

    // =============================
    // AJAX - products list per warehouse
    // =============================
    public function warehouseProducts($warehouseId)
    {
        // include all active products so stock-in form can add new ones
        $products = Product::where('status','Active')->get()->keyBy('id');

        $inventories = Inventory::where('warehouse_id',$warehouseId)
                        ->get()
                        ->keyBy('product_id');

        $result = [];
        foreach ($products as $prod) {
            $result[] = [
                'id' => $prod->id,
                'name' => $prod->name,
                'sku' => $prod->sku,
                'quantity' => isset($inventories[$prod->id]) ? $inventories[$prod->id]->quantity : 0,
            ];
        }

        return response()->json($result);
    }
}
