<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $attendances = Attendance::orderBy('date','desc')
                        ->orderBy('created_at','desc')
                        ->get();

        $monthly = Attendance::selectRaw(
                    "employee_name, SUM(status='Present') as present, SUM(status='Absent') as absent, SUM(status='Late') as late"
                )
                ->whereMonth('date', now()->month)
                ->groupBy('employee_name')
                ->get();

        return view('attendance.index', compact('attendances','monthly','today'));
    }

    // Return monthly aggregated data as JSON (pro-level calculation)
    public function monthlyData(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $monthly = Attendance::selectRaw(
                    "employee_name, SUM(status='Present') as present, SUM(status='Absent') as absent, SUM(status='Late') as late"
                )
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->groupBy('employee_name')
                ->get()
                ->map(function($m) use ($month, $year){
                    $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
                    $present = intval($m->present);
                    $absent = intval($m->absent);
                    $late = intval($m->late);
                    $den = max(1, ($present + $absent));
                    $percent = intval(($present / $den) * 100);
                    return [
                        'employee_name' => $m->employee_name,
                        'present' => $present,
                        'absent' => $absent,
                        'late' => $late,
                        'attendance_percent' => $percent,
                        'working_days' => $daysInMonth,
                    ];
                });

        return response()->json(['ok' => true, 'data' => $monthly]);
    }

    // Return latest auto attendance records (could be used for auto tab)
    public function autoData(Request $request)
    {
        $rows = Attendance::orderBy('date','desc')->orderBy('created_at','desc')->limit(50)->get();
        return response()->json(['ok' => true, 'data' => $rows]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_name' => 'required|string',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $a = Attendance::create($data);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $a]);
        }

        return redirect()->route('attendance.index')
                ->with('success','Attendance Saved');
    }

    public function edit($id)
    {
        $a = Attendance::findOrFail($id);
        return response()->json($a);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'employee_name' => 'required|string',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $a = Attendance::findOrFail($id);
        $a->update($data);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $a]);
        }

        return redirect()->route('attendance.index')
                ->with('success','Attendance Updated');
    }

    public function destroy($id)
    {
        Attendance::destroy($id);

        if (request()->wantsJson() || request()->ajax() || request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('attendance.index')->with('success','Attendance Deleted');
    }
}
