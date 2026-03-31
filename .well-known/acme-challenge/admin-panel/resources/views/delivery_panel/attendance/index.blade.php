<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $companyName ?? 'FMCG' }} Delivery Panel - Attendance</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="{{ asset('deliver_assets/images/favicon.png') }}">
    <link href="{{ asset('deliver_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('deliver_assets/css/style.css') }}" rel="stylesheet">

    <style>
        .brand-logo-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-logo-custom img {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
        }

        .brand-logo-custom .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: #2d3138;
            line-height: 1;
        }

        .brand-logo-custom .brand-sub {
            font-size: 12px;
            color: #787878;
            line-height: 1.2;
        }

        .sidebar-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
        }

        .floating-support,
        .style-switcher,
        .switch-demo,
        a[href*="w3itexperts"],
        a[href*="envato.market"] {
            display: none !important;
        }
        .btn-primary,
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary:focus-visible {
            background-color: #f73a0b !important;
            border-color: #f73a0b !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
@php
    $month = (int) ($month ?? now()->month);
    $year = (int) ($year ?? now()->year);
    $todayStatus = $todayStatus ?? 'Absent';
    $todayCompletedDeliveries = (int) ($todayCompletedDeliveries ?? 0);
    $summary = $summary ?? ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0, 'completed_deliveries' => 0];
    $calendarDays = $calendarDays ?? collect();
    $records = $records ?? collect();
    $todayAttendance = $todayAttendance ?? null;

    $brandName = 'FMCG';
    $panelLabel = 'Delivery Panel';
    $activeUserName = $deliveryProfile?->name ?? $user?->name ?? 'Delivery Partner';

    $resolveImage = function ($path, $fallback) {
        $path = trim((string) $path);
        if ($path === '') {
            return $fallback;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }
        $path = ltrim($path, '/');
        $candidates = [
            $path,
            'storage/' . $path,
            'uploads/' . $path,
            'uploads/admin/' . $path,
            'uploads/company/' . $path,
            'uploads/delivery/' . $path,
        ];
        foreach ($candidates as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }
        return $fallback;
    };

    $logoPath = $resolveImage(
        $companySettings?->company_logo
            ?? $companySettings?->logo
            ?? $companySettings?->profile_image
            ?? null,
        asset('deliver_assets/images/logo-full.png')
    );

    $profileImage = $resolveImage(
        $deliveryProfile?->avatar_path
            ?? $deliveryProfile?->profile_image
            ?? $user?->avatar
            ?? null,
        $logoPath
    );
@endphp

<div id="preloader">
    <div class="lds-ripple"><div></div><div></div></div>
</div>

<div id="main-wrapper">
    <div class="nav-header">
        <a href="{{ route('delivery.panel.dashboard') }}" class="brand-logo brand-logo-custom">
            <img src="{{ $logoPath }}" alt="Company Logo">
            <div>
                <div class="brand-name">{{ $brandName }}</div>
                <div class="brand-sub">{{ $panelLabel }}</div>
            </div>
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
                        <div class="dashboard_bar">{{ $panelLabel }}</div>
                        <div class="nav-item d-flex align-items-center">
                            <form action="javascript:void(0);">
                                <div class="input-group search-area">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <span class="input-group-text">
                                        <button type="submit" class="btn">
                                            <i class="flaticon-381-search-2"></i>
                                        </button>
                                    </span>
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
                                <i class="fas fa-bell"></i>
                                <span class="badge light text-white bg-primary rounded-circle">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="p-3 text-muted">No new notifications.</div>
                            </div>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                                <span class="badge light text-white bg-primary rounded-circle">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="p-3 text-muted">No new settings activity.</div>
                            </div>
                        </li>
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <img src="{{ $profileImage }}" width="20" alt="Profile">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon">
                                    <span class="ms-2">My Profile</span>
                                </a>
                                <form method="POST" action="{{ route('delivery.panel.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item ai-icon text-danger">Logout</button>
                                </form>
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
                        <img class="sidebar-user-avatar" src="{{ $profileImage }}" alt="User">
                        <div class="d-flex align-items-center sidebar-info">
                            <div>
                                <span class="font-w400 d-block">{{ $activeUserName }}</span>
                                <small class="text-end font-w400">Delivery Partner</small>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon">
                        <span class="ms-2">Profile</span>
                    </a>
                    <form method="POST" action="{{ route('delivery.panel.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item ai-icon text-danger">Logout</button>
                    </form>
                </div>
            </div>

            <ul class="metismenu" id="menu">
                <li><a class="has-arrow" href="{{ route('delivery.panel.dashboard') }}"><i class="flaticon-025-dashboard"></i><span class="nav-text">Dashboard</span></a></li>
                <li><a class="has-arrow" href="{{ route('delivery.panel.my.orders') }}"><i class="flaticon-381-user-7"></i><span class="nav-text">Orders</span></a></li>
                <li><a class="has-arrow" href="{{ route('delivery.panel.order.details') }}"><i class="flaticon-381-notepad"></i><span class="nav-text">Order Details</span></a></li>
                <li><a class="has-arrow" href="{{ route('delivery.panel.attendance') }}"><i class="flaticon-381-user-4"></i><span class="nav-text">Attendance</span></a></li>
                <li><a class="has-arrow" href="{{ route('delivery.panel.profile') }}"><i class="flaticon-381-internet"></i><span class="nav-text">Profile</span></a></li>
            </ul>

            <div class="plus-box">
                <p class="fs-14 font-w600 mb-2">Let {{ $brandName }} simplify<br>your delivery workflow</p>
                <p class="plus-box-p">Manage deliveries, orders, and reports in one place</p>
            </div>
            <div class="copyright">
                <p><strong>{{ $brandName }}</strong> - {{ $panelLabel }} &copy; <span class="current-year">2026</span></p>
                <p class="fs-12">Manage deliveries, orders, and reports in one place</p>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            <div class="d-flex align-items-center mb-4">
                <h3 class="mb-0 me-auto">Attendance</h3>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-2">Today Status</h5>
                            @php $todayClass = strtolower($todayStatus) === 'present' ? 'success' : (strtolower($todayStatus) === 'late' ? 'warning' : (strtolower($todayStatus) === 'leave' ? 'info' : 'danger')); @endphp
                            <h2 class="text-{{ $todayClass }} mb-2">{{ $todayStatus }}</h2>
                            <p class="mb-0 text-muted">Completed Deliveries Today: {{ $todayCompletedDeliveries }}</p>
                            <p class="mb-0 text-muted">Auto-calculated from completed deliveries</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title mb-0">Attendance Summary</h4></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-3"><h6 class="text-muted mb-1">Present</h6><h4 class="mb-0">{{ $summary['present'] ?? 0 }}</h4></div>
                                <div class="col-md-3 col-6 mb-3"><h6 class="text-muted mb-1">Absent</h6><h4 class="mb-0">{{ $summary['absent'] ?? 0 }}</h4></div>
                                <div class="col-md-3 col-6 mb-3"><h6 class="text-muted mb-1">Late</h6><h4 class="mb-0">{{ $summary['late'] ?? 0 }}</h4></div>
                                <div class="col-md-3 col-6 mb-3"><h6 class="text-muted mb-1">Completed Deliveries</h6><h4 class="mb-0">{{ $summary['completed_deliveries'] ?? 0 }}</h4></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Mark Today Attendance</h4></div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('delivery.panel.attendance.mark') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    @php $selectedStatus = old('status', $todayAttendance->status ?? $todayStatus); @endphp
                                    <option value="Present" {{ $selectedStatus === 'Present' ? 'selected' : '' }}>Present</option>
                                    <option value="Late" {{ $selectedStatus === 'Late' ? 'selected' : '' }}>Late</option>
                                    <option value="Leave" {{ $selectedStatus === 'Leave' ? 'selected' : '' }}>Leave</option>
                                    <option value="Absent" {{ $selectedStatus === 'Absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional note" value="{{ old('notes', $todayAttendance->notes ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end gap-2">
                                <button type="submit" name="action_type" value="check_in" class="btn btn-primary w-100">Check In</button>
                                <button type="submit" name="action_type" value="check_out" class="btn btn-primary w-100">Check Out</button>
                            </div>
                        </div>
                        <div class="text-muted small">
                            Today In: {{ $todayAttendance?->time_in ?? 'N/A' }} | Today Out: {{ $todayAttendance?->time_out ?? 'N/A' }}
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('delivery.panel.attendance') }}" class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-control">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create($year, $m, 1)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-control">
                                @for($y = now()->year; $y >= now()->year - 4; $y--)
                                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Monthly Attendance Calendar</h4></div>
                <div class="card-body">
                    <div class="row">
                        @forelse($calendarDays as $day)
                            @php
                                $status = $day['status'] ?? 'Absent';
                                $badgeClass = match (strtolower($status)) {
                                    'present' => 'success',
                                    'late' => 'warning',
                                    'leave' => 'info',
                                    default => 'danger',
                                };
                            @endphp
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>{{ $day['date']->format('d') }}</strong>
                                        <span class="badge badge-{{ $badgeClass }} light">{{ $status }}</span>
                                    </div>
                                    <small class="d-block text-muted">{{ $day['date']->format('D') }}</small>
                                    <small class="text-muted">Deliveries: {{ $day['deliveries_count'] ?? 0 }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">No calendar data found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Attendance Records</h4></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr><th>Date</th><th>Status</th><th>Completed Deliveries</th><th>Notes</th></tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    @php
                                        $recordStatus = $record->status ?? 'Absent';
                                        $recordClass = match (strtolower($recordStatus)) {
                                            'present' => 'success',
                                            'late' => 'warning',
                                            'leave' => 'info',
                                            default => 'danger',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td><span class="badge badge-{{ $recordClass }} light">{{ $recordStatus }}</span></td>
                                        <td>{{ $record->completed_orders ?? 0 }}</td>
                                        <td>{{ $record->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No attendance records available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(is_object($records) && method_exists($records, 'links'))
                    <div class="card-footer">{{ $records->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; {{ $brandName }} - {{ $panelLabel }} <span class="current-year">2026</span></p>
        </div>
    </div>
</div>

<script src="{{ asset('deliver_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('deliver_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('deliver_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('deliver_assets/js/dlabnav-init.js') }}"></script>
</body>
</html>
