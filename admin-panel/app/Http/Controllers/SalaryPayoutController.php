<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryPayout;

class SalaryPayoutController extends Controller
{
    // Store multiple payout rows
    public function store(Request $request)
    {
        $rows = $request->input('rows', []);
        $saved = [];
        foreach($rows as $r){
            $p = SalaryPayout::create([
                'employee_name' => $r['employee_name'] ?? null,
                'month' => $r['month'] ?? now()->month,
                'year' => $r['year'] ?? now()->year,
                'base_salary' => $r['base_salary'] ?? 0,
                'allowances' => $r['allowances'] ?? 0,
                'sales' => $r['sales'] ?? 0,
                'incentive' => $r['incentive'] ?? 0,
                'total_payout' => $r['total_payout'] ?? 0,
            ]);
            $saved[] = $p;
        }
        return response()->json(['ok'=>true,'data'=>$saved]);
    }

    public function list(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        $q = SalaryPayout::query();
        if($month) $q->where('month',$month);
        if($year) $q->where('year',$year);
        $data = $q->orderBy('employee_name')->get();
        return response()->json(['ok'=>true,'data'=>$data]);
    }
}
