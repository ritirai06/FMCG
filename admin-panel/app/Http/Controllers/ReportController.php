<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class ReportController extends Controller
{
    // ── shared filter builder ─────────────────────────────────────────────────
    private function applyFilters($query, Request $request, string $orderAlias = 'orders', string $productAlias = 'products')
    {
        $start = $request->start_date ?: now()->subDays(29)->toDateString();
        $end   = $request->end_date   ?: now()->toDateString();

        $query->whereBetween(
            DB::raw("DATE(COALESCE({$orderAlias}.order_date, {$orderAlias}.created_at))"),
            [$start, $end]
        );

        if ($v = $request->warehouse_id)  $query->where("{$productAlias}.warehouse_id", $v);
        if ($v = $request->city)          $query->where("{$orderAlias}.city_id", $v);
        if ($v = $request->sales_person)  $query->where("{$orderAlias}.sales_person_id", $v);
        if ($v = $request->delivery_person) $query->where("{$orderAlias}.assigned_delivery", $v);
        if ($v = $request->order_status)  $query->where("{$orderAlias}.status", $v);
        if ($v = $request->category)      $query->where("{$productAlias}.category", $v);
        if ($v = $request->brand)         $query->where("{$productAlias}.brand", $v);
        if ($v = $request->product_id)    $query->where("{$productAlias}.id", $v);
        if ($v = $request->min_amount)    $query->where("{$orderAlias}.amount", '>=', $v);
        if ($v = $request->max_amount)    $query->where("{$orderAlias}.amount", '<=', $v);

        return $query;
    }

    // ── PAGE ──────────────────────────────────────────────────────────────────
    public function index()
    {
        $totalOrders        = Order::count();
        $totalSales         = Order::sum('amount');
        $pending            = Order::whereRaw('LOWER(status) = ?', ['pending'])->count();
        $delivered          = Order::whereRaw('LOWER(status) = ?', ['delivered'])->count();
        $activeSalesPersons = DB::table('sales_persons')->where('status', 'Active')->count();

        $salesTrend  = Order::select(DB::raw("DATE(created_at) as date"), DB::raw("SUM(amount) as total"))
                            ->groupBy('date')->orderBy('date')->get();
        $salesDates  = $salesTrend->pluck('date');
        $salesTotals = $salesTrend->pluck('total');

        $orderStatus = Order::select('status', DB::raw('COUNT(*) as total'))
                            ->groupBy('status')->pluck('total', 'status');

        $cities          = City::orderBy('name')->get();
        $salesPersons    = DB::table('sales_persons')->orderBy('name')->get();
        $deliveryPersons = DB::table('delivery_persons')->orderBy('name')->get();
        $warehouses      = DB::table('warehouses')->where('status', 'Active')->orderBy('name')->get();
        $categories      = DB::table('categories')->where('status', 'Active')->orderBy('name')->get();
        $brands          = DB::table('brands')->where('status', 'Active')->orderBy('name')->get();
        $products        = DB::table('products')->orderBy('name')->get(['id', 'name', 'brand', 'category']);

        $orders = Order::with(['user', 'store' => fn($q) => $q->with('city')])
            ->latest()->limit(100)->get()
            ->map(function ($order) {
                $store = $order->store;
                $order->display_store = ($store instanceof \App\Models\Store)
                    ? $store->store_name : ($store ?: 'N/A');
                return $order;
            });

        return view('report.report_index', compact(
            'totalOrders', 'totalSales', 'pending', 'delivered',
            'activeSalesPersons', 'salesDates', 'salesTotals', 'orderStatus',
            'cities', 'salesPersons', 'deliveryPersons', 'warehouses',
            'categories', 'brands', 'products', 'orders'
        ));
    }

    // ── ANALYTICS API (all filters) ───────────────────────────────────────────
    public function analytics(Request $request)
    {
        // KPI summary
        $kpiQ = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('warehouses', 'products.warehouse_id', '=', 'warehouses.id');
        $this->applyFilters($kpiQ, $request);

        $kpi = (clone $kpiQ)->select(
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(orders.amount) as total_sales'),
            DB::raw("SUM((COALESCE(products.final_price, order_items.unit_price) - COALESCE(products.purchase_price,0)) * order_items.quantity) as total_margin"),
            DB::raw("SUM(CASE WHEN LOWER(orders.status)='pending' THEN 1 ELSE 0 END) as pending"),
            DB::raw("SUM(CASE WHEN LOWER(orders.status)='delivered' THEN 1 ELSE 0 END) as delivered")
        )->first();

        // Sales trend (daily)
        $trend = (clone $kpiQ)->select(
            DB::raw("DATE(COALESCE(orders.order_date, orders.created_at)) as date"),
            DB::raw('SUM(orders.amount) as total')
        )->groupBy('date')->orderBy('date')->get();

        // Order status breakdown
        $statusBreak = (clone $kpiQ)->select(
            'orders.status',
            DB::raw('COUNT(DISTINCT orders.id) as total')
        )->groupBy('orders.status')->get()->pluck('total', 'status');

        // Margin daily (warehouse-wise)
        $marginDaily = (clone $kpiQ)->select(
            DB::raw("DATE(COALESCE(orders.order_date, orders.created_at)) as date"),
            'warehouses.name as warehouse',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(order_items.total) as revenue'),
            DB::raw("SUM((COALESCE(products.final_price, order_items.unit_price) - COALESCE(products.purchase_price,0)) * order_items.quantity) as total_margin")
        )->groupBy('date', 'warehouses.id', 'warehouses.name')->orderBy('date')->get();

        // Today margin
        $todayStr    = now()->toDateString();
        $todayMargin = $marginDaily->where('date', $todayStr)->sum('total_margin');
        $byWh        = $marginDaily->groupBy('warehouse')->map(fn($g) => $g->sum('total_margin'));
        $topWh       = $byWh->sortDesc()->keys()->first() ?? 'N/A';
        $topWhMargin = $byWh->sortDesc()->first() ?? 0;

        // Orders table (filtered)
        $ordersQ = DB::table('orders')
            ->leftJoin('sales_persons', 'orders.sales_person_id', '=', 'sales_persons.id')
            ->leftJoin('cities', 'orders.city_id', '=', 'cities.id');

        $start = $request->start_date ?: now()->subDays(29)->toDateString();
        $end   = $request->end_date   ?: now()->toDateString();
        $ordersQ->whereBetween(DB::raw("DATE(COALESCE(orders.order_date, orders.created_at))"), [$start, $end]);
        if ($v = $request->city)         $ordersQ->where('orders.city_id', $v);
        if ($v = $request->sales_person) $ordersQ->where('orders.sales_person_id', $v);
        if ($v = $request->order_status) $ordersQ->where('orders.status', $v);
        if ($v = $request->min_amount)   $ordersQ->where('orders.amount', '>=', $v);
        if ($v = $request->max_amount)   $ordersQ->where('orders.amount', '<=', $v);
        if ($v = $request->delivery_person) $ordersQ->where('orders.assigned_delivery', $v);

        $ordersTable = $ordersQ->select(
            'orders.id', 'orders.order_number',
            DB::raw("DATE(COALESCE(orders.order_date, orders.created_at)) as date"),
            'orders.customer_name', 'orders.amount', 'orders.status',
            'sales_persons.name as sp_name', 'cities.name as city_name',
            'orders.store'
        )->orderByDesc('orders.created_at')->limit(200)->get();

        return response()->json([
            'kpi' => [
                'total_orders'  => (int)($kpi->total_orders ?? 0),
                'total_sales'   => round($kpi->total_sales ?? 0, 2),
                'total_margin'  => round($kpi->total_margin ?? 0, 2),
                'today_margin'  => round($todayMargin, 2),
                'pending'       => (int)($kpi->pending ?? 0),
                'delivered'     => (int)($kpi->delivered ?? 0),
                'top_warehouse' => $topWh,
                'top_warehouse_margin' => round($topWhMargin, 2),
            ],
            'trend'        => $trend->values(),
            'status_break' => $statusBreak,
            'margin_daily' => $marginDaily->values(),
            'orders'       => $ordersTable->values(),
        ]);
    }

    // ── FILTER HELPERS (AJAX dropdowns) ───────────────────────────────────────
    public function filterBrands(Request $request)
    {
        $q = DB::table('brands')->where('status', 'Active')->orderBy('name');
        if ($cat = $request->category) {
            $q->whereIn('name', DB::table('products')->where('category', $cat)->distinct()->pluck('brand'));
        }
        return response()->json($q->get(['id', 'name']));
    }

    public function filterProducts(Request $request)
    {
        $q = DB::table('products')->orderBy('name');
        if ($cat   = $request->category) $q->where('category', $cat);
        if ($brand = $request->brand)    $q->where('brand', $brand);
        return response()->json($q->get(['id', 'name']));
    }

    // ── MARGIN API (kept for backward compat) ─────────────────────────────────
    public function margin(Request $request)
    {
        $data = $this->analytics($request)->getData(true);
        return response()->json([
            'daily'   => $data['margin_daily'],
            'summary' => [
                'total_margin'         => $data['kpi']['total_margin'],
                'today_margin'         => $data['kpi']['today_margin'],
                'top_warehouse'        => $data['kpi']['top_warehouse'],
                'top_warehouse_margin' => $data['kpi']['top_warehouse_margin'],
            ],
        ]);
    }

    // ── EXPORT (respects all filters) ─────────────────────────────────────────
    public function marginExport(Request $request)
    {
        $data   = $this->analytics($request)->getData(true);
        $rows   = $data['margin_daily'];
        $start  = $request->start_date ?: now()->subDays(29)->toDateString();
        $end    = $request->end_date   ?: now()->toDateString();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Analytics');

        // Summary sheet
        $sheet->fromArray(['Metric', 'Value'], null, 'A1');
        $sheet->fromArray([
            ['Total Orders',  $data['kpi']['total_orders']],
            ['Total Sales',   $data['kpi']['total_sales']],
            ['Total Margin',  $data['kpi']['total_margin']],
            ['Pending',       $data['kpi']['pending']],
            ['Delivered',     $data['kpi']['delivered']],
        ], null, 'A2');

        // Margin detail sheet
        $sheet2 = $spreadsheet->createSheet()->setTitle('Margin Detail');
        $sheet2->fromArray(['Date', 'Warehouse', 'Orders', 'Revenue (₹)', 'Margin (₹)'], null, 'A1');
        $sheet2->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a8a']],
        ]);
        foreach (range('A', 'E') as $col) $sheet2->getColumnDimension($col)->setAutoSize(true);

        $r = 2;
        foreach ($rows as $row) {
            $sheet2->fromArray([
                $row['date'], $row['warehouse'] ?? 'Unassigned',
                $row['order_count'], round($row['revenue'], 2), round($row['total_margin'], 2),
            ], null, "A{$r}");
            $r++;
        }

        // Orders sheet
        $sheet3 = $spreadsheet->createSheet()->setTitle('Orders');
        $sheet3->fromArray(['Date', 'Order #', 'Customer', 'Sales Person', 'City', 'Amount (₹)', 'Status'], null, 'A1');
        $sheet3->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065f46']],
        ]);
        foreach (range('A', 'G') as $col) $sheet3->getColumnDimension($col)->setAutoSize(true);

        $r = 2;
        foreach ($data['orders'] as $o) {
            $sheet3->fromArray([
                $o['date'], $o['order_number'], $o['customer_name'] ?? $o['store'],
                $o['sp_name'] ?? '', $o['city_name'] ?? '', $o['amount'], $o['status'],
            ], null, "A{$r}");
            $r++;
        }

        $filename = 'analytics_' . $start . '_to_' . $end . '.xlsx';
        $writer   = new XlsxWriter($spreadsheet);

        return response()->streamDownload(fn() => $writer->save('php://output'), $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ── LEGACY ────────────────────────────────────────────────────────────────
    public function list()    { return response()->json([]); }
    public function summary() { return response()->json([]); }
    public function chart()   { return response()->json([]); }
}
