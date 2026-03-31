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

    /**
     * Smart Check-Out: finds today's record for the employee and updates time_out.
     * Falls back to creating a new record if none exists.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string',
            'image_out'     => 'nullable|image|max:5120',
        ]);

        $today = now()->toDateString();
        $empName = $request->employee_name;

        // Try to find existing today record
        $a = Attendance::where('employee_name', $empName)
                       ->where('date', $today)
                       ->latest()
                       ->first();

        $timeNow = now()->format('H:i');

        if ($a) {
            $updateData = ['time_out' => $timeNow];
            if ($request->hasFile('image_out')) {
                if ($a->image_out) \Storage::disk('public')->delete($a->image_out);
                $updateData['image_out'] = $request->file('image_out')->store('attendance', 'public');
            }
            $a->update($updateData);
            $a->refresh();
        } else {
            // No check-in found — create a checkout-only record
            $data = [
                'employee_name' => $empName,
                'date'          => $today,
                'time_out'      => $timeNow,
                'status'        => 'Present',
                'notes'         => '',
            ];
            if ($request->hasFile('image_out')) {
                $data['image_out'] = $request->file('image_out')->store('attendance', 'public');
            }
            $a = Attendance::create($data);
        }

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $a]);
        }

        return redirect()->route('attendance.index')->with('success', 'Checked Out');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_name' => 'required|string',
            'date'          => 'required|date',
            'time_in'       => 'nullable|date_format:H:i',
            'time_out'      => 'nullable|date_format:H:i',
            'status'        => 'required|string',
            'notes'         => 'nullable|string',
            'image_in'      => 'nullable|image|max:5120',
            'image_out'     => 'nullable|image|max:5120',
        ]);

        // Handle image uploads
        if ($request->hasFile('image_in')) {
            $data['image_in'] = $request->file('image_in')->store('attendance', 'public');
        }
        if ($request->hasFile('image_out')) {
            $data['image_out'] = $request->file('image_out')->store('attendance', 'public');
        }

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
            'date'          => 'required|date',
            'time_in'       => 'nullable|date_format:H:i',
            'time_out'      => 'nullable|date_format:H:i',
            'status'        => 'required|string',
            'notes'         => 'nullable|string',
            'image_in'      => 'nullable|image|max:5120',
            'image_out'     => 'nullable|image|max:5120',
        ]);

        $a = Attendance::findOrFail($id);

        // Handle image uploads (only replace if new file uploaded)
        if ($request->hasFile('image_in')) {
            // Delete old image if exists
            if ($a->image_in) {
                \Storage::disk('public')->delete($a->image_in);
            }
            $data['image_in'] = $request->file('image_in')->store('attendance', 'public');
        } else {
            unset($data['image_in']); // keep existing
        }

        if ($request->hasFile('image_out')) {
            if ($a->image_out) {
                \Storage::disk('public')->delete($a->image_out);
            }
            $data['image_out'] = $request->file('image_out')->store('attendance', 'public');
        } else {
            unset($data['image_out']); // keep existing
        }

        $a->update($data);
        $a->refresh();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'data' => $a]);
        }

        return redirect()->route('attendance.index')
                ->with('success','Attendance Updated');
    }

    public function destroy($id)
    {
        $a = Attendance::findOrFail($id);
        // Delete images from storage
        if ($a->image_in) \Storage::disk('public')->delete($a->image_in);
        if ($a->image_out) \Storage::disk('public')->delete($a->image_out);
        $a->delete();

        if (request()->wantsJson() || request()->ajax() || request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('attendance.index')->with('success','Attendance Deleted');
    }
}
