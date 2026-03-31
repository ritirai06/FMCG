<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\SalesPerson;
use App\Models\Store;
use App\Models\Locality;
use App\Models\User;
use App\Models\AdminSetting;
use App\Models\Attendance;
use App\Helpers\OrderHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        return redirect()->route('sale.panel.dashboard');
    }

    public function indexOld()
    {
        // Current logged-in sales person
        $user = Auth::user();
        
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::with(['city', 'localities'])->find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->with(['city', 'localities'])->first();
        }

        // Company settings
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';

        $baseOrderQuery = Order::query();
        if ($user && method_exists($user, 'isSales') && $user->isSales()) {
            $baseOrderQuery->where('created_by', $user->id);
        }

        // Dashboard metrics
        $totalProducts = Product::count();
        $todayOrders = (clone $baseOrderQuery)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $todayRevenue = (clone $baseOrderQuery)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount') ?? 0;
        $monthlySalesSummary = (clone $baseOrderQuery)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount') ?? 0;
        $lowStock      = Inventory::where('quantity','<',10)->count();

        // Recent orders with store details
        $recentOrders = (clone $baseOrderQuery)
            ->with('store')
            ->latest()
            ->take(5)
            ->get();

        $attendanceStatus = 'Absent';
        $attendanceNames = collect([$salesPerson?->name, $user?->name])
            ->filter()
            ->map(fn ($name) => trim((string) $name))
            ->unique()
            ->values();
        if ($attendanceNames->isNotEmpty()) {
            $todayAttendance = Attendance::query()
                ->whereDate('date', now()->toDateString())
                ->whereIn('employee_name', $attendanceNames->all())
                ->latest('id')
                ->first();
            if ($todayAttendance) {
                $attendanceStatus = (string) $todayAttendance->status;
            }
        }

        $assignedLocalities = collect($salesPerson?->localities ?? [])
            ->pluck('name')
            ->filter()
            ->values();

        // Assigned stores - get stores with their order data
        $assignedStoresData = [];
        $stores = Store::all();
        
        foreach ($stores as $store) {
            $thisMonthOrders = $store->orders()
                ->whereMonth('created_at', now()->month)
                ->get();
            
            $pendingCount = $thisMonthOrders->whereIn('status', ['Pending', 'Processing', 'Out for Delivery'])->count();
            $deliveredCount = $thisMonthOrders->where('status', 'Delivered')->count();
            $totalRevenue = $thisMonthOrders->sum('amount');
            
            $assignedStoresData[] = (object)[
                'id' => $store->id,
                'store_name' => $store->store_name,
                'code' => $store->code,
                'manager' => $store->manager,
                'phone' => $store->phone,
                'address' => $store->address,
                'status' => $store->status,
                'pending_orders' => $pendingCount,
                'delivered_orders' => $deliveredCount,
                'monthly_revenue' => $totalRevenue,
            ];
        }
        
        $assignedStores = collect($assignedStoresData);

        // ===== DAILY DATA (Last 7 days) =====
        $dailyLabels = [];
        $dailySales = [];
        $dailyOrders = [];
        $dailyReturned = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('D');
            
            $sales = Order::whereDate('created_at', $date->format('Y-m-d'))->sum('amount') ?? 0;
            $orders = Order::whereDate('created_at', $date->format('Y-m-d'))->count();
            $returned = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->whereIn('status', ['Cancelled', 'Returned', 'Failed'])->count();
            
            $dailySales[] = (int)$sales;
            $dailyOrders[] = $orders;
            $dailyReturned[] = $returned;
        }

        // ===== WEEKLY DATA (Last 4 weeks) =====
        $weeklyLabels = [];
        $weeklySales = [];
        $weeklyOrders = [];
        $weeklyReturned = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weeklyLabels[] = 'W' . ($i + 1);
            
            $sales = Order::whereBetween('created_at', [$weekStart, $weekEnd])->sum('amount') ?? 0;
            $orders = Order::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $returned = Order::whereBetween('created_at', [$weekStart, $weekEnd])
                ->whereIn('status', ['Cancelled', 'Returned', 'Failed'])->count();
            
            $weeklySales[] = (int)$sales;
            $weeklyOrders[] = $orders;
            $weeklyReturned[] = $returned;
        }

        // ===== MONTHLY DATA =====
        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total','month');

        // Monthly orders placed
        $monthlyOrdersPlaced = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total','month');

        // Monthly orders returned/cancelled
        $monthlyReturned = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('status', ['Cancelled', 'Returned', 'Failed'])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total','month');

        $months = [];
        $salesData = [];
        $ordersPlacedData = [];
        $ordersReturnedData = [];
        for ($i=1; $i<=12; $i++) {
            $months[] = date('M', mktime(0,0,0,$i,1));
            $salesData[] = (int)($monthlySales[$i] ?? 0);
            $ordersPlacedData[] = (int)($monthlyOrdersPlaced[$i] ?? 0);
            $ordersReturnedData[] = (int)($monthlyReturned[$i] ?? 0);
        }

        // Order status breakdown
        $orderStatus = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total','status');

        $statusData = [
            $orderStatus['Delivered'] ?? 0,
            $orderStatus['Pending'] ?? 0,
            $orderStatus['Cancelled'] ?? 0,
        ];

        // ===== ORDER ACTIVITY STATS =====
        // Daily delivery stats
        $dailyDelivered = Order::whereDate('created_at', now()->format('Y-m-d'))
            ->where('status', 'Delivered')->count();
        $dailySalesValue = Order::whereDate('created_at', now()->format('Y-m-d'))->sum('amount') ?? 0;

        // Weekly delivery stats  
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyDelivered = Order::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 'Delivered')->count();
        $weeklySalesValue = Order::whereBetween('created_at', [$weekStart, $weekEnd])->sum('amount') ?? 0;

        // Monthly delivery stats
        $monthlyDelivered = Order::whereMonth('created_at', now()->month)->where('status', 'Delivered')->count();
        $monthlySalesValue = Order::whereMonth('created_at', now()->month)->sum('amount') ?? 0;

        // ===== SALES COVERAGE METRICS =====
        // Get total stores and assigned stores
        $allStoresCount = Store::count();
        $assignedStoresCount = $assignedStores->count();
        
        // Retail Stores - show all available stores as assigned
        $retailStores = $assignedStoresCount;
        $retailCoverage = $allStoresCount > 0 ? round(($assignedStoresCount / $allStoresCount) * 100) : 0;
        $retailFraction = $assignedStoresCount . '/' . $allStoresCount;
        
        // Wholesale clients - stores not yet included (for future expansion)
        $wholesaleCount = 0; // No separate wholesale tracking
        $wholesaleCoverage = 0;
        $wholesaleFraction = '0/0';
        
        // Get store IDs for all assigned stores
        $assignedStoreIds = $assignedStores->pluck('id')->toArray();
        
        // Total orders from all assigned stores in this month
        $totalOrdersAllTime = Order::count();
        $totalOrdersThisMonth = Order::whereMonth('created_at', now()->month)->count();
        
        // Orders delivered from assigned stores this month
        $ordersDeliveredThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereIn('store_id', $assignedStoreIds)
            ->where('status', 'Delivered')
            ->count();
        
        // Total orders from assigned stores this month
        $assignedOrdersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereIn('store_id', $assignedStoreIds)
            ->count();
        
        $deliveryCompletion = $assignedOrdersThisMonth > 0 ? round(($ordersDeliveredThisMonth / $assignedOrdersThisMonth) * 100) : 0;
        $deliveryFraction = $ordersDeliveredThisMonth . '/' . $assignedOrdersThisMonth;
        
        // Sales target achievement from assigned stores
        $salesTargetAmount = 1500000; // 15 Lakhs as target
        $currentMonthSales = Order::whereMonth('created_at', now()->month)
            ->whereIn('store_id', $assignedStoreIds)
            ->sum('amount') ?? 0;
        
        $salesTargetPercentage = $salesTargetAmount > 0 ? round(($currentMonthSales / $salesTargetAmount) * 100) : 0;
        $salesTargetPercentage = min($salesTargetPercentage, 100); // Cap at 100%
        $salesAchievedText = '₹' . number_format($currentMonthSales / 100000, 1, '.', '') . 'L';

        return view('sale.dashboard', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'totalProducts',
            'todayOrders',
            'todayRevenue',
            'monthlySalesSummary',
            'attendanceStatus',
            'assignedLocalities',
            'lowStock',
            'recentOrders',
            'assignedStores',
            'dailyLabels','dailySales','dailyOrders','dailyReturned',
            'weeklyLabels','weeklySales','weeklyOrders','weeklyReturned',
            'months','salesData','ordersPlacedData','ordersReturnedData','statusData',
            'dailyDelivered','dailySalesValue','weeklyDelivered','weeklySalesValue',
            'monthlyDelivered','monthlySalesValue',
            'retailStores','retailCoverage','retailFraction',
            'wholesaleCount','wholesaleCoverage','wholesaleFraction',
            'ordersDeliveredThisMonth','deliveryCompletion','deliveryFraction',
            'salesTargetPercentage','salesAchievedText'
        ));
    }

    // Orders page
    public function orders(Request $request)
    {
        $user = Auth::user();

        // Get filters from request
        $filters = [
            'status' => $request->get('status', 'all'),
            'store' => $request->get('store', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $applyRoleScope = function ($query) use ($user) {
            if ($user) {
                $hasUserRoleColumn = Schema::hasTable('users') && Schema::hasColumn('users', 'role');
                $hasCreatedByColumn = Schema::hasColumn('orders', 'created_by');
                $hasAssignedDeliveryColumn = Schema::hasColumn('orders', 'assigned_delivery');

                // If role column does not exist, do not hard-filter; show real orders.
                if (!$hasUserRoleColumn) {
                    return;
                }

                if ($user->isAdmin()) {
                    // Admin sees all orders
                } elseif ($user->isSales()) {
                    if ($hasCreatedByColumn) {
                        $query->where('created_by', $user->id);
                    }
                } elseif ($user->isDelivery()) {
                    if ($hasAssignedDeliveryColumn) {
                        $query->where('assigned_delivery', $user->id);
                    }
                } else {
                    // Unknown role: keep real data visible instead of forcing empty result.
                }
            }
        };

        $applyFilterScope = function ($query) use ($filters) {
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                // Keep status filtering resilient to mixed-case values in DB
                $query->whereRaw('LOWER(status) = ?', [strtolower((string) $filters['status'])]);
            }

            if (!empty($filters['store']) && $filters['store'] !== 'all') {
                $query->where('store_id', $filters['store']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }
        };

        // Build base query for listing
        $query = Order::with(['store', 'salesPerson', 'createdBy', 'assignedDelivery', 'items']);
        $applyRoleScope($query);
        $applyFilterScope($query);

        // Get paginated orders
        $orders = $query->latest('created_at')->paginate(10);

        // Get available statuses
        $statuses = OrderHelper::getOrderStatuses();

        // Get stores for filter (role-based)
        $stores = collect();
        if ($user && $user->isAdmin()) {
            $stores = Store::orderBy('store_name')->get();
        } elseif ($user && $user->isSales() && Schema::hasColumn('orders', 'created_by')) {
            $stores = Store::whereHas('orders', function ($q) {
                $q->where('created_by', Auth::id());
            })->orderBy('store_name')->get();
        } else {
            $stores = Store::orderBy('store_name')->get();
        }

        // Get delivery persons (schema-safe for older/newer user table variants)
        $deliveryPersons = collect();
        if (Schema::hasTable('users')) {
            $deliveryQuery = User::query();

            if (Schema::hasColumn('users', 'role')) {
                $deliveryQuery->where('role', 'delivery');
            } elseif (Schema::hasColumn('users', 'user_type')) {
                $deliveryQuery->where('user_type', 'delivery');
            } elseif (Schema::hasColumn('users', 'type')) {
                $deliveryQuery->where('type', 'delivery');
            } else {
                // No role-like column, avoid selecting wrong users
                $deliveryQuery->whereRaw('1 = 0');
            }

            if (Schema::hasColumn('users', 'status')) {
                $deliveryQuery->where('status', true);
            }

            $deliveryPersons = $deliveryQuery->orderBy('name')->get();
        }

        // Get summary statistics (role scoped, unfiltered)
        $summaryQuery = Order::query();
        $applyRoleScope($summaryQuery);
        $orderAmountColumn = Schema::hasColumn('orders', 'total_amount') ? 'total_amount' : 'amount';

        $summary = [
            'total' => $summaryQuery->count(),
            'pending' => (clone $summaryQuery)->where('status', 'Pending')->count(),
            'approved' => (clone $summaryQuery)->where('status', 'Approved')->count(),
            'packed' => (clone $summaryQuery)->where('status', 'Packed')->count(),
            'out_for_delivery' => (clone $summaryQuery)->where('status', 'Out for Delivery')->count(),
            'delivered' => (clone $summaryQuery)->where('status', 'Delivered')->count(),
            'cancelled' => (clone $summaryQuery)->where('status', 'Cancelled')->count(),
            'total_amount' => (clone $summaryQuery)->sum($orderAmountColumn) ?? 0,
        ];

        // Right panel: real DB stats (same scope as table: role + filters)
        $personOrdersBase = Order::query();
        $applyRoleScope($personOrdersBase);
        $applyFilterScope($personOrdersBase);

        $personOrderCount = (clone $personOrdersBase)->count();
        $personRevenue = (float)((clone $personOrdersBase)->sum($orderAmountColumn) ?? 0);
        $personDelivered = (clone $personOrdersBase)->where('status', 'Delivered')->count();
        $personPending = (clone $personOrdersBase)->where('status', 'Pending')->count();
        $personCancelled = (clone $personOrdersBase)->where('status', 'Cancelled')->count();
        $personRecentOrders = (clone $personOrdersBase)
            ->with('store')
            ->latest('created_at')
            ->take(8)
            ->get();

        // Legacy data for backward compatibility
        $salesPerson = null;
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::with('city')->find($user->sales_person_id);
        }
        
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::with('city')->where('email', $user->email)->first();
        }

        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';

        return view('sale.order.order_list', compact(
            'user',
            'orders',
            'statuses',
            'stores',
            'deliveryPersons',
            'summary',
            'filters',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'personOrderCount',
            'personRevenue',
            'personDelivered',
            'personPending',
            'personCancelled',
            'personRecentOrders'
        ));
    }

    // Stores page
    public function stores()
    {
        $user =Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }
        
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';

        // Filters for embedded order table on store list page
        $filters = [
            'status' => request()->get('status', 'all'),
            'store' => request()->get('store', 'all'),
            'date_from' => request()->get('date_from'),
            'date_to' => request()->get('date_to'),
        ];

        $orderAmountColumn = Schema::hasColumn('orders', 'total_amount') ? 'total_amount' : 'amount';

        // Get all stores with dynamic order counters
        $stores = Store::query()
            ->withCount('orders')
            ->withCount([
                'orders as delivered_orders_count' => function ($q) {
                    $q->where('status', 'Delivered');
                }
            ])
            ->latest('created_at')
            ->get();

        // Add dynamic revenue + inventory metrics per store
        $stores = $stores->map(function ($store) use ($orderAmountColumn) {
            $store->total_revenue = (float) Order::where('store_id', $store->id)->sum($orderAmountColumn);

            $store->sku_count_total = 0;
            $store->low_stock_total = 0;
            if (Schema::hasTable('store_inventories')) {
                $inventoryQuery = DB::table('store_inventories')->where('store_id', $store->id);
                $store->sku_count_total = (int) (clone $inventoryQuery)->count();

                if (Schema::hasColumn('store_inventories', 'quantity')) {
                    $store->low_stock_total = (int) (clone $inventoryQuery)->where('quantity', '<', 10)->count();
                }
            }

            return $store;
        });

        $storeSummary = [
            'total' => $stores->count(),
            'active' => $stores->where('status', true)->count(),
            'inactive' => $stores->where('status', false)->count(),
            'total_sku' => (int) $stores->sum('sku_count_total'),
            'low_stock_items' => (int) $stores->sum('low_stock_total'),
        ];

        // Order dataset used by this view's order table
        $ordersQuery = Order::with(['store', 'createdBy', 'assignedDelivery'])
            ->latest('created_at');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $ordersQuery->whereRaw('LOWER(status) = ?', [strtolower((string) $filters['status'])]);
        }
        if (!empty($filters['store']) && $filters['store'] !== 'all') {
            $ordersQuery->where('store_id', $filters['store']);
        }
        if (!empty($filters['date_from'])) {
            $ordersQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $ordersQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        $orders = $ordersQuery->paginate(10)->appends(request()->query());
        $statuses = OrderHelper::getOrderStatuses();
        
        // Get recent 5 orders
        $recentOrders = Order::with('store')->latest()->take(5)->get();

        return view('sale.store.store_list', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'stores',
            'storeSummary',
            'orders',
            'statuses',
            'filters',
            'recentOrders'
        ));
    }

    // Attendance page
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }
        
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        // Get real data for today
        $todayOrders = Order::whereDate('created_at', now()->toDateString())->count();
        $todayRevenue = Order::whereDate('created_at', now()->toDateString())->sum('amount') ?? 0;
        $storesVisited = Store::count();
        
        // Recent orders for display
        $recentOrders = Order::with('store')->latest()->take(5)->get();

        $attendanceNameCandidates = collect([
            $salesPerson?->name ?? null,
            $user?->name ?? null,
        ])->filter()->map(fn ($n) => trim((string) $n))->unique()->values();

        // Attendance records for selected month/year (sales user scoped when possible)
        $attendanceBase = \App\Models\Attendance::query()
            ->when($attendanceNameCandidates->isNotEmpty(), function ($q) use ($attendanceNameCandidates) {
                $q->whereIn('employee_name', $attendanceNameCandidates->all());
            })
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear);

        $attendanceRecords = (clone $attendanceBase)
            ->latest('date')
            ->paginate(10)
            ->appends($request->query());
        $attendanceCalendarRecords = (clone $attendanceBase)
            ->select(['date', 'status'])
            ->get();

        $presentCount = (clone $attendanceBase)->whereRaw('LOWER(status) = ?', ['present'])->count();
        $absentCount = (clone $attendanceBase)->whereRaw('LOWER(status) = ?', ['absent'])->count();
        $lateCount = (clone $attendanceBase)->whereRaw('LOWER(status) = ?', ['late'])->count();
        $leaveCount = (clone $attendanceBase)->whereRaw('LOWER(status) = ?', ['leave'])->count();
        $totalMarked = $presentCount + $absentCount + $lateCount + $leaveCount;

        $attendanceSummary = [
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'leave' => $leaveCount,
            'attendance_rate' => $totalMarked > 0 ? round((($presentCount + $lateCount) / $totalMarked) * 100, 1) : 0,
            'today_present' => \App\Models\Attendance::query()
                ->whereDate('date', now()->toDateString())
                ->whereRaw('LOWER(status) = ?', ['present'])
                ->count(),
        ];
        $todayAttendanceRecord = \App\Models\Attendance::query()
            ->when($attendanceNameCandidates->isNotEmpty(), function ($q) use ($attendanceNameCandidates) {
                $q->whereIn('employee_name', $attendanceNameCandidates->all());
            })
            ->whereDate('date', now()->toDateString())
            ->latest('id')
            ->first();

        $productsCount = Product::count();
        $totalStockUnits = \App\Models\Inventory::sum('quantity') ?? 0;
        $lowStockProducts = Schema::hasColumn('products', 'stock_quantity')
            ? Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)->count()
            : 0;
        $outOfStockProducts = Schema::hasColumn('products', 'stock_quantity')
            ? Product::where('stock_quantity', '<=', 0)->count()
            : 0;

        $stockInToday = \App\Models\InventoryLog::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereRaw('LOWER(type) = ?', ['in'])
            ->sum('quantity') ?? 0;
        $stockOutToday = \App\Models\InventoryLog::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereRaw('LOWER(type) = ?', ['out'])
            ->sum('quantity') ?? 0;

        $inventorySummary = [
            'products' => (int) $productsCount,
            'total_stock_units' => (int) $totalStockUnits,
            'low_stock_products' => (int) $lowStockProducts,
            'out_of_stock_products' => (int) $outOfStockProducts,
            'stock_in_today' => (int) $stockInToday,
            'stock_out_today' => (int) $stockOutToday,
            'store_sku_total' => (int) (\App\Models\StoreInventory::sum('sku_count') ?? 0),
            'store_low_stock_total' => (int) (\App\Models\StoreInventory::sum('low_stock_items') ?? 0),
        ];

        $recentInventoryLogs = \App\Models\InventoryLog::query()
            ->with(['inventory.product', 'inventory.warehouse'])
            ->latest('id')
            ->take(12)
            ->get()
            ->map(function ($log) {
                return (object) [
                    'type' => strtolower((string) ($log->type ?? '')),
                    'quantity' => (int) ($log->quantity ?? 0),
                    'product_name' => $log->inventory?->product?->name ?? 'N/A',
                    'warehouse_name' => $log->inventory?->warehouse?->name ?? 'N/A',
                    'created_at' => $log->created_at,
                ];
            });

        return view('sale.attendance.index', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'selectedMonth',
            'selectedYear',
            'attendanceSummary',
            'todayAttendanceRecord',
            'inventorySummary',
            'attendanceRecords',
            'attendanceCalendarRecords',
            'recentInventoryLogs',
            'todayOrders',
            'todayRevenue',
            'storesVisited',
            'recentOrders'
        ));
    }

    // Profile page
    public function profile()
    {
        $user = Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->with('city')->first();
        }
        
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';

        $profileOrderQuery = Order::query();
        if ($user && $user->isSales()) {
            $profileOrderQuery->where('created_by', $user->id);
        } elseif ($user && $user->isDelivery()) {
            $profileOrderQuery->where('assigned_delivery', $user->id);
        }

        $orderAmountColumn = Schema::hasColumn('orders', 'total_amount') ? 'total_amount' : 'amount';
        $profileStats = [
            'total_orders' => (clone $profileOrderQuery)->count(),
            'delivered_orders' => (clone $profileOrderQuery)->where('status', 'Delivered')->count(),
            'pending_orders' => (clone $profileOrderQuery)->where('status', 'Pending')->count(),
            'total_revenue' => (clone $profileOrderQuery)->sum($orderAmountColumn) ?? 0,
        ];

        $attendanceQuery = Attendance::query();
        if (Schema::hasColumn('attendances', 'employee_name')) {
            $attendanceName = $salesPerson?->name ?: ($user?->name ?? null);
            if ($attendanceName) {
                $attendanceQuery->where('employee_name', $attendanceName);
            }
        }
        if (Schema::hasColumn('attendances', 'date')) {
            $attendanceQuery->whereMonth('date', now()->month)->whereYear('date', now()->year);
        }
        $profileStats['present_days_this_month'] = (clone $attendanceQuery)->where('status', 'Present')->count();
        $profileStats['attendance_records_this_month'] = (clone $attendanceQuery)->count();

        return view('sale.profile', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'profileStats'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('sale.login')->with('error', 'Please login again.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->name = $validated['name'];
        if (Schema::hasColumn('users', 'phone')) {
            $user->phone = $validated['phone'] ?? $user->phone;
        }
        if (Schema::hasColumn('users', 'mobile')) {
            $user->mobile = $validated['phone'] ?? $user->mobile;
        }
        $user->save();

        $salesPerson = null;
        if (property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        if (!$salesPerson && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }
        if ($salesPerson) {
            $salesPerson->name = $validated['name'];
            if (Schema::hasColumn('sales_persons', 'phone')) {
                $salesPerson->phone = $validated['phone'] ?? $salesPerson->phone;
            }
            $salesPerson->save();
        }

        return redirect()->route('sale.profile')->with('success', 'Profile updated successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sale.login');
    }

    // Create Order page
    public function createOrder()
    {
        $user = Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }
        
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';
        
        // Get all stores ordered by name
        $stores = Store::orderBy('store_name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('sale.order.create', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'stores',
            'products'
        ));
    }

    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'status' => ['required', 'in:Present,Absent,Late,Leave'],
            'notes' => ['nullable', 'string', 'max:500'],
            'action_type' => ['nullable', 'in:check_in,check_out'],
        ]);

        $user = Auth::user();
        $salesPerson = null;
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }

        $attendanceDate = $validated['date'] ?? now()->toDateString();
        $employeeName = trim((string) ($salesPerson?->name ?: ($user?->name ?? 'Sales User')));

        $attendance = Attendance::firstOrNew([
            'employee_name' => $employeeName,
            'date' => $attendanceDate,
        ]);
        $attendance->status = $validated['status'];
        if (!empty($validated['notes'])) {
            $attendance->notes = $validated['notes'];
        }

        $actionType = $validated['action_type'] ?? null;
        if ($actionType === 'check_in' && empty($attendance->time_in)) {
            $attendance->time_in = now()->format('H:i:s');
        }
        if ($actionType === 'check_out') {
            if (empty($attendance->time_in)) {
                $attendance->time_in = now()->format('H:i:s');
            }
            $attendance->time_out = now()->format('H:i:s');
        }

        $attendance->save();

        $month = (int) \Carbon\Carbon::parse($attendanceDate)->month;
        $year = (int) \Carbon\Carbon::parse($attendanceDate)->year;

        return redirect()->route('sale.attendance', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Attendance marked successfully.');
    }

    // Store Order
    public function storeOrder(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::where('email', $user->email)->first();
        }
        
        // If no sales person found, show error
        if (!$salesPerson) {
            return redirect()->back()->with('error', 'Sales person profile not found. Please contact administrator.')
                                      ->withInput();
        }

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Pending,Processing,Out for Delivery,Delivered,Cancelled',
            'contact_number' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        do {
            $generatedOrderNumber = 'ORD' . now()->format('YmdHis') . rand(10, 99);
        } while (Order::where('order_number', $generatedOrderNumber)->exists());

        try {
            DB::beginTransaction();

            $lineItems = [];
            $totalAmount = 0.0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $available = (int) ($product->stock_quantity ?? 0);
                if ($available < $qty) {
                    throw new \RuntimeException("Insufficient stock for {$product->name}. Available: {$available}");
                }

                $unitPrice = (float) ($product->sale_price ?? $product->mrp ?? $product->price ?? 0);
                $subtotal = $unitPrice * $qty;
                $totalAmount += $subtotal;

                $lineItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name ?: $product->product_name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ];
            }

            $order = Order::create([
                'order_number' => $generatedOrderNumber,
                'store_id' => $validated['store_id'],
                'sales_person_id' => $salesPerson->id,
                'created_by' => $user?->id,
                'customer' => $validated['customer'] ?? null,
                'customer_name' => $validated['customer'] ?? null,
                'customer_phone' => $validated['contact_number'] ?? null,
                'order_date' => $validated['order_date'] ?: now()->toDateString(),
                'amount' => $totalAmount,
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $request->notes ?? null
            ]);

            foreach ($lineItems as $line) {
                OrderItem::create(array_merge($line, ['order_id' => $order->id]));

                // Keep both product and central inventory in sync
                $product = Product::find($line['product_id']);
                if ($product) {
                    $product->decrement('stock_quantity', $line['quantity']);
                }

                $inventory = Inventory::where('product_id', $line['product_id'])->first();
                if ($inventory) {
                    $inventory->quantity = max(0, (int)$inventory->quantity - (int)$line['quantity']);
                    $inventory->save();
                }
            }

            DB::commit();

            return redirect()->route('sale.order.list')->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage())
                                      ->withInput();
        }
    }

    // Create Store page
    public function createStore()
    {
        $user = Auth::user();
        $salesPerson = null;
        
        // Try to find sales person by ID first
        if ($user && property_exists($user, 'sales_person_id') && $user->sales_person_id) {
            $salesPerson = SalesPerson::with(['city', 'localities'])->find($user->sales_person_id);
        }
        
        // If not found, try by email
        if (!$salesPerson && $user && $user->email) {
            $salesPerson = SalesPerson::with(['city', 'localities'])->where('email', $user->email)->first();
        }
        
        $companySettings = AdminSetting::first();
        $companyName = $companySettings?->company_name ?? 'SalePanel';
        $salesRegion = $salesPerson?->city?->city_name ?? 'Regional Sales';

        // Dynamic real data for the create screen
        $recentStores = Store::latest()->take(5)->get();

        // Locality dropdown data:
        // 1) Assigned localities of sales person
        // 2) Fallback: same city localities
        // 3) Final fallback: all localities
        $assignedLocalities = collect($salesPerson?->localities ?? [])->values();
        if ($assignedLocalities->isEmpty()) {
            $localityQuery = Locality::query();

            if ($salesPerson && Schema::hasColumn('sales_persons', 'city_id') && !empty($salesPerson->city_id) && Schema::hasColumn('localities', 'city_id')) {
                $localityQuery->where('city_id', $salesPerson->city_id);
            }

            if (Schema::hasColumn('localities', 'status')) {
                $localityQuery->where('status', true);
            }

            $assignedLocalities = $localityQuery->orderBy('name')->get();
        }

        if ($assignedLocalities->isEmpty()) {
            $assignedLocalities = Locality::query()->orderBy('name')->get();
        }

        // Fallback: derive locality names from existing store addresses (Locality: xyz)
        if ($assignedLocalities->isEmpty()) {
            $derived = Store::query()
                ->whereNotNull('address')
                ->pluck('address')
                ->map(function ($address) {
                    $text = (string) $address;
                    if (preg_match('/Locality:\s*([^\r\n]+)/i', $text, $m)) {
                        return trim((string) $m[1]);
                    }
                    return null;
                })
                ->filter()
                ->unique()
                ->values()
                ->map(fn ($name) => (object) ['name' => $name]);

            if ($derived->isNotEmpty()) {
                $assignedLocalities = $derived;
            }
        }

        // Final fallback option so dropdown is always visible/selectable
        if ($assignedLocalities->isEmpty()) {
            $assignedLocalities = collect([(object) ['name' => 'General Area']]);
        }
        $storeStats = [
            'total' => Store::count(),
            'active' => Store::where('status', true)->count(),
            'inactive' => Store::where('status', false)->count(),
        ];

        return view('sale.store.create', compact(
            'user',
            'salesPerson',
            'companyName',
            'companySettings',
            'salesRegion',
            'recentStores',
            'assignedLocalities',
            'storeStats'
        ));
    }

    // Store Store
    public function storeStore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:stores,code',
            'manager' => 'nullable|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'locality_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'nullable|boolean',
        ]);

        $code = $request->code;
        if (!$code) {
            do {
                $code = 'STR' . now()->format('ymd') . rand(100, 999);
            } while (Store::where('code', $code)->exists());
        }

        $address = trim((string) $request->address);
        $localityName = trim((string) $request->input('locality_name', ''));
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($localityName !== '') {
            $address .= "\nLocality: " . $localityName;
        }
        if ($latitude !== null && $longitude !== null && $latitude !== '' && $longitude !== '') {
            $address .= "\nGPS: " . $latitude . ', ' . $longitude;
        }

        Store::create([
            'store_name' => $request->store_name,
            'code' => $code,
            'manager' => $request->manager ?: (Auth::user()->name ?? null),
            'phone' => $request->contact_number,
            'address' => $address,
            'status' => (bool) $request->input('status', true),
        ]);

        return redirect()->route('sale.stores')->with('success', 'Store created successfully!');
    }
}
