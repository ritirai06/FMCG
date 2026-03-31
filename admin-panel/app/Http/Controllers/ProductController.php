<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductUnit;
use App\Models\InventoryLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function create()
    {
        $brands = DB::table('brands')->select('id', 'name')->orderBy('name')->get();
        $categories = DB::table('categories')->select('id', 'name')->where('status', 'Active')->orderBy('name')->get();
        $subCategories = DB::table('subcategories')->select('id', 'name', 'category_id')->where('status', 'Active')->orderBy('name')->get();
        $warehouses = DB::table('warehouses')->select('id', 'name')->where('status', 'Active')->orderBy('name')->get();

        return view('product.create', compact('brands', 'categories', 'subCategories', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'brand' => 'required',
            'category' => 'required',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'status' => 'required',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $gstPercent      = floatval($request->gst_percent ?? 0);
        $cessPct         = floatval($request->cess_percent ?? 0);
        $sellEntered     = floatval($request->sell_price);
        $purchaseEntered = floatval($request->purchase_price);
        $sellTaxType     = $request->sell_tax_type ?? 'excl';
        $purchaseTaxType = $request->purchase_tax_type ?? 'excl';
        $discType        = $request->discount_type ?? 'flat';
        $discValue       = floatval($request->discount_value ?? 0);
        $priceIncludesGst = ($sellTaxType === 'incl');

        $calc = $this->calcPrices($sellEntered, $purchaseEntered, $sellTaxType, $purchaseTaxType, $gstPercent, $cessPct, $discType, $discValue);
        $gstAmount  = $calc['gst_amount'];
        $cessAmount = $calc['cess_amount'];
        $finalPrice = $calc['final_price'];
        $discAmount = $calc['discount_amount'];
        $sellBase   = $calc['sell_base'];
        $purchaseBase = $calc['purchase_base'];

        // Primary image (from first uploaded image)
        $imagePath = null;
        if ($request->hasFile('images')) {
            $first = $request->file('images')[0];
            $imagePath = $first->store('products', 'public');
        }

        $productId = DB::table('products')->insertGetId([
            'name' => $request->name,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'item_code' => $request->item_code ?? $request->sku,
            'item_description' => $request->item_description,
            'brand' => $request->brand,
            'category' => $request->category,
            'purchase_price' => $purchaseEntered,
            'sell_price' => $sellEntered,
            'sale_price' => $sellEntered,
            'mrp' => $request->mrp,
            'margin' => round($sellBase - $discAmount - $purchaseBase, 2),
            'status' => $request->status,
            'quantity' => $request->available_units ?? 0,
            'available_units' => $request->available_units ?? 0,
            'warehouse_id' => $request->warehouse_id,
            'gst_percent' => $gstPercent,
            'gst_amount' => round($gstAmount, 2),
            'price_includes_gst' => $priceIncludesGst,
            'final_price' => round($finalPrice - $discAmount, 2),
            'hsn_code' => $request->hsn_code,
            'cess_percent' => $cessPct,
            'discount' => round($discAmount, 2),
            'discount_type' => $discType,
            'discount_value' => $discValue,
            'discount_amount' => round($discAmount, 2),
            'offer_text' => $request->offer_text,
            'image' => $imagePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => $path
                ]);
            }
        }

        // Unit conversions
        if ($request->filled('unit_names')) {
            foreach ($request->unit_names as $i => $uName) {
                if (empty($uName))
                    continue;
                ProductUnit::create([
                    'product_id' => $productId,
                    'unit_name' => $uName,
                    'base_unit' => $request->base_units[$i] ?? 'PCS',
                    'conversion_value' => $request->conversion_values[$i] ?? 1,
                ]);
            }
        }

        // Warehouse inventory
        if ($request->warehouse_id) {
            $units = $request->available_units ?? 0;
            DB::table('inventories')->insert([
                'product_id' => $productId,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $units,
                'available_units' => $units,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log if initial stock is entered
            if ($units > 0) {
                InventoryLog::create([
                    'product_id'  => $productId,
                    'warehouse_id' => $request->warehouse_id,
                    'change_type' => 'add',
                    'quantity'    => $units,
                    'reason'      => 'Initial Stock Entry',
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Product added successfully']);
    }

    public function index()
    {
        $products = DB::table('products')->orderBy('id', 'desc')->get();
        return view('product.product_list', compact('products'));
    }

    public function fetchProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        return response()->json($product);
    }

    public function updateAjax(Request $request, $id)
    {
        $gstPercent      = floatval($request->gst_percent ?? 0);
        $cessPct         = floatval($request->cess_percent ?? 0);
        $sellEntered     = floatval($request->sell_price);
        $purchaseEntered = floatval($request->purchase_price);
        $sellTaxType     = $request->sell_tax_type ?? 'excl';
        $purchaseTaxType = $request->purchase_tax_type ?? 'excl';
        $discType        = $request->discount_type ?? 'flat';
        $discValue       = floatval($request->discount_value ?? 0);
        $priceIncludesGst = ($sellTaxType === 'incl');

        $calc = $this->calcPrices($sellEntered, $purchaseEntered, $sellTaxType, $purchaseTaxType, $gstPercent, $cessPct, $discType, $discValue);
        $gstAmount    = $calc['gst_amount'];
        $cessAmount   = $calc['cess_amount'];
        $finalPrice   = $calc['final_price'];
        $discAmount   = $calc['discount_amount'];
        $sellBase     = $calc['sell_base'];
        $purchaseBase = $calc['purchase_base'];

        DB::table('products')->where('id', $id)->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'item_code' => $request->item_code ?? $request->sku,
            'unit' => $request->unit,
            'item_description' => $request->item_description,
            'brand' => $request->brand,
            'category' => $request->category,
            'purchase_price' => $purchaseEntered,
            'sell_price' => $sellEntered,
            'sale_price' => $sellEntered,
            'mrp' => $request->mrp,
            'margin' => round($sellBase - $discAmount - $purchaseBase, 2),
            'status' => $request->status,
            'gst_percent' => $gstPercent,
            'gst_amount' => round($gstAmount, 2),
            'price_includes_gst' => $priceIncludesGst,
            'final_price' => round($finalPrice - $discAmount, 2),
            'hsn_code' => $request->hsn_code,
            'cess_percent' => $cessPct,
            'discount' => round($discAmount, 2),
            'discount_type' => $discType,
            'discount_value' => $discValue,
            'discount_amount' => round($discAmount, 2),
            'offer_text' => $request->offer_text,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Product updated successfully']);
    }

    public function destroy($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return redirect()->route('product.index')->with('success', 'Product deleted successfully');
    }

    // ── Shared price calculation (mirrors JS breakdownPrice) ──────────────
    private function calcPrices(float $sellEntered, float $purchaseEntered, string $sellType, string $purchaseType, float $gstPct, float $cessPct, string $discType, float $discValue): array
    {
        $totalTax = $gstPct + $cessPct;

        if ($sellType === 'incl' && $totalTax > 0) {
            $sellBase   = $sellEntered / (1 + $totalTax / 100);
            $gstAmt     = $sellBase * $gstPct  / 100;
            $cessAmt    = $sellBase * $cessPct / 100;
            $finalPrice = $sellEntered;
        } else {
            $sellBase   = $sellEntered;
            $gstAmt     = $sellBase * $gstPct  / 100;
            $cessAmt    = $sellBase * $cessPct / 100;
            $finalPrice = $sellBase + $gstAmt + $cessAmt;
        }

        $purchaseBase = ($purchaseType === 'incl' && $totalTax > 0)
            ? $purchaseEntered / (1 + $totalTax / 100)
            : $purchaseEntered;

        $discAmt = $discType === 'percent'
            ? ($finalPrice * $discValue / 100)
            : $discValue;
        if ($discAmt > $finalPrice) $discAmt = 0;

        return [
            'sell_base'     => round($sellBase, 4),
            'purchase_base' => round($purchaseBase, 4),
            'gst_amount'    => round($gstAmt, 2),
            'cess_amount'   => round($cessAmt, 2),
            'final_price'   => round($finalPrice - $discAmt, 2),
            'discount_amount' => round($discAmt, 2),
            'margin'        => round($sellBase - $discAmt - $purchaseBase, 2),
        ];
    }

    // ── IMPORT ────────────────────────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $request->validate(['bulk_file' => 'required|file|mimes:xlsx,xls']);

        try {
            $spreadsheet = IOFactory::load($request->file('bulk_file')->getRealPath());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cannot read file: ' . $e->getMessage()]);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, false); // 0-indexed

        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'File is empty']);
        }

        // Build header map from row 0
        $required = ['Product Name', 'SKU'];
        $headerRow = array_map('trim', $rows[0]);
        $map = array_flip($headerRow); // column_name => index

        foreach ($required as $col) {
            if (!isset($map[$col])) {
                return response()->json(['success' => false, 'message' => "Missing required column: $col"]);
            }
        }

        $col = fn(string $name) => isset($map[$name]) ? ($rows[$i][$map[$name]] ?? null) : null;

        $imported = $updated = $failed = 0;
        $errors   = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Skip completely empty rows
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) continue;

            $name = trim($col('Product Name') ?? '');
            $sku  = trim($col('SKU') ?? '');

            if ($name === '' || $sku === '') {
                $failed++;
                $errors[] = "Row " . ($i + 1) . ": Product Name and SKU are required";
                continue;
            }

            $gstPct      = floatval($col('GST %')      ?? 0);
            $cessPct     = floatval($col('Cess %')     ?? 0);
            $sellEntered = floatval($col('Sell Price') ?? 0);
            $purchEntered= floatval($col('Purchase Price') ?? 0);
            $discType    = strtolower(trim($col('Discount Type') ?? 'flat'));
            $discValue   = floatval($col('Discount Value') ?? 0);
            if (!in_array($discType, ['percent', 'flat'])) $discType = 'flat';

            $calc = $this->calcPrices($sellEntered, $purchEntered, 'excl', 'excl', $gstPct, $cessPct, $discType, $discValue);

            // Resolve warehouse
            $warehouseName = trim($col('Warehouse') ?? '');
            $warehouseId   = null;
            if ($warehouseName !== '') {
                $warehouseId = DB::table('warehouses')->where('name', $warehouseName)->value('id');
            }

            $payload = [
                'name'             => $name,
                'sku'              => $sku,
                'item_code'        => trim($col('Item Code') ?? $sku),
                'category'         => trim($col('Category') ?? ''),
                'brand'            => trim($col('Brand')    ?? ''),
                'unit'             => trim($col('Unit')     ?? 'PCS'),
                'mrp'              => floatval($col('MRP')  ?? 0),
                'purchase_price'   => $purchEntered,
                'sell_price'       => $sellEntered,
                'sale_price'       => $sellEntered,
                'gst_percent'      => $gstPct,
                'gst_amount'       => $calc['gst_amount'],
                'cess_percent'     => $cessPct,
                'price_includes_gst' => false,
                'discount_type'    => $discType,
                'discount_value'   => $discValue,
                'discount_amount'  => $calc['discount_amount'],
                'discount'         => $calc['discount_amount'],
                'final_price'      => $calc['final_price'],
                'margin'           => $calc['margin'],
                'warehouse_id'     => $warehouseId,
                'available_units'  => intval($col('Stock') ?? 0),
                'quantity'         => intval($col('Stock') ?? 0),
                'status'           => trim($col('Status') ?? 'Active'),
                'updated_at'       => now(),
            ];

            $existing = DB::table('products')->where('sku', $sku)->first();

            if ($existing) {
                DB::table('products')->where('sku', $sku)->update($payload);
                $updated++;
            } else {
                $payload['created_at'] = now();
                $productId = DB::table('products')->insertGetId($payload);

                // Seed inventory row if warehouse given
                if ($warehouseId && $payload['available_units'] > 0) {
                    DB::table('inventories')->insert([
                        'product_id'      => $productId,
                        'warehouse_id'    => $warehouseId,
                        'quantity'        => $payload['available_units'],
                        'available_units' => $payload['available_units'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
                $imported++;
            }
        }

        return response()->json([
            'success'  => true,
            'imported' => $imported,
            'updated'  => $updated,
            'failed'   => $failed,
            'errors'   => $errors,
            'message'  => "Import complete: $imported created, $updated updated" . ($failed ? ", $failed failed" : ''),
        ]);
    }

    // ── EXPORT ────────────────────────────────────────────────────────────
    public function export()
    {
        $products = DB::table('products')
            ->leftJoin('warehouses', 'products.warehouse_id', '=', 'warehouses.id')
            ->select('products.*', 'warehouses.name as warehouse_name')
            ->orderBy('products.id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');

        $headers = [
            'Product Name', 'SKU', 'Item Code', 'Category', 'Brand', 'Unit',
            'MRP', 'Purchase Price', 'Sell Price', 'GST %', 'Cess %',
            'Discount Type', 'Discount Value', 'Final Price',
            'Warehouse', 'Stock', 'Status',
        ];

        // Header row styling
        $sheet->fromArray($headers, null, 'A1');
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

        // Auto-width
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Data rows
        $row = 2;
        foreach ($products as $p) {
            $sheet->fromArray([
                $p->name,
                $p->sku,
                $p->item_code,
                $p->category,
                $p->brand,
                $p->unit,
                $p->mrp,
                $p->purchase_price,
                $p->sell_price,
                $p->gst_percent  ?? 0,
                $p->cess_percent ?? 0,
                $p->discount_type  ?? 'flat',
                $p->discount_value ?? 0,
                $p->final_price  ?? $p->sell_price,
                $p->warehouse_name ?? '',
                $p->available_units ?? 0,
                $p->status,
            ], null, "A{$row}");
            $row++;
        }

        $filename = 'products_export_' . date('Ymd_His') . '.xlsx';
        $writer   = new XlsxWriter($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function statusPage()
    {
        $products = DB::table('products')->orderBy('id', 'desc')->paginate(10);
        $totalProducts = DB::table('products')->count();
        $activeProducts = DB::table('products')->where('status', 'Active')->count();
        $inactiveProducts = DB::table('products')->where('status', 'Inactive')->count();
        return view('product.product_status', compact('products', 'totalProducts', 'activeProducts', 'inactiveProducts'));
    }

    public function toggleStatus(Request $request)
    {
        DB::table('products')->where('id', $request->id)->update(['status' => $request->status, 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    // AJAX: get warehouse stock for a product
    public function getWarehouseStock(Request $request)
    {
        $stock = DB::table('inventories')
            ->where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->value('available_units') ?? 0;
        return response()->json(['stock' => $stock]);
    }
}
