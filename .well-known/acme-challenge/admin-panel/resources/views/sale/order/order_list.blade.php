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
            <div class="d-flex align-items-center mb-4 flex-wrap">
                <h3 class="mb-0 me-auto">Order Management</h3>
                <a href="{{ route('sale.order.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Order
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row order-list-layout">
                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Orders</h5></div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('sale.order.list') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(($stores ?? collect())->count() > 0)
                                <div class="col-md-3">
                                    <label for="store" class="form-label">Store</label>
                                    <select class="form-select" id="store" name="store">
                                        <option value="all" {{ ($filters['store'] ?? 'all') === 'all' ? 'selected' : '' }}>All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ ($filters['store'] ?? '') == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                                </div>
                                <div class="col-md-2 filter-action-wrap">
                                    <button type="submit" class="btn btn-primary filter-action-btn"><i class="fas fa-search me-2"></i>Filter</button>
                                    <a href="{{ route('sale.order.list') }}" class="btn reset-action-btn"><i class="fas fa-redo me-2"></i>Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title"><i class="fas fa-list me-2"></i>Orders <span class="badge bg-primary ms-2">{{ $orders->total() }}</span></h5></div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>No</th><th>Order #</th><th>Store</th><th>Customer</th><th>Amount</th><th>Status</th><th>Created By</th><th>Assigned To</th><th>Date</th><th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $statusLower = strtolower($order->status ?? 'Pending');
                                        $badgeClass = match (true) {
                                            str_contains($statusLower, 'deliver') => 'success',
                                            str_contains($statusLower, 'cancel') => 'danger',
                                            str_contains($statusLower, 'approve') => 'info',
                                            str_contains($statusLower, 'pack') => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                        <td><span class="fw-bold text-primary">{{ $order->order_number ?? ('ORD-' . $order->id) }}</span></td>
                                        <td><small class="text-muted">{{ $order->store?->store_name ?? 'N/A' }}</small></td>
                                        <td><small class="d-block">{{ $order->customer_name ?? $order->customer ?? 'N/A' }}</small><small class="text-muted">{{ $order->customer_phone ?? 'N/A' }}</small></td>
                                        <td><span class="fw-bold">&#8377;{{ number_format($order->total_amount ?? $order->amount ?? 0, 2) }}</span></td>
                                        <td><span class="badge bg-{{ $badgeClass }} light">{{ $order->status ?? 'Pending' }}</span></td>
                                        <td><small>{{ $order->createdBy?->name ?? 'N/A' }}</small></td>
                                        <td>@if($order->assignedDelivery)<span class="badge bg-light text-dark">{{ $order->assignedDelivery->name }}</span>@else<span class="badge bg-light text-muted">Unassigned</span>@endif</td>
                                        <td><small>{{ $order->created_at?->format('d-m-Y') ?? 'N/A' }}</small></td>
                                        <td>
                                            <div class="action-buttons d-flex justify-content-center gap-2" style="flex-wrap:wrap;">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-info js-view-order"
                                                    data-order-modal="1"
                                                    title="View Details"
                                                    data-order-number="{{ e($order->order_number ?? ('ORD-' . $order->id)) }}"
                                                    data-store="{{ e($order->store?->store_name ?? 'N/A') }}"
                                                    data-customer="{{ e($order->customer_name ?? $order->customer ?? 'N/A') }}"
                                                    data-phone="{{ e($order->customer_phone ?? 'N/A') }}"
                                                    data-amount="{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}"
                                                    data-status="{{ e($order->status ?? 'Pending') }}"
                                                    data-created-by="{{ e($order->createdBy?->name ?? 'N/A') }}"
                                                    data-assigned-to="{{ e($order->assignedDelivery?->name ?? 'Unassigned') }}"
                                                    data-date="{{ e($order->created_at?->format('d-m-Y') ?? 'N/A') }}"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center text-muted py-4"><i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><p>No orders found matching your filters.</p></td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($orders->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3 order-pagination-bar">
                        <div class="text-muted small">
                            Showing {{ ($orders->currentPage() - 1) * $orders->perPage() + 1 }}
                            to {{ min($orders->currentPage() * $orders->perPage(), $orders->total()) }}
                            of {{ $orders->total() }} orders
                        </div>
                        <nav>{{ $orders->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}</nav>
                    </div>
                    @endif
                </div>

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Order List Insights</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="mb-1">Own orders only</h6>
                                <p class="mb-0 text-muted">
                                    @if(auth()->check() && auth()->user()->isSales())
                                        Enabled for your account. Total own orders in current list: <strong>{{ $personOrderCount ?? 0 }}</strong>
                                    @else
                                        Role-based visibility active for this account.
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Filter by date</h6>
                                <p class="mb-0 text-muted">
                                    From: <strong>{{ $filters['date_from'] ?: 'Not set' }}</strong>,
                                    To: <strong>{{ $filters['date_to'] ?: 'Not set' }}</strong>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Status show</h6>
                                <p class="mb-0 text-muted">
                                    Selected status: <strong>{{ ucfirst($filters['status'] ?? 'all') }}</strong><br>
                                    Pending: <strong>{{ $personPending ?? 0 }}</strong>,
                                    Delivered: <strong>{{ $personDelivered ?? 0 }}</strong>,
                                    Cancelled: <strong>{{ $personCancelled ?? 0 }}</strong>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Revenue in current list</h6>
                                <p class="mb-0 text-muted">
                                    Total value: <strong>&#8377;{{ number_format((float)($personRevenue ?? 0), 2) }}</strong>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">Latest order (real-time)</h6>
                                @php $latestOrder = collect($personRecentOrders ?? [])->first(); @endphp
                                @if($latestOrder)
                                    <p class="mb-0 text-muted">
                                        <strong>{{ $latestOrder->order_number ?? ('ORD-' . $latestOrder->id) }}</strong>
                                        - {{ $latestOrder->store?->store_name ?? 'N/A' }}
                                        - {{ $latestOrder->status ?? 'N/A' }}<br>
                                        {{ $latestOrder->created_at?->format('d-m-Y h:i A') ?? 'N/A' }}
                                    </p>
                                @else
                                    <p class="mb-0 text-muted">No order found in current filter.</p>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1">Click to view details</h6>
                                <p class="mb-0 text-muted">Use <i class="fas fa-eye text-info"></i> icon in table actions to open order details popup.</p>
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

<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Order #:</strong> <span id="modalOrderNumber">-</span></div>
                <div class="mb-2"><strong>Store:</strong> <span id="modalStore">-</span></div>
                <div class="mb-2"><strong>Customer:</strong> <span id="modalCustomer">-</span></div>
                <div class="mb-2"><strong>Phone:</strong> <span id="modalPhone">-</span></div>
                <div class="mb-2"><strong>Amount:</strong> ₹<span id="modalAmount">0.00</span></div>
                <div class="mb-2"><strong>Status:</strong> <span id="modalStatus">-</span></div>
                <div class="mb-2"><strong>Created By:</strong> <span id="modalCreatedBy">-</span></div>
                <div class="mb-2"><strong>Assigned To:</strong> <span id="modalAssignedTo">-</span></div>
                <div class="mb-0"><strong>Date:</strong> <span id="modalDate">-</span></div>
            </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderDetailsModalEl = document.getElementById('orderDetailsModal');
        const orderDetailsModal = orderDetailsModalEl ? new bootstrap.Modal(orderDetailsModalEl) : null;

        const fillOrderModal = function (el) {
            document.getElementById('modalOrderNumber').textContent = el.dataset.orderNumber || '-';
            document.getElementById('modalStore').textContent = el.dataset.store || '-';
            document.getElementById('modalCustomer').textContent = el.dataset.customer || '-';
            document.getElementById('modalPhone').textContent = el.dataset.phone || '-';
            document.getElementById('modalAmount').textContent = el.dataset.amount || '0.00';
            document.getElementById('modalStatus').textContent = el.dataset.status || '-';
            document.getElementById('modalCreatedBy').textContent = el.dataset.createdBy || '-';
            document.getElementById('modalAssignedTo').textContent = el.dataset.assignedTo || '-';
            document.getElementById('modalDate').textContent = el.dataset.date || '-';
            orderDetailsModal?.show();
        };

        document.addEventListener('click', function (event) {
            const explicitTrigger = event.target.closest('.js-view-order, [data-order-modal="1"]');
            const eyeTrigger = event.target.closest('.action-buttons a .fa-eye, .action-buttons button .fa-eye');
            const trigger = explicitTrigger || (eyeTrigger ? eyeTrigger.closest('a,button') : null);
            if (!trigger) return;
            event.preventDefault();
            event.stopPropagation();
            if (!trigger.dataset.orderNumber) {
                const row = trigger.closest('tr');
                if (row) {
                    const cells = row.querySelectorAll('td');
                    trigger.dataset.orderNumber = cells[1]?.innerText?.trim() || '-';
                    trigger.dataset.store = cells[2]?.innerText?.trim() || '-';
                    trigger.dataset.customer = cells[3]?.innerText?.split('\n')[0]?.trim() || '-';
                    trigger.dataset.phone = cells[3]?.innerText?.split('\n')[1]?.trim() || '-';
                    trigger.dataset.amount = (cells[4]?.innerText || '0').replace(/[^\d.]/g, '') || '0.00';
                    trigger.dataset.status = cells[5]?.innerText?.trim() || '-';
                    trigger.dataset.createdBy = cells[6]?.innerText?.trim() || '-';
                    trigger.dataset.assignedTo = cells[7]?.innerText?.trim() || '-';
                    trigger.dataset.date = cells[8]?.innerText?.trim() || '-';
                }
            }
            fillOrderModal(trigger);
        });

    });
</script>
</body>
</html>

