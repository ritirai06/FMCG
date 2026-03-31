@extends('sale.panel.layout')
@section('title', 'Achievements')
@section('back')1@endsection
@section('back_url', route('sale.panel.dashboard'))

@section('content')
@php $name = $user?->name ?? $salesPerson?->name ?? 'Sales User'; @endphp

<!-- PROFILE BANNER -->
<div class="card-sp text-center" style="background:var(--primary);color:#fff;padding:28px 16px;margin-bottom:16px;">
    <div style="width:68px;height:68px;border-radius:50%;background:rgba(255,255,255,.2);
                display:flex;align-items:center;justify-content:center;
                font-size:26px;font-weight:700;margin:0 auto 12px;">
        {{ strtoupper(substr($name,0,2)) }}
    </div>
    <div style="font-size:20px;font-weight:700;">{{ strtoupper($name) }}</div>
    <div style="font-size:13px;opacity:.8;margin-top:4px;">Sales Representative</div>
</div>

<!-- STATS GRID -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:#DBEAFE;color:#2563EB;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-graph-up"></i>
        </div>
        <div>
            <div style="font-size:16px;font-weight:700;color:var(--text);">₹{{ number_format($totalSales,0) }}</div>
            <div style="font-size:12px;color:var(--muted);">Total Sales</div>
        </div>
    </div>
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-bag-check"></i>
        </div>
        <div>
            <div style="font-size:16px;font-weight:700;color:var(--text);">{{ $totalOrders }}</div>
            <div style="font-size:12px;color:var(--muted);">Total Orders</div>
        </div>
    </div>
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:#DCFCE7;color:#16A34A;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div style="font-size:16px;font-weight:700;color:var(--text);">{{ $deliveredOrders }}</div>
            <div style="font-size:12px;color:var(--muted);">Delivered</div>
        </div>
    </div>
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:#FDF4FF;color:#7C3AED;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div style="font-size:16px;font-weight:700;color:var(--text);">{{ $totalCustomers }}</div>
            <div style="font-size:12px;color:var(--muted);">Parties</div>
        </div>
    </div>
</div>

<!-- THIS MONTH -->
@php
    $target = 500000;
    $pct    = $target > 0 ? min(100, round(($thisMonthSales / $target) * 100)) : 0;
@endphp
<div class="card-sp" style="margin-bottom:16px;">
    <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">This Month's Sales</div>
    <div style="font-size:28px;font-weight:700;color:var(--primary);">₹{{ number_format($thisMonthSales,0) }}</div>
    <div style="margin-top:12px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
            <span>Target: ₹{{ number_format($target,0) }}</span>
            <span style="font-weight:600;">{{ $pct }}%</span>
        </div>
        <div style="background:var(--border);border-radius:20px;height:8px;overflow:hidden;">
            <div style="width:{{ $pct }}%;height:100%;background:var(--primary);border-radius:20px;transition:width .6s;"></div>
        </div>
    </div>
</div>

<!-- BADGES -->
<div class="sp-section-hdr">Badges Earned</div>
@php
    $badges = [
        ['icon'=>'bi-star-fill',    'label'=>'First Sale',  'earned'=>$totalOrders>=1,        'color'=>'#F59E0B'],
        ['icon'=>'bi-fire',         'label'=>'10 Orders',   'earned'=>$totalOrders>=10,        'color'=>'#EF4444'],
        ['icon'=>'bi-trophy-fill',  'label'=>'₹1L Sales',   'earned'=>$totalSales>=100000,     'color'=>'#7C3AED'],
        ['icon'=>'bi-people-fill',  'label'=>'5 Parties',   'earned'=>$totalCustomers>=5,      'color'=>'#16A34A'],
        ['icon'=>'bi-lightning-fill','label'=>'₹5L Sales',  'earned'=>$totalSales>=500000,     'color'=>'#2563EB'],
        ['icon'=>'bi-gem',          'label'=>'Top Seller',  'earned'=>$totalSales>=1000000,    'color'=>'#DB2777'],
    ];
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;">
    @foreach($badges as $badge)
    <div style="background:{{ $badge['earned'] ? $badge['color'].'18' : 'var(--bg)' }};
                border-radius:12px;padding:18px 8px;text-align:center;
                border:1.5px solid {{ $badge['earned'] ? $badge['color'].'50' : 'var(--border)' }};
                transition:transform .2s;"
         onmouseover="this.style.transform='translateY(-2px)'"
         onmouseout="this.style.transform='none'">
        <i class="bi {{ $badge['icon'] }}"
           style="font-size:28px;color:{{ $badge['earned'] ? $badge['color'] : '#CBD5E1' }};margin-bottom:8px;display:block;"></i>
        <div style="font-size:11.5px;font-weight:700;color:{{ $badge['earned'] ? $badge['color'] : '#94A3B8' }};">
            {{ $badge['label'] }}
        </div>
        @if(!$badge['earned'])
        <div style="font-size:10px;color:#CBD5E1;margin-top:3px;">
            <i class="bi bi-lock-fill"></i> Locked
        </div>
        @else
        <div style="font-size:10px;color:{{ $badge['color'] }};margin-top:3px;font-weight:600;">
            <i class="bi bi-check-circle-fill"></i> Earned
        </div>
        @endif
    </div>
    @endforeach
</div>

@endsection
