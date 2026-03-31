<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $todayOrders   = Order::count();
        $todayRevenue  = Order::sum('amount');
        $lowStock      = Inventory::where('quantity','<',10)->count();

        $recentOrders = Order::with('store')->latest()->take(5)->get();

        /*
        |----------------------------------
        | MONTHLY SALES (Dynamic)
        |----------------------------------
        */
        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total','month');

        $months = [];
        $salesData = [];

        for ($i=1; $i<=12; $i++) {
            $months[] = date('M', mktime(0,0,0,$i,1));
            $salesData[] = $monthlySales[$i] ?? 0;
        }

        /*
        |----------------------------------
        | ORDER STATUS COUNT
        |----------------------------------
        */

        $orderStatus = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total','status');

        $statusData = [
            $orderStatus['Delivered'] ?? 0,
            $orderStatus['Pending'] ?? 0,
            $orderStatus['Cancelled'] ?? 0,
        ];

        return view('dashboard.dashboard', compact(
            'totalProducts',
            'todayOrders',
            'todayRevenue',
            'lowStock',
            'recentOrders',
            'months',
            'salesData',
            'statusData'
        ));
    }
    
}