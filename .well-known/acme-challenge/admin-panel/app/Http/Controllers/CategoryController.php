<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('Category.category_list', compact('categories'));
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'status' => 'required'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => str()->slug($request->name),
            'description' => $request->description ?? null,
            'status' => $request->status,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category added successfully',
                'category' => $category
            ]);
        }

        return redirect()->back()->with('success','Category added');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories,name,'.$id,
            'status' => 'required'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => str()->slug($request->name),
            'description' => $request->description ?? null,
            'status' => $request->status,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        }

        return redirect()->back()->with('success','Category updated');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        }
        
        return redirect()->back()->with('success','Category deleted');
    }

    // Return a status page for categories (simple wrapper)
    public function statusPage()
    {
        $categories = Category::latest()->get();
        return view('Category.category_status', compact('categories'));
    }
}
