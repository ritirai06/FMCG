<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncentiveSlab;
use Illuminate\Support\Facades\Validator;

class IncentiveSlabController extends Controller
{
    public function list()
    {
        $slabs = IncentiveSlab::orderBy('min_amount')->get();
        return response()->json(['ok'=>true,'data'=>$slabs]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'min_amount' => 'required|numeric',
            'max_amount' => 'nullable|numeric',
            'percent' => 'required|numeric',
            'effective_from' => 'nullable|date'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $sl = IncentiveSlab::create($v->validated());
        return response()->json(['ok'=>true,'data'=>$sl]);
    }

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(),[
            'min_amount' => 'required|numeric',
            'max_amount' => 'nullable|numeric',
            'percent' => 'required|numeric',
            'effective_from' => 'nullable|date'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $sl = IncentiveSlab::findOrFail($id);
        $sl->update($v->validated());
        return response()->json(['ok'=>true,'data'=>$sl]);
    }

    public function destroy($id)
    {
        IncentiveSlab::destroy($id);
        return response()->json(['ok'=>true]);
    }
}
