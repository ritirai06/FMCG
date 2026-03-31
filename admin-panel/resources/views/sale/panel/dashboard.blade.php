@extends('sale.panel.layout')
@section('title', 'Dashboard')

@section('content')
@php
    $name = trim((string)($user?->name ?? $salesPerson?->name ?? 'Sales User'));
    $greeting = now()->hour < 12 ? 'Good Morning' : (now()->hour < 17 ? 'Good Afternoon' : 'Good Evening');
@endphp

<!-- GREETING BANNER -->
<div style="background:var(--primary);border-radius:var(--radius);padding:24px 28px;color:#fff;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-20px;top:-20px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.06);"></div>
    <div style="font-size:13px;opacity:.8;margin-bottom:3px;">{{ $greeting }},</div>
    <div style="font-size:24px;font-weight:700;margin-bottom:4px;">{{ $name }} 👋</div>
    <div style="font-size:12px;opacity:.7;">{{ now()->format('l, d M Y') }}</div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
            <div>
                <div class="stat-val">₹{{ number_format($totalPayments, 0) }}</div>
                <div class="stat-lbl">Total Payments</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="stat-val">₹{{ number_format($totalSales, 0) }}</div>
                <div class="stat-lbl">Total Sales</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
            <div>
                <div class="stat-val">{{ $todayOrders }}</div>
                <div class="stat-lbl">Today Orders</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-val">{{ $totalCustomers }}</div>
                <div class="stat-lbl">Parties</div>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="sec-hdr">Quick Actions</div>
<div class="menu-grid">
    <a href="{{ route('sale.panel.parties') }}" class="menu-item">
        <div class="menu-icon" style="background:var(--primary);"><i class="bi bi-people-fill"></i></div>
        <span class="menu-label">Parties</span>
    </a>
    <a href="{{ route('sale.panel.items') }}" class="menu-item">
        <div class="menu-icon" style="background:#059669;"><i class="bi bi-box-seam-fill"></i></div>
        <span class="menu-label">Items</span>
    </a>
    <a href="{{ route('sale.panel.transactions') }}" class="menu-item">
        <div class="menu-icon" style="background:#0284C7;"><i class="bi bi-receipt-cutoff"></i></div>
        <span class="menu-label">Transactions</span>
    </a>
    <a href="{{ route('sale.panel.attendance') }}" class="menu-item">
        <div class="menu-icon" style="background:#D97706;"><i class="bi bi-calendar-check-fill"></i></div>
        <span class="menu-label">Attendance</span>
    </a>
    <a href="{{ route('sale.panel.achievements') }}" class="menu-item">
        <div class="menu-icon" style="background:#DC2626;"><i class="bi bi-trophy-fill"></i></div>
        <span class="menu-label">Achievements</span>
    </a>
    <a href="{{ route('sale.panel.expenses') }}" class="menu-item">
        <div class="menu-icon" style="background:#7C3AED;"><i class="bi bi-wallet2"></i></div>
        <span class="menu-label">Expenses</span>
    </a>
</div>

<!-- QUICK SALE STRIP -->
<div class="row g-3 mb-4">
    <div class="col-6">
        <a href="{{ route('sale.panel.sale.new') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-white rounded-3"
           style="background:#059669;">
            <i class="bi bi-plus-circle-fill" style="font-size:24px;"></i>
            <div>
                <div style="font-weight:700;font-size:14px;">New Sale</div>
                <div style="font-size:11px;opacity:.85;">Create order</div>
            </div>
        </a>
    </div>
    <div class="col-6">
        <a href="{{ route('sale.panel.payment.in') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-white rounded-3"
           style="background:var(--primary);">
            <i class="bi bi-cash-stack" style="font-size:24px;"></i>
            <div>
                <div style="font-weight:700;font-size:14px;">Payment In</div>
                <div style="font-size:11px;opacity:.85;">Collect payment</div>
            </div>
        </a>
    </div>
</div>

<!-- RECENT SALES -->
<div class="sec-hdr">
    Recent Sales
    <a href="{{ route('sale.panel.transactions') }}">View All</a>
</div>

@forelse($recentOrders as $order)
@php
    $sc = ['Delivered'=>['#DCFCE7','#16A34A'],'Complete'=>['#DCFCE7','#16A34A'],'Pending'=>['#FEF9C3','#CA8A04'],'Cancelled'=>['#FEE2E2','#DC2626']];
    [$sbg,$sfg] = $sc[$order->status] ?? ['#F1F5F9','#64748B'];
@endphp
<div class="txn-item">
    <div class="txn-icon" style="background:{{ $sbg }};color:{{ $sfg }};"><i class="bi bi-bag-check-fill"></i></div>
    <div style="flex:1;min-width:0;">
        <div class="txn-title">{{ $order->order_number ?? '#'.$order->id }}</div>
        <div class="txn-sub">{{ $order->store?->store_name ?? $order->customer_name ?? 'N/A' }} · {{ $order->created_at?->diffForHumans() }}</div>
    </div>
    <div class="text-end">
        <div class="txn-amt" style="color:var(--primary);">₹{{ number_format($order->total_amount ?? $order->amount ?? 0, 0) }}</div>
        <span class="sp-badge" style="background:{{ $sbg }};color:{{ $sfg }};">{{ $order->status }}</span>
    </div>
</div>
@empty
<div class="empty-state">
    <i class="bi bi-bag-x"></i>
    <p>No sales yet.<br><a href="{{ route('sale.panel.sale.new') }}" style="color:var(--primary);">Create your first sale</a></p>
</div>
@endforelse
@endsection
