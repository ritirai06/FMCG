<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    // CREATE PAGE
    public function create()
    {
        return view('brands.add_brand');
    }

    // STORE BRAND
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|unique:brands,name',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'      => 'required'
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')
                        ->store('brands', 'public');
        }

        DB::table('brands')->insert([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'logo'        => $logoPath,
            'status'      => $request->status,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Brand Added Successfully');
    }

    // LIST PAGE
public function index()
{
    $brands = DB::table('brands')->orderBy('id','desc')->get();
    return view('brands.brand_list', compact('brands'));
}

public function edit($id)
{
    $brand = DB::table('brands')->where('id',$id)->first();
    return response()->json($brand);
}

public function update(Request $request,$id)
{
    DB::table('brands')->where('id',$id)->update([
        'name'=>$request->name,
        'description'=>$request->description,
        'status'=>$request->status,
        'updated_at'=>now()
    ]);

    return response()->json(['success'=>true]);
}

public function destroy($id)
{
    DB::table('brands')->where('id',$id)->delete();
    return response()->json(['success'=>true]);
}
// ================= STATUS PAGE =================
public function statusPage()
{
    $brands = DB::table('brands')->get();

    $total = $brands->count();
    $active = $brands->where('status', 'Active')->count();
    $inactive = $brands->where('status', 'Inactive')->count();

    $activePercent = $total > 0 ? round(($active / $total) * 100) : 0;

    return view('brands.brand_status', compact(
        'brands',
        'total',
        'active',
        'inactive',
        'activePercent'
    ));
}


// ================= TOGGLE STATUS =================
public function toggleStatus(Request $request)
{
    $brand = DB::table('brands')->where('id', $request->id)->first();

    if(!$brand){
        return response()->json(['success'=>false]);
    }

    $newStatus = $brand->status == 'Active' ? 'Inactive' : 'Active';

    DB::table('brands')
        ->where('id',$request->id)
        ->update(['status'=>$newStatus]);

    // fresh counts
    $total = DB::table('brands')->count();
    $active = DB::table('brands')
                ->where('status','Active')->count();
    $inactive = DB::table('brands')
                ->where('status','Inactive')->count();

    return response()->json([
        'success'=>true,
        'status'=>$newStatus,
        'total'=>$total,
        'active'=>$active,
        'inactive'=>$inactive
    ]);
}


}
