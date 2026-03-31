@extends('sale.layout')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="me-auto">
                <h3 class="mb-1">Attendance & Inventory Operations</h3>
                <p class="text-muted mb-0">Live monthly attendance with stock and inventory movement insights.</p>
            </div>
            <form method="GET" action="{{ route('sale.attendance') }}" class="d-flex gap-2">
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($selectedMonth == $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary">Apply</button>
            </form>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Present</p><h4 class="mb-0 text-success">{{ $attendanceSummary['present'] }}</h4></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Absent</p><h4 class="mb-0 text-danger">{{ $attendanceSummary['absent'] }}</h4></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Late</p><h4 class="mb-0 text-warning">{{ $attendanceSummary['late'] }}</h4></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Leave</p><h4 class="mb-0 text-secondary">{{ $attendanceSummary['leave'] }}</h4></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Attendance Rate</p><h4 class="mb-0">{{ $attendanceSummary['attendance_rate'] }}%</h4></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Today Present</p><h4 class="mb-0">{{ $attendanceSummary['today_present'] }}</h4></div></div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Total Products</p><h4 class="mb-0">{{ number_format($inventorySummary['products']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Total Stock Units</p><h4 class="mb-0">{{ number_format($inventorySummary['total_stock_units']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Low Stock Products</p><h4 class="mb-0 text-warning">{{ number_format($inventorySummary['low_stock_products']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Out of Stock</p><h4 class="mb-0 text-danger">{{ number_format($inventorySummary['out_of_stock_products']) }}</h4></div></div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Stock In Today</p><h4 class="mb-0 text-success">{{ number_format($inventorySummary['stock_in_today']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Stock Out Today</p><h4 class="mb-0 text-danger">{{ number_format($inventorySummary['stock_out_today']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Store SKU Total</p><h4 class="mb-0">{{ number_format($inventorySummary['store_sku_total']) }}</h4></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Store Low Stock Items</p><h4 class="mb-0 text-warning">{{ number_format($inventorySummary['store_low_stock_total']) }}</h4></div></div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Attendance Records</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Status</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceRecords as $record)
                                    <tr>
                                        <td>{{ $record->date?->format('d M Y') }}</td>
                                        <td>{{ $record->employee_name }}</td>
                                        <td>
                                            @php
                                                $status = strtolower($record->status ?? 'absent');
                                                $cls = str_contains($status, 'present') ? 'success' : (str_contains($status, 'late') ? 'warning' : (str_contains($status, 'leave') ? 'info' : 'danger'));
                                            @endphp
                                            <span class="badge bg-{{ $cls }}">{{ $record->status }}</span>
                                        </td>
                                        <td>{{ $record->time_in ?: '-' }}</td>
                                        <td>{{ $record->time_out ?: '-' }}</td>
                                        <td>{{ $record->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No attendance records found for selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($attendanceRecords->count())
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted">Showing {{ ($attendanceRecords->currentPage() - 1) * $attendanceRecords->perPage() + 1 }} to {{ min($attendanceRecords->currentPage() * $attendanceRecords->perPage(), $attendanceRecords->total()) }} of {{ $attendanceRecords->total() }}</small>
                            {{ $attendanceRecords->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Inventory Movements</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Product</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentInventoryLogs as $log)
                                        <tr>
                                            <td>
                                                @if($log->type === 'in')
                                                    <span class="badge bg-success">IN</span>
                                                @elseif($log->type === 'out')
                                                    <span class="badge bg-danger">OUT</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ strtoupper($log->type) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->quantity }}</td>
                                            <td>
                                                <div>{{ $log->product_name }}</div>
                                                <small class="text-muted">{{ $log->warehouse_name }}</small>
                                            </td>
                                            <td><small>{{ \Carbon\Carbon::parse($log->created_at)->format('d M, H:i') }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No inventory movement found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
