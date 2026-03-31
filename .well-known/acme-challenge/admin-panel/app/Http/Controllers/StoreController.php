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
        $data = Store::orderBy('store_name')->get();
        return response()->json(['ok'=>true,'data'=>$data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'store_name' => 'required|string',
            'code' => 'nullable|string',
            'manager' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'nullable|boolean'
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
            'status' => 'nullable|boolean'
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
