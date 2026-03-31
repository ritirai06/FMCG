<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\SalesPerson;
use App\Models\Order;
use App\Models\IncentiveSlab;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $salesPeople = SalesPerson::with('city')->get();
        
        foreach($salesPeople as $sp) {
            $orders = Order::where('sales_person_id', $sp->id)->get();
            $sp->total_orders = $orders->count();
            $sp->actual_sales = $orders->sum('total_amount');
            $sp->calculated_incentive = $sp->calculateIncentive($sp->actual_sales);
            $sp->calculated_bonus = $sp->calculateBonus();
            $sp->total_salary = $sp->calculateTotalSalary($sp->actual_sales);
        }
        
        return view('salary.salary_index', compact('salesPeople'));
    }

    public function show($id)
    {
        $salesPerson = SalesPerson::with(['city', 'localities', 'assignedCities'])->findOrFail($id);
        
        $orders = Order::where('sales_person_id', $id)->latest()->get();
        $totalOrders = $orders->count();
        $actualSales = $orders->sum('total_amount');
        
        $incentive = $salesPerson->calculateIncentive($actualSales);
        $bonus = $salesPerson->calculateBonus();
        $totalSalary = $salesPerson->calculateTotalSalary($actualSales);
        
        $extraSales = max(0, $actualSales - ($salesPerson->target_sales ?? 0));
        
        return view('salary.show', compact(
            'salesPerson',
            'orders',
            'totalOrders',
            'actualSales',
            'incentive',
            'bonus',
            'totalSalary',
            'extraSales'
        ));
    }

    public function listJson()
    {
        $salaries = Salary::orderBy('employee_name')->get();
        return response()->json(['ok'=>true,'data'=>$salaries]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'employee_name' => 'required|string',
            'role' => 'nullable|string',
            'base_salary' => 'required|numeric',
            'allowances' => 'nullable|numeric'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = Salary::create($v->validated());
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function edit($id)
    {
        $s = Salary::findOrFail($id);
        return response()->json($s);
    }

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(),[
            'employee_name' => 'required|string',
            'role' => 'nullable|string',
            'base_salary' => 'required|numeric',
            'allowances' => 'nullable|numeric'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $s = Salary::findOrFail($id);
        $s->update($v->validated());
        return response()->json(['ok'=>true,'data'=>$s]);
    }

    public function destroy($id)
    {
        Salary::destroy($id);
        return response()->json(['ok'=>true]);
    }

    // Monthly summary: basic payout + incentive calculation. Accepts optional sales map via POST sales[employee_name]=amount
    public function monthlySummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $sales = $request->input('sales', []);

        $salaries = Salary::orderBy('employee_name')->get();
        $slabs = IncentiveSlab::orderBy('min_amount')->get();

        $rows = $salaries->map(function($s) use ($sales, $slabs){
            $empSales = isset($sales[$s->employee_name]) ? floatval($sales[$s->employee_name]) : 0;
            $incentivePercent = 0;
            foreach($slabs as $sl){
                $min = floatval($sl->min_amount);
                $max = $sl->max_amount !== null ? floatval($sl->max_amount) : null;
                if($empSales >= $min && ($max === null || $empSales <= $max)){
                    $incentivePercent = floatval($sl->percent);
                    break;
                }
            }
            $incentive = ($incentivePercent/100) * $empSales;
            $base = floatval($s->base_salary);
            $allow = floatval($s->allowances);
            $total = $base + $allow + $incentive;
            return [
                'employee_name' => $s->employee_name,
                'month' => now()->monthName,
                'base_salary' => $base,
                'allowances' => $allow,
                'sales' => $empSales,
                'incentive' => round($incentive,2),
                'total_payout' => round($total,2),
            ];
        });

        return response()->json(['ok'=>true,'data'=>$rows]);
    }
}
