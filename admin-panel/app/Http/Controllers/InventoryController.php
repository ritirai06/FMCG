<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryLog;
use App\Models\Adjustment;
use App\Models\InventoryTransaction;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

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
    // ADD QUANTITY TO PRODUCT
    // =============================
    public function addQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);
            $product->quantity += $request->quantity;
            $product->save();

            // Update warehouse stock if specified
            if ($request->warehouse_id) {
                $warehouseProduct = WarehouseProduct::firstOrCreate(
                    ['product_id' => $request->product_id, 'warehouse_id' => $request->warehouse_id],
                    ['stock_quantity' => 0]
                );
                $warehouseProduct->stock_quantity += $request->quantity;
                $warehouseProduct->save();
            }

            // Log the change
            InventoryLog::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'change_type' => 'add',
                'quantity' => $request->quantity,
                'reason' => $request->reason ?? 'Stock added'
            ]);

            // Transaction log
            InventoryTransaction::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'type' => 'stock_in',
                'quantity' => $request->quantity,
                'notes' => $request->reason
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Quantity added successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================
    // REMOVE QUANTITY FROM PRODUCT
    // =============================
    public function removeQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);
            
            if ($product->quantity < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock'], 400);
            }

            $product->quantity -= $request->quantity;
            $product->save();

            // Update warehouse stock if specified
            if ($request->warehouse_id) {
                $warehouseProduct = WarehouseProduct::where('product_id', $request->product_id)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();
                
                if (!$warehouseProduct || $warehouseProduct->stock_quantity < $request->quantity) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Insufficient warehouse stock'], 400);
                }

                $warehouseProduct->stock_quantity -= $request->quantity;
                $warehouseProduct->save();
            }

            // Log the change
            InventoryLog::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'change_type' => 'remove',
                'quantity' => $request->quantity,
                'reason' => $request->reason ?? 'Stock removed'
            ]);

            // Transaction log
            InventoryTransaction::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'type' => 'stock_out',
                'quantity' => $request->quantity,
                'notes' => $request->reason
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Quantity removed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================
    // GET WAREHOUSE STOCK FOR PRODUCT
    // =============================
    public function getWarehouseStock($productId)
    {
        $stocks = WarehouseProduct::with('warehouse')
            ->where('product_id', $productId)
            ->get()
            ->map(function($item) {
                return [
                    'warehouse_id' => $item->warehouse_id,
                    'warehouse_name' => $item->warehouse->name ?? 'Unknown',
                    'stock_quantity' => $item->stock_quantity
                ];
            });

        return response()->json($stocks);
    }

    // =============================
    // GET INVENTORY LOGS
    // =============================
    public function getInventoryLogs($productId)
    {
        $logs = InventoryLog::with(['warehouse', 'product'])
            ->where('product_id', $productId)
            ->latest()
            ->get();

        return response()->json($logs);
    }

    // =============================
    // GET PRODUCTS FOR WAREHOUSE (used by Stock In/Out dropdowns)
    // =============================
    public function warehouseProducts($warehouseId)
    {
        // All active products with their current stock in this warehouse
        $products = Product::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'quantity']);

        $stockMap = Inventory::where('warehouse_id', $warehouseId)
            ->pluck('quantity', 'product_id');

        return response()->json(
            $products->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'sku'      => $p->sku ?? '',
                'quantity' => (int) ($stockMap[$p->id] ?? 0),
            ])
        );
    }

    // =============================
    // GET INVENTORY TRANSACTIONS
    // =============================
    public function getTransactions(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'warehouse'])->latest();

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(50);

        return response()->json($transactions);
    }
}