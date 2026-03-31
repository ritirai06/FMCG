@extends('sale.panel.layout')
@section('title', $party->business_name)
@section('back')1@endsection
@section('back_url', route('sale.panel.parties'))

@section('topnav_actions')
    <a href="tel:{{ $party->mobile }}" title="Call"><i class="bi bi-telephone"></i></a>
    @if($party->geolocation)
    @php [$lat,$lng] = explode(',', $party->geolocation.',0'); @endphp
    <a href="https://www.google.com/maps?q={{ trim($lat) }},{{ trim($lng) }}" target="_blank" title="Location"><i class="bi bi-geo-alt"></i></a>
    @endif
    <a href="{{ route('sale.panel.party.edit', $party->id) }}" title="Edit"><i class="bi bi-pencil"></i></a>
@endsection

@section('content')
@php
    $initials = strtoupper(substr($party->business_name, 0, 2));
    $colors   = ['#2563EB','#059669','#D97706','#DC2626','#7C3AED','#0284C7','#DB2777'];
    $color    = $colors[$party->id % count($colors)];
@endphp

<!-- PARTY HEADER -->
<div class="card-sp text-center" style="padding:28px 16px 20px;">
    <div style="width:72px;height:72px;border-radius:18px;background:{{ $color }}18;color:{{ $color }};
                display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;
                margin:0 auto 14px;">{{ $initials }}</div>
    <div style="font-size:20px;font-weight:700;color:var(--text);">{{ $party->business_name }}</div>
    @if($party->contact_person)
    <div style="font-size:13px;color:var(--muted);margin-top:3px;">{{ $party->contact_person }}</div>
    @endif
    @if($party->route)
    <div style="margin-top:8px;">
        <span class="sp-badge sp-badge-info">{{ $party->route }}</span>
    </div>
    @endif
</div>

<!-- STATS -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:#FEE2E2;color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-currency-rupee"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#DC2626;">₹{{ number_format($totalDue, 0) }}</div>
            <div style="font-size:12px;color:var(--muted);">Due Amount</div>
        </div>
    </div>
    <div class="card-sp d-flex align-items-center gap-3" style="padding:16px;">
        <div style="width:42px;height:42px;border-radius:10px;background:#DCFCE7;color:#16A34A;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
            <i class="bi bi-bag-check"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:var(--text);">{{ $orders->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Total Orders</div>
        </div>
    </div>
</div>

<!-- ACTION BUTTONS ROW 1 -->
<div class="action-bar">
    <a href="tel:{{ $party->mobile }}" class="action-btn" style="background:#EFF6FF;color:#2563EB;">
        <i class="bi bi-telephone-fill"></i><span>Call</span>
    </a>
    @if($party->geolocation)
    <a href="https://www.google.com/maps?q={{ trim($lat) }},{{ trim($lng) }}" target="_blank" class="action-btn" style="background:#F0FDF4;color:#16A34A;">
        <i class="bi bi-geo-alt-fill"></i><span>Location</span>
    </a>
    @else
    <span class="action-btn" style="background:#F8FAFC;color:#94A3B8;cursor:default;">
        <i class="bi bi-geo-alt"></i><span>Location</span>
    </span>
    @endif
    <a href="{{ route('sale.panel.party.edit', $party->id) }}" class="action-btn" style="background:#FFFBEB;color:#D97706;">
        <i class="bi bi-pencil-fill"></i><span>Edit</span>
    </a>
</div>

<!-- ACTION BUTTONS ROW 2 -->
<div class="action-bar" style="margin-bottom:20px;">
    <a href="{{ route('sale.panel.sale.new', ['party_id'=>$party->id]) }}" class="action-btn" style="background:#EFF6FF;color:#2563EB;">
        <i class="bi bi-plus-circle-fill"></i><span>New Sale</span>
    </a>
    <a href="{{ route('sale.panel.payment.in', ['party_id'=>$party->id]) }}" class="action-btn" style="background:#F0FDF4;color:#16A34A;">
        <i class="bi bi-cash-stack"></i><span>Payment In</span>
    </a>
    <a href="{{ route('sale.panel.transactions', ['party_id'=>$party->id]) }}" class="action-btn" style="background:#F5F3FF;color:#7C3AED;">
        <i class="bi bi-receipt"></i><span>Transactions</span>
    </a>
    <a href="{{ route('sale.panel.returns') }}" class="action-btn" style="background:#FFF7ED;color:#EA580C;">
        <i class="bi bi-arrow-counterclockwise"></i><span>Return</span>
    </a>
</div>

<!-- PARTY DETAILS -->
<div class="sp-section-hdr">Party Details</div>
<div class="card-sp" style="padding:0;">
    @php
        $details = [
            'Mobile'        => $party->mobile,
            'Email'         => $party->email ?? null,
            'GSTIN'         => $party->gstin ?? null,
            'Credit Limit'  => $party->credit_limit ? '₹'.number_format($party->credit_limit) : null,
            'Credit Period' => $party->credit_period ? $party->credit_period.' days' : null,
            'Route'         => $party->route ?? null,
            'Address'       => $party->billing_address ?? null,
        ];
    @endphp
    @foreach($details as $label => $value)
        @if($value)
        <div class="detail-row" style="padding:12px 16px;">
            <span class="detail-label">{{ $label }}</span>
            <span class="detail-value">{{ $value }}</span>
        </div>
        @endif
    @endforeach
</div>

<!-- MAP -->
@if($party->geolocation)
<div class="sp-section-hdr">Location</div>
<div style="border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);margin-bottom:16px;height:200px;">
    <iframe src="https://maps.google.com/maps?q={{ trim($lat) }},{{ trim($lng) }}&z=15&output=embed"
            style="width:100%;height:100%;border:none;" allowfullscreen loading="lazy"></iframe>
</div>
@endif

<!-- RECENT ORDERS -->
<div class="sp-section-hdr">Recent Orders</div>
@forelse($orders as $order)
@php
    $statusColors = ['Delivered'=>['#DCFCE7','#16A34A'],'Complete'=>['#DCFCE7','#16A34A'],'Pending'=>['#FEF9C3','#CA8A04'],'Cancelled'=>['#FEE2E2','#DC2626']];
    [$sBg,$sFg] = $statusColors[$order->status] ?? ['#F1F5F9','#64748B'];
@endphp
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:{{ $sBg }};color:{{ $sFg }};"><i class="bi bi-bag-check-fill"></i></div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ $order->order_number ?? '#'.$order->id }}</div>
        <div class="sp-txn-sub">
            {{ $order->created_at?->format('d M Y') }} ·
            <span class="sp-badge" style="background:{{ $sBg }};color:{{ $sFg }};">{{ $order->status }}</span>
        </div>
    </div>
    <div class="sp-txn-amount" style="color:var(--primary);">₹{{ number_format($order->total_amount ?? $order->amount ?? 0, 0) }}</div>
</div>
@empty
<div class="empty-state">
    <i class="bi bi-bag-x"></i>
    <p>No orders yet</p>
</div>
@endforelse

<!-- RECENT PAYMENTS -->
@if($payments->count())
<div class="sp-section-hdr" style="margin-top:8px;">Recent Payments</div>
@foreach($payments as $payment)
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:#DCFCE7;color:#16A34A;"><i class="bi bi-cash-stack"></i></div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ $payment->payment_type }}</div>
        <div class="sp-txn-sub">{{ $payment->payment_date ?? $payment->created_at?->format('d M Y') }}</div>
    </div>
    <div class="sp-txn-amount" style="color:#16A34A;">+₹{{ number_format($payment->amount) }}</div>
</div>
@endforeach
@endif

@endsection
