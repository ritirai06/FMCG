<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreContact;
use Illuminate\Support\Facades\Validator;

class StoreContactController extends Controller
{
    public function list(){ $data = StoreContact::with('store')->get(); return response()->json(['ok'=>true,'data'=>$data]); }

    public function store(Request $request){
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $c = StoreContact::create($v->validated()); return response()->json(['ok'=>true,'data'=>$c]);
    }

    public function edit($id){ $c = StoreContact::findOrFail($id); return response()->json(['ok'=>true,'data'=>$c]); }

    public function update(Request $request,$id){
        $v = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $c = StoreContact::findOrFail($id); $c->update($v->validated()); return response()->json(['ok'=>true,'data'=>$c]);
    }

    public function destroy($id){ StoreContact::destroy($id); return response()->json(['ok'=>true]); }
}
