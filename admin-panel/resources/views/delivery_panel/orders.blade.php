@extends('delivery_panel.layout')

@section('page_title', 'Orders')

@push('styles')
<style>
  .orders-toolbar {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 10px;
    margin-bottom: 14px;
  }
  .orders-kpis {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    margin-bottom: 14px;
  }
  .orders-kpi {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 12px;
  }
  .orders-kpi .lbl { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
  .orders-kpi .val { font-size: 22px; font-weight: 800; line-height: 1.1; margin-top: 4px; }

  .status-chip {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
  }
  .s-assigned  { background: #ede9fe; color: #6d28d9; }
  .s-picked    { background: #dbeafe; color: #1d4ed8; }
  .s-out       { background: #fef3c7; color: #b45309; }
  .s-delivered { background: #dcfce7; color: #166534; }
  .s-failed    { background: #fee2e2; color: #b91c1c; }
  .s-default   { background: #f1f5f9; color: #334155; }

  /* ── Card grid ── */
  .order-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
  }
  .order-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .order-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px 10px;
    border-bottom: 1px solid var(--border);
  }
  .order-card-body { padding: 14px; flex: 1; }
  .order-card-body .store-name { font-size: 16px; font-weight: 700; margin-bottom: 2px; }
  .order-card-body .store-addr { font-size: 12px; color: var(--muted); margin-bottom: 10px; }
  .order-card-meta { display: flex; gap: 16px; font-size: 12px; color: var(--muted); margin-bottom: 14px; flex-wrap: wrap; }
  .order-card-meta span strong { color: #1e293b; }
  .order-card-footer {
    display: flex;
    gap: 8px;
    padding: 10px 14px;
    border-top: 1px solid var(--border);
    background: #f8fafc;
  }
  .btn-go-store {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: #16a34a;
    color: #fff;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    border: none;
  }
  .btn-go-store:hover { background: #15803d; color: #fff; }
  .btn-go-store.disabled { background: #94a3b8; pointer-events: none; }

  @media (max-width: 1200px) { .orders-kpis { grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 992px)  {
    .orders-toolbar { grid-template-columns: 1fr; }
    .orders-kpis    { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 576px)  {
    .orders-kpis { grid-template-columns: 1fr; }
    .order-cards { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
@php
  $statusClass = function ($status) {
      $s = strtolower((string) $status);
      if ($s === 'assigned') return 's-assigned';
      if ($s === 'picked') return 's-picked';
      if ($s === 'out for delivery' || $s === 'out_for_delivery') return 's-out';
      if ($s === 'delivered' || $s === 'completed') return 's-delivered';
      if ($s === 'failed' || $s === 'returned') return 's-failed';
      return 's-default';
  };
@endphp

<div class="orders-kpis">
  <div class="orders-kpi"><div class="lbl">All</div><div class="val">{{ $statusCounts['all'] ?? 0 }}</div></div>
  <div class="orders-kpi"><div class="lbl">Assigned</div><div class="val">{{ $statusCounts['Assigned'] ?? 0 }}</div></div>
  <div class="orders-kpi"><div class="lbl">Picked</div><div class="val">{{ $statusCounts['Picked'] ?? 0 }}</div></div>
  <div class="orders-kpi"><div class="lbl">Out for Delivery</div><div class="val">{{ $statusCounts['Out for Delivery'] ?? 0 }}</div></div>
  <div class="orders-kpi"><div class="lbl">Delivered</div><div class="val">{{ $statusCounts['Delivered'] ?? 0 }}</div></div>
  <div class="orders-kpi"><div class="lbl">Failed / Returned</div><div class="val">{{ $statusCounts['Failed'] ?? 0 }}</div></div>
</div>

<form method="GET" action="{{ route('delivery.panel.orders') }}" class="orders-toolbar">
  <input type="text" class="form-control" value="{{ request('search') }}" name="search" placeholder="Search by order no / customer / store">

  <select class="form-select" name="status">
    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
    @foreach($statuses as $st)
      <option value="{{ $st }}" {{ ($filters['status'] ?? 'all') === $st ? 'selected' : '' }}>{{ $st }}</option>
    @endforeach
  </select>

  <button class="dp-btn dp-btn-primary" type="submit"><i class="fas fa-filter"></i> Apply</button>
</form>

@if($orders->isEmpty())
  <div class="text-center text-muted py-5">No orders found for selected filters.</div>
@else
<div class="order-cards">
  @foreach($orders as $order)
    @php
      $store  = optional($order->store);
      $lat    = $store->latitude ?? null;
      $lng    = $store->longitude ?? null;
      $addr   = $store->address ?? null;
      $phone  = $store->phone ?? $order->customer_phone ?? null;
      $mapUrl = ($lat && $lng)
        ? 'https://www.google.com/maps/dir/?api=1&destination='.$lat.','.$lng
        : ($addr ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($addr) : null);
    @endphp
    <div class="order-card">
      <div class="order-card-header">
        <span class="fw-bold">#{{ $order->order_number ?? ('ORD-' . $order->id) }}</span>
        <span class="status-chip {{ $statusClass($order->status) }}">{{ $order->status ?? 'Pending' }}</span>
      </div>

      <div class="order-card-body">
        <div class="store-name"><i class="fas fa-store me-1 text-success"></i>{{ $store->store_name ?? 'N/A' }}</div>
        <div class="store-addr">{{ $addr ?? 'Address not available' }}</div>

        <div class="order-card-meta">
          <span><strong>Customer:</strong> {{ $order->customer_name ?? 'N/A' }}</span>
          <span><strong>Amount:</strong> Rs {{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</span>
          @if($phone)<span><a href="tel:{{ $phone }}" style="color:var(--primary);"><i class="fas fa-phone me-1"></i>{{ $phone }}</a></span>@endif
        </div>
      </div>

      <div class="order-card-footer">
        @if($mapUrl)
          <a href="{{ $mapUrl }}" target="_blank" class="btn-go-store">
            <i class="fas fa-map-marker-alt"></i> Go to Store
          </a>
        @else
          <span class="btn-go-store disabled"><i class="fas fa-map-marker-alt"></i> No Location</span>
        @endif
        <a href="{{ route('delivery.panel.order.details', ['id' => $order->id]) }}" class="dp-btn dp-btn-ghost" title="View details">
          <i class="fas fa-eye"></i>
        </a>
      </div>
    </div>
  @endforeach
</div>
@endif

@if(method_exists($orders, 'links'))
  <div class="mt-3">{{ $orders->links() }}</div>
@endif
@endsection
