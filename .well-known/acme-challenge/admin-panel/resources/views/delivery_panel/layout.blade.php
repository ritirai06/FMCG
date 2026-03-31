<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $companyName ?? 'SalePanel' }} - Delivery Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { background: #f5f7fb; }
        .sidebar { width: 250px; min-height: 100vh; background: #0f1f38; color: #fff; position: fixed; }
        .main { margin-left: 250px; min-height: 100vh; }
        .nav-link { color: #c9d6ea; border-radius: 8px; margin-bottom: 4px; }
        .nav-link:hover, .nav-link.active { background: #1a335b; color: #fff; }
        .brand { font-weight: 700; font-size: 1.1rem; }
        @media (max-width: 992px) {
            .sidebar { position: static; width: 100%; min-height: auto; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="sidebar p-3">
    <div class="brand mb-3"><i class="fa-solid fa-truck-fast me-2"></i>Delivery Panel</div>
    <div class="small text-light-emphasis mb-3">{{ $companyName ?? 'SalePanel' }}</div>
    <div class="mb-3 small">{{ $user?->name }}</div>
    <a class="nav-link {{ request()->routeIs('delivery.panel.dashboard') ? 'active' : '' }}" href="{{ route('delivery.panel.dashboard') }}"><i class="fa-solid fa-chart-line me-2"></i>Dashboard</a>
    <a class="nav-link {{ request()->routeIs('delivery.panel.orders') ? 'active' : '' }}" href="{{ route('delivery.panel.orders') }}"><i class="fa-solid fa-box me-2"></i>Orders</a>
    <a class="nav-link {{ request()->routeIs('delivery.panel.stores') ? 'active' : '' }}" href="{{ route('delivery.panel.stores') }}"><i class="fa-solid fa-store me-2"></i>Stores</a>
    <a class="nav-link {{ request()->routeIs('delivery.panel.attendance') ? 'active' : '' }}" href="{{ route('delivery.panel.attendance') }}"><i class="fa-solid fa-calendar-check me-2"></i>Attendance</a>
    <a class="nav-link {{ request()->routeIs('delivery.panel.profile') ? 'active' : '' }}" href="{{ route('delivery.panel.profile') }}"><i class="fa-solid fa-user me-2"></i>Profile</a>

    <form method="POST" action="{{ route('delivery.panel.logout') }}" class="mt-3">
        @csrf
        <button class="btn btn-outline-light btn-sm w-100" type="submit">Logout</button>
    </form>
</div>

<div class="main">
    <nav class="navbar navbar-expand-lg bg-white border-bottom px-3">
        <span class="navbar-brand mb-0 h5">@yield('page_title', 'Delivery Panel')</span>
    </nav>
    <div class="p-3">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
