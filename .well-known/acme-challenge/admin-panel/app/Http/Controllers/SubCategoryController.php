<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category')->latest()->get();
        $categories = Category::where('status', 'Active')->get();
        return view('subcategories.list', compact('subcategories', 'categories'));
    }

    public function create()
    {
        // Fetch all categories so the dropdown always has values
        $categories = Category::all();
        $total = SubCategory::count();
        $active = SubCategory::where('status', 'Active')->count();
        $inactive = SubCategory::where('status', 'Inactive')->count();
        
        return view('subcategories.create', compact('categories', 'total', 'active', 'inactive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:subcategories,name',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => str()->slug($request->name),
            'description' => $request->description ?? null,
            'category_id' => $request->category_id,
            'status' => $request->has('status') ? 'Active' : 'Inactive',
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/subcategories'), $filename);
            $data['image'] = $filename;
        }

        $subcategory = SubCategory::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'subcategory' => $subcategory]);
        }

        return redirect()->route('subcategories.index')->with('success','SubCategory added');
    }

    public function show($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, $id)
    {
        $subcategory = SubCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:subcategories,name,'.$id,
            'category_id' => 'required|exists:categories,id'
        ]);

        $subcategory->update([
            'name' => $request->name,
            'slug' => str()->slug($request->name),
            'description' => $request->description ?? null,
            'category_id' => $request->category_id,
            'status' => $request->has('status') ? 'Active' : 'Inactive',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'subcategory' => $subcategory]);
        }

        return redirect()->back()->with('success','SubCategory updated');
    }

    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'SubCategory deleted']);
        }
        
        return redirect()->back()->with('success','SubCategory deleted');
    }
}
