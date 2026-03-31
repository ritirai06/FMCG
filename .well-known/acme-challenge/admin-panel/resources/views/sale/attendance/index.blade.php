<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jobick: Job Admin Dashboard Bootstrap 5 Template + FrontEnd</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
    <link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        a[href*="support.w3itexperts"],
        a[href*="envato.market"],
        .sidebar-right,
        .sidebar-right-trigger,
        .sidebar-close-trigger,
        .dlab-demo-panel,
        .dlab-demo-trigger,
        #DZ_THEME_PANEL,
        #DZScript {
            display: none !important;
            visibility: hidden !important;
        }

        /* Keep action buttons stable on hover in create-order page */
        .order-btn-stable.btn-primary:hover,
        .order-btn-stable.btn-primary:focus {
            color: #fff !important;
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .order-btn-stable.btn-danger:hover,
        .order-btn-stable.btn-danger:focus {
            color: #fff !important;
            background-color: #f72b50 !important;
            border-color: #f72b50 !important;
        }

        .order-btn-stable.btn-danger.light:hover,
        .order-btn-stable.btn-danger.light:focus {
            color: #f72b50 !important;
            background-color: #ffecef !important;
            border-color: #f72b50 !important;
        }

        /* Order list layout: prevent right column stretch */
        .order-list-layout {
            align-items: flex-start;
        }

        .order-list-layout > [class*="col-"] {
            align-self: flex-start;
        }

        .order-list-layout .col-xl-4 .card {
            height: auto !important;
            min-height: 0 !important;
        }

        /* Filter controls polish */
        .filter-action-btn,
        .reset-action-btn {
            height: 46px;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            min-width: 95px;
        }

        .filter-action-btn {
            box-shadow: 0 8px 18px rgba(247, 58, 11, 0.22);
            color: #fff !important;
            background-color: #f73a0b !important;
            border-color: #f73a0b !important;
        }

        .filter-action-btn:hover,
        .filter-action-btn:focus,
        .filter-action-btn:active {
            color: #fff !important;
            background-color: #e73508 !important;
            border-color: #e73508 !important;
        }

        .reset-action-btn {
            border: 1px solid #d9e2ec;
            background: #f7f9fc;
            color: #2b3674;
        }

        .reset-action-btn:hover,
        .reset-action-btn:focus {
            background: #eef3f8;
            color: #1f2f57;
            border-color: #c9d5e3;
        }

        .filter-action-wrap {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-action-wrap .filter-action-btn,
        .filter-action-wrap .reset-action-btn {
            flex: 1 1 0;
            min-width: 0;
            white-space: nowrap;
        }

        /* Keep button colors stable on hover/focus (no white flash) */
        .sale-order-page .btn-primary,
        .sale-order-page .btn-primary:hover,
        .sale-order-page .btn-primary:focus,
        .sale-order-page .btn-primary:active {
            color: #fff !important;
            background-color: #f73a0b !important;
            border-color: #f73a0b !important;
        }

        .sale-order-page .btn-info,
        .sale-order-page .btn-info:hover,
        .sale-order-page .btn-info:focus,
        .sale-order-page .btn-info:active {
            color: #fff !important;
        }

        .sale-order-page .btn-warning,
        .sale-order-page .btn-warning:hover,
        .sale-order-page .btn-warning:focus,
        .sale-order-page .btn-warning:active {
            color: #1f2f57 !important;
        }

        .sale-order-page .btn-danger,
        .sale-order-page .btn-danger:hover,
        .sale-order-page .btn-danger:focus,
        .sale-order-page .btn-danger:active,
        .sale-order-page .btn-success,
        .sale-order-page .btn-success:hover,
        .sale-order-page .btn-success:focus,
        .sale-order-page .btn-success:active {
            color: #fff !important;
        }

        .sale-order-page .btn-light,
        .sale-order-page .btn-light:hover,
        .sale-order-page .btn-light:focus,
        .sale-order-page .btn-light:active {
            color: #2b3674 !important;
            background-color: #f7f9fc !important;
            border-color: #d9e2ec !important;
        }

        .sale-order-page .order-pagination-bar {
            flex-wrap: wrap;
            gap: 10px;
        }

        .sale-order-page .order-pagination-bar nav {
            margin-left: auto;
        }

        .sale-order-page .order-pagination-bar .pagination {
            margin-bottom: 0;
        }

        .store-insights-card {
            border: 1px solid #edf2f9;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .store-insights-metric {
            border: 1px solid #edf2f9;
            border-radius: 10px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 10px 12px;
        }

        .store-insights-metric .metric-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .store-insights-metric .metric-value {
            font-size: 22px;
            line-height: 1;
            font-weight: 700;
            color: #1f2f57;
        }

        .store-insights-table th {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            border-top: 0;
        }

        .store-insights-table td {
            font-size: 12px;
            color: #1f2f57;
            vertical-align: middle;
        }

        .store-insights-subcard {
            border: 1px solid #edf2f9;
            border-radius: 10px;
            background: #fff;
            padding: 12px;
            height: 100%;
        }
    </style>
</head>
<body>
@php
    $resolveImage = function ($path, $fallback) {
        $path = ltrim((string) $path, '/');
        if ($path === '') {
            return $fallback;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        if (file_exists(public_path('uploads/admin/' . $path))) {
            return asset('uploads/admin/' . $path);
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        return asset($path);
    };

    $companyDisplayName = $companyName ?? 'SalePanel';
    $companyLogo = $resolveImage(
        $companySettings?->company_logo ?? $companySettings?->logo ?? $companySettings?->profile_image,
        asset('sale_assets/images/logo-full.png')
    );

    $salesPersonName = collect([
        $user?->name ?? null,
        $user?->full_name ?? null,
        $user?->username ?? null,
    ])->map(fn ($v) => trim((string) $v))->first(fn ($v) => $v !== '');
    if (!$salesPersonName) {
        $salesPersonName = trim((string) ($salesPerson?->name ?? ''));
    }
    if (!$salesPersonName && !empty($user?->email)) {
        $salesPersonName = trim((string) strstr($user->email, '@', true));
    }
    $salesPersonName = $salesPersonName ?: 'Sales User';
    $salesPersonStatus = $user?->status ?? $salesPerson?->status ?? 'Active';
    $salesProfileImage = $resolveImage(
        $user?->avatar_path ?? $user?->profile_image ?? $salesPerson?->avatar_path ?? $salesPerson?->profile_image ?? $companySettings?->profile_image,
        $companyLogo
    );

@endphp

<div id="main-wrapper" class="sale-order-page">
    <div class="nav-header">
        <a href="/sale/dashboard" class="brand-logo">
            <img class="logo-abbr" src="{{ $companyLogo }}" alt="{{ $companyDisplayName }}" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
            <span class="brand-title" style="font-size:14px;font-weight:600;color:#464646;margin-left:8px;">{{ $companyDisplayName }}</span>
        </a>
        <div class="nav-control">
            <div class="hamburger">
                <span class="line"></span><span class="line"></span><span class="line"></span>
            </div>
        </div>
    </div>

    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                        <div class="dashboard_bar">{{ $companyDisplayName }} Dashboard</div>
                        <div class="nav-item d-flex align-items-center">
                            <form action="javascript:void(0);">
                                <div class="input-group search-area">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <span class="input-group-text"><button type="submit" class="btn"><i class="flaticon-381-search-2"></i></button></span>
                                </div>
                            </form>
                            <div class="plus-icon">
                                <a href="javascript:void(0);"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"><g data-name="Layer 2" transform="translate(-2 -2)"><path d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8Z"></path></g></svg>
                                <span class="badge light text-white bg-primary rounded-circle">9</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class="fas fa-cog" style="font-size:20px;line-height:1;color:#2b3674;"></i>
                                <span class="badge light text-white bg-primary rounded-circle">5</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <img src="{{ $salesProfileImage }}" width="20" alt="{{ $salesPersonName }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="/sale/profile" class="dropdown-item ai-icon"><span class="ms-2">{{ $salesPersonName }}</span></a>
                                <a href="/sale/attendance" class="dropdown-item ai-icon"><span class="ms-2">{{ $salesPersonStatus }}</span></a>
                                <a href="/sale/logout" class="dropdown-item ai-icon" onclick="return confirm('Are you sure you want to logout?')"><span class="ms-2">Logout</span></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="dlabnav">
        <div class="dlabnav-scroll">
            <div class="dropdown header-profile2">
                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                    <div class="header-info2 d-flex align-items-center">
                        <img src="{{ $salesProfileImage }}" alt="">
                        <div class="d-flex align-items-center sidebar-info">
                            <div>
                                <span class="font-w400 d-block">{{ $salesPersonName }}</span>
                                <small class="text-end font-w400">{{ $salesPersonStatus }}</small>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </a>
            </div>
            <ul class="metismenu" id="menu">
                <li class="{{ request()->is('sale/dashboard') ? 'mm-active' : '' }}">
                    <a href="/sale/dashboard"><i class="flaticon-025-dashboard"></i><span class="nav-text">Dashboard</span></a>
                </li>
                <li class="{{ request()->is('sale/order/*') ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="{{ request()->is('sale/order/*') ? 'true' : 'false' }}"><i class="flaticon-381-user-7"></i><span class="nav-text">Orders</span></a>
                    <ul aria-expanded="{{ request()->is('sale/order/*') ? 'true' : 'false' }}" class="{{ request()->is('sale/order/*') ? 'mm-show' : '' }}" style="{{ request()->is('sale/order/*') ? 'display:block;' : '' }}">
                        <li><a href="/sale/order/create">Create Order</a></li>
                        <li><a href="/sale/order/list">Order List</a></li>
                    </ul>
                </li>
                <li class="{{ request()->is('sale/store/*') ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="{{ request()->is('sale/store/*') ? 'true' : 'false' }}"><i class="flaticon-093-waving"></i><span class="nav-text">Stores</span></a>
                    <ul aria-expanded="{{ request()->is('sale/store/*') ? 'true' : 'false' }}" class="{{ request()->is('sale/store/*') ? 'mm-show' : '' }}" style="{{ request()->is('sale/store/*') ? 'display:block;' : '' }}">
                        <li><a href="/sale/store/create">Create Store</a></li>
                        <li><a href="/sale/store/list">Store List</a></li>
                    </ul>
                </li>
                <li class="{{ request()->is('sale/attendance') ? 'mm-active' : '' }}"><a href="/sale/attendance"><i class="flaticon-381-user-4"></i><span class="nav-text">Attendance</span></a></li>
                <li class="{{ request()->is('sale/profile') ? 'mm-active' : '' }}"><a href="/sale/profile"><i class="flaticon-381-internet"></i><span class="nav-text">Profile</span></a></li>
            </ul>
            <div class="plus-box">
                <p class="fs-14 font-w600 mb-2">Let SalePanel simplify<br>your sales workflow</p>
                <p class="plus-box-p">Manage stores, orders, and reports in one place</p>
            </div>
            <div class="copyright">
                <p><strong>FMCG</strong> - Simplify your sales workflow &copy; <span class="current-year">2026</span></p>
                <p class="fs-12">Manage stores, orders, and reports in one place</p>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            @php
                $attSummary = $attendanceSummary ?? [
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'leave' => 0,
                    'attendance_rate' => 0,
                    'today_present' => 0,
                ];
                $monthNum = (int) ($selectedMonth ?? now()->month);
                $yearNum = (int) ($selectedYear ?? now()->year);
                $monthName = \Carbon\Carbon::create()->month($monthNum)->format('F');
                $monthStart = \Carbon\Carbon::create($yearNum, $monthNum, 1);
                $monthEnd = $monthStart->copy()->endOfMonth();
                $startWeekDay = (int) $monthStart->dayOfWeek;
                $daysInMonth = (int) $monthEnd->day;
                $calendarMap = collect($attendanceCalendarRecords ?? [])->mapWithKeys(function ($r) {
                    $d = optional($r->date)->format('Y-m-d');
                    return $d ? [$d => strtolower((string) ($r->status ?? ''))] : [];
                });
            @endphp

            <div class="d-flex align-items-center mb-4 flex-wrap">
                <h3 class="mb-0 me-auto">Attendance</h3>
                <form method="GET" action="{{ route('sale.attendance') }}" class="d-flex gap-2">
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected($monthNum === $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                    <select name="year" class="form-select">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected($yearNum === $y)>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </form>
            </div>

            <div class="row order-list-layout">
                <div class="col-xl-12">
                    <div class="card mb-3">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Mark Attendance</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('sale.attendance.mark') }}" class="row g-3">
                                @csrf
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}">
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        @php $currentStatus = old('status', $todayAttendanceRecord->status ?? 'Present'); @endphp
                                        <option value="Present" @selected($currentStatus === 'Present')>Present</option>
                                        <option value="Absent" @selected($currentStatus === 'Absent')>Absent</option>
                                        <option value="Late" @selected($currentStatus === 'Late')>Late</option>
                                        <option value="Leave" @selected($currentStatus === 'Leave')>Leave</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Action</label>
                                    <select name="action_type" class="form-select">
                                        <option value="">No Action</option>
                                        <option value="check_in">Check In</option>
                                        <option value="check_out">Check Out</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Stock & Inventory Context</label>
                                    <input type="text" class="form-control" value="Auto-calculated based on performance and stock movement." readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" rows="2" class="form-control" placeholder="Optional note">{{ old('notes', $todayAttendanceRecord->notes ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Present</p><h4 class="mb-0 text-success">{{ $attSummary['present'] }}</h4></div></div></div>
                        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Absent</p><h4 class="mb-0 text-danger">{{ $attSummary['absent'] }}</h4></div></div></div>
                        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Late</p><h4 class="mb-0 text-warning">{{ $attSummary['late'] }}</h4></div></div></div>
                        <div class="col-xl-3 col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Rate</p><h4 class="mb-0">{{ $attSummary['attendance_rate'] }}%</h4></div></div></div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Monthly Calendar - {{ $monthName }} {{ $yearNum }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $day = 1; @endphp
                                        @for($row = 0; $row < 6; $row++)
                                            <tr>
                                                @for($col = 0; $col < 7; $col++)
                                                    @if(($row === 0 && $col < $startWeekDay) || $day > $daysInMonth)
                                                        <td class="bg-light"></td>
                                                    @else
                                                        @php
                                                            $dateStr = \Carbon\Carbon::create($yearNum, $monthNum, $day)->format('Y-m-d');
                                                            $state = $calendarMap[$dateStr] ?? '';
                                                            $badgeClass = str_contains($state, 'present') ? 'success' : (str_contains($state, 'late') ? 'warning' : (str_contains($state, 'leave') ? 'info' : (str_contains($state, 'absent') ? 'danger' : 'secondary')));
                                                        @endphp
                                                        <td>
                                                            <div class="fw-semibold">{{ $day }}</div>
                                                            @if($state !== '')
                                                                <span class="badge bg-{{ $badgeClass }} mt-1">{{ ucfirst($state) }}</span>
                                                            @endif
                                                        </td>
                                                        @php $day++; @endphp
                                                    @endif
                                                @endfor
                                            </tr>
                                            @if($day > $daysInMonth) @break @endif
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Attendance Records</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendanceRecords as $record)
                                        @php
                                            $status = strtolower((string) ($record->status ?? ''));
                                            $cls = str_contains($status, 'present') ? 'success' : (str_contains($status, 'late') ? 'warning' : (str_contains($status, 'leave') ? 'info' : 'danger'));
                                        @endphp
                                        <tr>
                                            <td>{{ optional($record->date)->format('d M Y') }}</td>
                                            <td><span class="badge bg-{{ $cls }}">{{ $record->status ?? 'N/A' }}</span></td>
                                            <td>{{ $record->time_in ?: '-' }}</td>
                                            <td>{{ $record->time_out ?: '-' }}</td>
                                            <td>{{ $record->notes ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">No attendance records found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($attendanceRecords->count() > 0)
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small class="text-muted">Showing {{ ($attendanceRecords->currentPage() - 1) * $attendanceRecords->perPage() + 1 }} to {{ min($attendanceRecords->currentPage() * $attendanceRecords->perPage(), $attendanceRecords->total()) }} of {{ $attendanceRecords->total() }}</small>
                                {{ $attendanceRecords->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="card store-insights-card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Attendance</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="mb-1"><i class="fas fa-thumbtack me-1"></i>Menu Name: Attendance</h6>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Today present/absent</h6>
                                <p class="mb-0 text-muted">Present: <strong>{{ $attSummary['present'] ?? 0 }}</strong>, Absent: <strong>{{ $attSummary['absent'] ?? 0 }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Monthly calendar</h6>
                                <p class="mb-0 text-muted">{{ $monthName }} {{ $yearNum }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Auto calculated based on performance</h6>
                                <p class="mb-0 text-muted">Attendance rate: <strong>{{ $attSummary['attendance_rate'] ?? 0 }}%</strong></p>
                            </div>
                            <div>
                                <h6 class="mb-1">Summary stats</h6>
                                <p class="mb-0 text-muted">
                                    Today present: <strong>{{ $attSummary['today_present'] ?? 0 }}</strong><br>
                                    Late: <strong>{{ $attSummary['late'] ?? 0 }}</strong>, Leave: <strong>{{ $attSummary['leave'] ?? 0 }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; FMCG <span class="current-year">2026</span></p>
        </div>
    </div>
</div>

<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.js') }}"></script>
<script src="{{ asset('sale_assets/js/jquery.localizationTool.js') }}"></script>
<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/dlabnav-init.js') }}"></script>
</body>
</html>
