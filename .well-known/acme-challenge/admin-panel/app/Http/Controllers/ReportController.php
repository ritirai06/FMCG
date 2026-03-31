<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {

        /* ================= KPI ================= */

        $totalOrders = Order::count();
        $totalSales  = Order::sum('amount');
        $pending     = Order::where('status','pending')->count();
        $delivered   = Order::where('status','delivered')->count();

        $activeSalesPersons = User::count();


        /* ================= SALES TREND ================= */

        $salesTrend = Order::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("SUM(amount) as total")
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $salesDates  = $salesTrend->pluck('date');
        $salesTotals = $salesTrend->pluck('total');


        /* ================= ORDER STATUS ================= */

        $orderStatus = Order::select(
            'status',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('status')
        ->pluck('total','status');


        /* ================= FILTER DATA ================= */

        $cities = City::all();
        $salesPersons = User::all();


        /* ================= TABLE DATA ================= */

        $orders = Order::with(['user','store','city'])
            ->latest()
            ->get();


        return view('report.report_index', compact(
            'totalOrders',
            'totalSales',
            'pending',
            'delivered',
            'activeSalesPersons',
            'salesDates',
            'salesTotals',
            'orderStatus',
            'cities',
            'salesPersons',
            'orders'
        ));
    }
}