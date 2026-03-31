<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $companyName ?? 'FMCG' }} Delivery Panel - Profile</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="{{ asset('deliver_assets/images/favicon.png') }}">
    <link href="{{ asset('deliver_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('deliver_assets/css/style.css') }}" rel="stylesheet">

    <style>
        html, body, #main-wrapper {
            overflow-x: hidden;
        }

        .content-body {
            overflow-x: hidden;
        }

        .content-body .container-fluid {
            max-width: 100%;
        }

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

        .profile-stat {
            border: 1px solid #eef1f7;
            border-radius: 10px;
            padding: 14px;
            height: auto;
            min-height: 84px;
        }

        .profile-main-card,
        .profile-stats-card {
            height: 100%;
        }

        .profile-main-card .card-body {
            min-height: 320px;
        }

        .floating-support,
        .style-switcher,
        .switch-demo,
        a[href*="w3itexperts"],
        a[href*="envato.market"] {
            display: none !important;
        }
    </style>
</head>
<body>
@php
    $brandName = $companyName ?? 'FMCG';
    $panelLabel = 'Delivery Panel';
    $activeUserName = $deliveryProfile?->name ?? $profileUser?->name ?? $user?->name ?? 'Delivery Partner';
    $zonesInput = collect($zones ?? [])->filter()->implode(', ');

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
            ?? $profileUser?->avatar
            ?? $user?->avatar
            ?? null,
        $logoPath
    );
@endphp

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
                                    <span class="input-group-text"><button type="submit" class="btn"><i class="flaticon-381-search-2"></i></button></span>
                                </div>
                            </form>
                            <div class="plus-icon"><a href="javascript:void(0);"><i class="fas fa-plus"></i></a></div>
                        </div>
                    </div>
                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge light text-white bg-primary rounded-circle">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end"><div class="p-3 text-muted">No new notifications.</div></div>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                                <span class="badge light text-white bg-primary rounded-circle">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end"><div class="p-3 text-muted">No new settings activity.</div></div>
                        </li>
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <img src="{{ $profileImage }}" width="20" alt="Profile">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon"><span class="ms-2">My Profile</span></a>
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
                    <a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon"><span class="ms-2">Profile</span></a>
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-center mb-4">
                <h3 class="mb-0 me-auto">Profile</h3>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card profile-main-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-1">Name</label>
                                    <h5 class="mb-0">{{ $deliveryProfile?->name ?? $profileUser?->name ?? 'N/A' }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-1">Phone</label>
                                    <h5 class="mb-0">{{ $phone ?? 'N/A' }}</h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted mb-1">Assigned Zones</label>
                                    <div>
                                        @if(($zones ?? collect())->count())
                                            @foreach($zones as $zone)
                                                <span class="badge badge-info light me-2 mb-2">{{ $zone }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No assigned zones.</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">Logout</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card profile-stats-card">
                        <div class="card-header"><h4 class="card-title mb-0">Delivery Stats</h4></div>
                        <div class="card-body">
                            <div class="profile-stat mb-2">
                                <small class="text-muted d-block">Total Assigned</small>
                                <h4 class="mb-0">{{ (int) data_get($profileStats, 'total_assigned', 0) }}</h4>
                            </div>
                            <div class="profile-stat mb-2">
                                <small class="text-muted d-block">Delivered</small>
                                <h4 class="mb-0">{{ (int) data_get($profileStats, 'delivered', 0) }}</h4>
                            </div>
                            <div class="profile-stat mb-2">
                                <small class="text-muted d-block">Out For Delivery</small>
                                <h4 class="mb-0">{{ (int) data_get($profileStats, 'out_for_delivery', 0) }}</h4>
                            </div>
                            <div class="profile-stat">
                                <small class="text-muted d-block">Revenue Handled</small>
                                <h4 class="mb-0">&#8377;{{ number_format((float) data_get($profileStats, 'revenue_handled', 0), 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('delivery.panel.profile.update') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $deliveryProfile?->name ?? $profileUser?->name ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" maxlength="10" value="{{ old('phone', $phone ?? '') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Assigned Zones (comma separated)</label>
                            <input type="text" name="zones" class="form-control" value="{{ old('zones', $zonesInput) }}">
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

    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('delivery.panel.profile.change_password') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Password</button>
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
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('delivery.panel.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
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
<script src="{{ asset('deliver_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('deliver_assets/js/dlabnav-init.js') }}"></script>
</body>
</html>
