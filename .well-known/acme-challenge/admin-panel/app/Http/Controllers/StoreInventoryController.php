<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\Validator;

class StoreInventoryController extends Controller
{
    public function list()
    {
        $data = StoreInventory::with('store')->get();
        return response()->json(['ok'=>true,'data'=>$data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'sku_count' => 'required|integer',
            'low_stock_items' => 'nullable|integer',
            'last_sync' => 'nullable|date'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $inv = StoreInventory::create($v->validated());
        return response()->json(['ok'=>true,'data'=>$inv]);
    }

    public function edit($id){ $i = StoreInventory::findOrFail($id); return response()->json(['ok'=>true,'data'=>$i]); }

    public function update(Request $request,$id){
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'sku_count' => 'required|integer',
            'low_stock_items' => 'nullable|integer',
            'last_sync' => 'nullable|date'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $i = StoreInventory::findOrFail($id); $i->update($v->validated()); return response()->json(['ok'=>true,'data'=>$i]);
    }

    public function destroy($id){ StoreInventory::destroy($id); return response()->json(['ok'=>true]); }
}
