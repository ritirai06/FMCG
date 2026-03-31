@extends('sale.panel.layout')
@section('title', 'Transactions')

@section('content')
<!-- FILTER TABS -->
<div class="sp-filter-tabs">
    <a href="{{ route('sale.panel.transactions', ['type'=>'all']) }}"
       class="sp-filter-tab {{ $type==='all' ? 'active' : '' }}">All</a>
    <a href="{{ route('sale.panel.transactions', ['type'=>'sales']) }}"
       class="sp-filter-tab {{ $type==='sales' ? 'active' : '' }}">Sales</a>
    <a href="{{ route('sale.panel.transactions', ['type'=>'payments']) }}"
       class="sp-filter-tab {{ $type==='payments' ? 'active' : '' }}">Payments</a>
</div>

@if($type !== 'payments')
<!-- SALES -->
<div class="sp-section-hdr">Sales Orders</div>
@forelse($orders as $order)
@php
    $statusColors = ['Delivered'=>['#dcfce7','#16a34a'],'Complete'=>['#dcfce7','#16a34a'],'Pending'=>['#fef9c3','#ca8a04'],'Cancelled'=>['#fee2e2','#dc2626']];
    [$bg,$fg] = $statusColors[$order->status] ?? ['#f1f5f9','#64748b'];
@endphp
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:{{ $bg }};color:{{ $fg }};">
        <i class="fas fa-shopping-bag"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ $order->order_number ?? '#'.$order->id }}</div>
        <div class="sp-txn-sub">
            {{ $order->customer_name ?? 'N/A' }} ·
            {{ $order->created_at?->format('d M Y') }}
        </div>
        <div style="margin-top:3px;">
            <span class="sp-badge" style="background:{{ $bg }};color:{{ $fg }};">{{ $order->status }}</span>
            @if($order->items_count ?? $order->items?->count())
            <span class="sp-badge sp-badge-info" style="margin-left:4px;">{{ $order->items?->count() ?? 0 }} items</span>
            @endif
        </div>
        @php
            $dp = $order->assignedDeliveryPerson ?? null;
            $dpUser = (!$dp && $order->assignedDelivery) ? $order->assignedDelivery : null;
            $dpName = $dp?->name ?? $dpUser?->name ?? null;
            $dpPhone = $dp?->phone ?? $dpUser?->phone ?? null;
            $dpVehicle = $dp?->vehicle ?? null;
            $storeLat  = $order->store?->latitude ?? null;
            $storeLng  = $order->store?->longitude ?? null;
            $storeAddr = $order->store?->address ?? null;
            $storePhone = $order->store?->phone ?? null;
            $storeCity = $order->store?->city?->name ?? null;
            $storeDirections = ($storeLat && $storeLng)
                ? 'https://www.google.com/maps/dir/?api=1&destination='.$storeLat.','.$storeLng
                : ($storeAddr ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($storeAddr) : null);
            $storeMap = ($storeLat && $storeLng)
                ? 'https://maps.google.com/?q='.$storeLat.','.$storeLng
                : ($storeAddr ? 'https://maps.google.com/?q='.urlencode($storeAddr) : null);
        @endphp
        @if($dpName)
        <div style="margin-top:8px;background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:10px;padding:10px 12px;">
            {{-- Header: Agent name --}}
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                <span style="background:#7c3aed;color:#fff;border-radius:6px;padding:3px 8px;font-size:11px;font-weight:700;">
                    <i class="fas fa-motorcycle me-1"></i>Delivery Agent
                </span>
                <span style="font-size:13px;font-weight:700;color:#4c1d95;">{{ $dpName }}</span>
            </div>
            {{-- Agent details --}}
            <div style="font-size:12px;color:#6d28d9;margin-bottom:6px;display:flex;flex-wrap:wrap;gap:8px;">
                @if($dpPhone)
                <a href="tel:{{ $dpPhone }}" style="color:#6d28d9;text-decoration:none;">
                    <i class="fas fa-phone me-1"></i>{{ $dpPhone }}
                </a>
                @endif
                @if($dpVehicle)
                <span><i class="fas fa-motorcycle me-1"></i>{{ $dpVehicle }}</span>
                @endif
            </div>
            {{-- Store delivery address --}}
            @if($order->store?->store_name || $storeAddr)
            <div style="background:#ede9fe;border-radius:7px;padding:7px 10px;margin-bottom:6px;">
                <div style="font-size:11px;font-weight:700;color:#7c3aed;margin-bottom:2px;">
                    <i class="fas fa-store me-1"></i>{{ $order->store?->store_name ?? '' }}
                    @if($storeCity) <span style="font-weight:400;color:#9ca3af;">· {{ $storeCity }}</span>@endif
                </div>
                @if($storeAddr)
                <div style="font-size:11px;color:#374151;">
                    <i class="fas fa-map-pin me-1" style="color:#f73a0b;"></i>{{ $storeAddr }}
                </div>
                @endif
            </div>
            @endif
            {{-- Action links --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @if($storeDirections)
                <a href="{{ $storeDirections }}" target="_blank"
                   style="font-size:11px;font-weight:700;color:#fff;background:#f73a0b;border-radius:6px;padding:4px 10px;text-decoration:none;">
                    <i class="fas fa-route me-1"></i>Get Directions
                </a>
                @elseif($storeMap)
                <a href="{{ $storeMap }}" target="_blank"
                   style="font-size:11px;font-weight:700;color:#fff;background:#f73a0b;border-radius:6px;padding:4px 10px;text-decoration:none;">
                    <i class="fas fa-map-marker-alt me-1"></i>View on Map
                </a>
                @endif
                @if($storePhone)
                <a href="tel:{{ $storePhone }}"
                   style="font-size:11px;font-weight:700;color:#fff;background:#16a34a;border-radius:6px;padding:4px 10px;text-decoration:none;">
                    <i class="fas fa-phone me-1"></i>Call Store
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
    <div class="sp-txn-amount" style="color:var(--sp-primary);">₹{{ number_format($order->total_amount ?? $order->amount ?? 0, 0) }}</div>
</div>
@empty
<div class="sp-empty"><i class="fas fa-shopping-bag"></i><p>No sales found</p></div>
@endforelse
{{ $orders->links('vendor.pagination.simple-bootstrap-5') }}
@endif

@if($type !== 'sales')
<!-- PAYMENTS -->
<div class="sp-section-hdr" style="margin-top:{{ $type==='all' ? '16px' : '0' }};">Payments Received</div>
@forelse($payments as $payment)
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:#dcfce7;color:#16a34a;">
        <i class="fas fa-rupee-sign"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ $payment->customer?->business_name ?? 'Unknown' }}</div>
        <div class="sp-txn-sub">
            {{ $payment->payment_type }} ·
            {{ $payment->payment_date ?? $payment->created_at?->format('d M Y') }}
        </div>
        @if($payment->notes)
        <div style="font-size:11px;color:var(--sp-muted);margin-top:2px;">{{ $payment->notes }}</div>
        @endif
    </div>
    <div class="sp-txn-amount" style="color:#16a34a;">+₹{{ number_format($payment->amount) }}</div>
</div>
@empty
<div class="sp-empty"><i class="fas fa-rupee-sign"></i><p>No payments found</p></div>
@endforelse
{{ $payments->links('vendor.pagination.simple-bootstrap-5') }}
@endif

<!-- FAB: New Sale -->
<a href="{{ route('sale.panel.sale.new') }}" style="
    position:fixed;bottom:calc(var(--sp-bottom-h)+16px);right:20px;
    width:56px;height:56px;border-radius:50%;background:var(--sp-primary);color:#fff;
    display:flex;align-items:center;justify-content:center;font-size:24px;
    box-shadow:0 4px 16px rgba(98,89,202,.5);text-decoration:none;z-index:200;">
    <i class="fas fa-plus"></i>
</a>
@endsection
