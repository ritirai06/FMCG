<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // ======================
    // CREATE PAGE
    // ======================
    public function create()
    {
        $brands = DB::table('brands')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $allCategories = DB::table('categories')
            ->select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->get();

        $categories = $allCategories->whereNull('parent_id')->values();
        $subCategories = $allCategories->whereNotNull('parent_id')->values();

        return view('product.create', compact('brands', 'categories', 'subCategories'));
    }

    // ======================
    // STORE PRODUCT
    // ======================
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required',
            'sku'             => 'required|unique:products,sku',
            'brand'           => 'required',
            'category'        => 'required',
            'purchase_price' => 'required|numeric',
            'sale_price'      => 'required|numeric',
            'mrp'             => 'required|numeric',
            'status'          => 'required',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $margin = $request->sale_price - $request->purchase_price;

        // IMAGE UPLOAD
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->insert([
            'name'            => $request->name,
            'sku'             => $request->sku,
            'brand'           => $request->brand,
            'category'        => $request->category,
            'sub_category'    => $request->sub_category,
            'purchase_price' => $request->purchase_price,
            'sale_price'      => $request->sale_price,
            'mrp'             => $request->mrp,
            'margin'          => $margin,
            'status'          => $request->status,
            'image'           => $imagePath,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully'
        ]);
    }

    // ======================
    // PRODUCT LIST
    // ======================
    public function index()
    {
        $products = DB::table('products')
            ->orderBy('id', 'desc')
            ->get();

        return view('product.product_list', compact('products'));
    }

    // ======================
    // FETCH PRODUCT (AJAX FOR MODAL)
    // ======================
    public function fetchProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        return response()->json($product);
    }

    // ======================
    // UPDATE PRODUCT (AJAX)
    // ======================
    public function updateAjax(Request $request, $id)
{
    $margin = $request->sale_price - $request->purchase_price;

    DB::table('products')->where('id', $id)->update([
        'name'            => $request->name,
        'sku'             => $request->sku,
        'brand'           => $request->brand,
        'category'        => $request->category,
        'purchase_price'  => $request->purchase_price,
        'sale_price'      => $request->sale_price,
        'mrp'             => $request->mrp,
        'margin'          => $margin,
        'status'          => $request->status,
        'updated_at'      => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Product updated successfully'
    ]);
}

    // ======================
    // DELETE PRODUCT
    // ======================
    public function destroy($id)
    {
        DB::table('products')->where('id', $id)->delete();

        return redirect()
    ->route('product.index')
    ->with('success', 'Product deleted successfully');

    }

   // ======================
// PRODUCT STATUS PAGE
// ======================
public function statusPage()
{
    $products = DB::table('products')
        ->orderBy('id', 'desc')
        ->paginate(10);

    $totalProducts = DB::table('products')->count();
    $activeProducts = DB::table('products')->where('status', 'Active')->count();
    $inactiveProducts = DB::table('products')->where('status', 'Inactive')->count();

    return view('product.product_status', compact(
        'products',
        'totalProducts',
        'activeProducts',
        'inactiveProducts'
    ));
}

// ======================
// TOGGLE STATUS (AJAX)
// ======================
public function toggleStatus(Request $request)
{
    DB::table('products')
        ->where('id', $request->id)
        ->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

    return response()->json([
        'success' => true
    ]);
}

}



