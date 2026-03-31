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
                                <a href="{{ route('sale.logout') }}" class="dropdown-item ai-icon js-logout-trigger"><span class="ms-2">Logout</span></a>
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
                $assignedTerritories = collect([
                    $salesRegion ?? null,
                    $salesPerson?->territory ?? null,
                    $salesPerson?->assigned_area ?? null,
                    $salesPerson?->assigned_locality ?? null,
                ])->filter(fn ($v) => trim((string) $v) !== '')->unique()->values();

                $profileTarget = $salesPerson?->target ?? $salesPerson?->monthly_target ?? 0;
                $personPhone = $salesPerson?->phone ?? $user?->phone ?? $user?->mobile ?? 'N/A';
            @endphp

            <div class="d-flex align-items-center mb-4">
                <h3 class="mb-0">Profile</h3>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row order-list-layout">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-4">
                                        <img src="{{ $salesProfileImage }}" alt="{{ $salesPersonName }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;" class="me-3">
                                        <div>
                                            <h4 class="mb-1">{{ $salesPersonName }}</h4>
                                            <p class="text-muted mb-0">{{ $salesPersonStatus }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Menu Name</small>
                                    <h6 class="mb-0">Profile</h6>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <h6 class="mb-0">{{ $personPhone }}</h6>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Assigned territories</small>
                                    <h6 class="mb-0">{{ $assignedTerritories->isNotEmpty() ? $assignedTerritories->implode(', ') : 'No assigned territories' }}</h6>
                                </div>
                            </div>

                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                                <a href="{{ route('sale.logout') }}" class="btn btn-danger js-logout-trigger">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card store-insights-card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Profile Stats</h4>
                        </div>
                        <div class="card-body">
                            <div class="store-insights-metric mb-3">
                                <div class="metric-label">Total Orders</div>
                                <div class="metric-value">{{ (int) ($profileStats['total_orders'] ?? 0) }}</div>
                            </div>
                            <div class="store-insights-metric mb-3">
                                <div class="metric-label">Delivered</div>
                                <div class="metric-value">{{ (int) ($profileStats['delivered_orders'] ?? 0) }}</div>
                            </div>
                            <div class="store-insights-metric mb-3">
                                <div class="metric-label">Pending</div>
                                <div class="metric-value">{{ (int) ($profileStats['pending_orders'] ?? 0) }}</div>
                            </div>
                            <div class="store-insights-metric mb-0">
                                <div class="metric-label">Target</div>
                                <div class="metric-value">INR {{ number_format((float) $profileTarget, 2) }}</div>
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

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('sale.profile.update') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $salesPersonName) }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $personPhone === 'N/A' ? '' : $personPhone) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Kya aap logout karna chahte hain?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                <a href="{{ route('sale.logout') }}" id="confirmLogoutBtn" class="btn btn-danger">Yes, Logout</a>
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
        const logoutModalEl = document.getElementById('logoutConfirmModal');
        if (!logoutModalEl || typeof bootstrap === 'undefined') return;
        const logoutModal = new bootstrap.Modal(logoutModalEl);
        const confirmBtn = document.getElementById('confirmLogoutBtn');

        document.querySelectorAll('.js-logout-trigger').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const targetHref = el.getAttribute('href') || '{{ route('sale.logout') }}';
                if (confirmBtn) confirmBtn.setAttribute('href', targetHref);
                logoutModal.show();
            });
        });
    });
</script>
</body>
</html>
