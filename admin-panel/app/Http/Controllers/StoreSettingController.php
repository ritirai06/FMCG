<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Validator;

class StoreSettingController extends Controller
{
    public function list(){ $data = StoreSetting::with('store')->get(); return response()->json(['ok'=>true,'data'=>$data]); }

    public function store(Request $request){
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'notifications_enabled' => 'nullable|boolean',
            'sync_enabled' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = StoreSetting::create(array_merge($v->validated(), ['notifications_enabled' => $request->input('notifications_enabled',1),'sync_enabled' => $request->input('sync_enabled',1)]));
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function edit($id){ $s = StoreSetting::findOrFail($id); return response()->json(['ok'=>true,'data'=>$s]); }

    public function update(Request $request,$id){
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'notifications_enabled' => 'nullable|boolean',
            'sync_enabled' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = StoreSetting::findOrFail($id); $s->update(array_merge($v->validated(), ['notifications_enabled' => $request->input('notifications_enabled',1),'sync_enabled' => $request->input('sync_enabled',1)])); return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function destroy($id){ StoreSetting::destroy($id); return response()->json(['ok'=>true]); }
}
