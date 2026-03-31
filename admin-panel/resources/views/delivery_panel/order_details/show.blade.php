@extends('delivery_panel.layout')

@section('page_title', 'Order Details')

@push('styles')
<style>
  .od-grid { display:grid; grid-template-columns: 1.4fr .9fr; gap:14px; }
  .od-card {
    background:#fff; border:1px solid var(--border); border-radius:var(--radius);
    box-shadow:var(--shadow); overflow:hidden;
  }
  .od-card .hd { padding:14px 16px; border-bottom:1px solid var(--border); font-weight:800; }
  .od-card .bd { padding:14px 16px; }

  .od-meta { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:10px; }
  .od-meta .item { background:#f8fafc; border:1px solid var(--border); border-radius:10px; padding:10px 12px; }
  .od-meta .lbl { font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
  .od-meta .val { margin-top:4px; font-size:14px; font-weight:700; color:#0f172a; word-break:break-word; }

  .od-status-chip {
    display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px;
    font-size:11px; font-weight:700;
  }
  .s-assigned  { background:#ede9fe; color:#6d28d9; }
  .s-picked    { background:#dbeafe; color:#1d4ed8; }
  .s-out       { background:#fef3c7; color:#b45309; }
  .s-delivered { background:#dcfce7; color:#166534; }
  .s-failed    { background:#fee2e2; color:#b91c1c; }
  .s-default   { background:#f1f5f9; color:#334155; }

  .od-nav-table th { font-size:12px; color:var(--muted); font-weight:700; }
  .od-nav-table td { font-size:13px; vertical-align:middle; }

  .od-timeline { display:grid; gap:10px; }
  .od-timeline .t-item { display:flex; gap:10px; padding:10px; border:1px solid var(--border); border-radius:10px; background:#fff; }
  .od-timeline .dot {
    width:32px; height:32px; border-radius:999px; display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:12px; flex:0 0 32px;
  }
  .od-timeline .t-title { font-size:14px; font-weight:700; margin-bottom:2px; }
  .od-timeline .t-time { font-size:12px; color:var(--muted); }
  .od-timeline .t-desc { font-size:12px; color:#475569; margin-top:2px; }

  @media (max-width: 1100px) { .od-grid { grid-template-columns: 1fr; } }
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

  $orderNo = $order->order_number ?? ('ORD-' . $order->id);
  $orderTotal = (float) ($order->total_amount ?? $order->amount ?? 0);
  $orderItems = $order->items ?? collect();
@endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h5 class="mb-0 fw-bold">#{{ $orderNo }}</h5>
    <small class="text-muted">{{ optional($order->created_at)->format('d M Y, h:i A') }}</small>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="od-status-chip {{ $statusClass($order->status) }}">{{ $order->status ?? 'Pending' }}</span>
    <span class="fw-bold">Rs {{ number_format($orderTotal, 2) }}</span>
  </div>
</div>

<div class="od-grid">
  <div class="d-grid" style="gap:14px;">
    <div class="od-card">
      <div class="hd">Picked / Assigned Orders</div>
      <div class="bd p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle od-nav-table mb-0">
            <thead>
              <tr>
                <th class="ps-3">Order #</th>
                <th>Store</th>
                <th>Status</th>
                <th class="text-end">Amount</th>
                <th class="text-end pe-3">Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $navigatorRows = collect($orderNavigator ?? collect())->filter(function ($o) {
                  $status = strtolower((string) data_get($o, 'status', ''));
                  return in_array($status, ['assigned', 'pending', 'picked', 'out for delivery', 'out_for_delivery', 'delivered', 'failed', 'returned'], true);
                });
              @endphp
              @forelse($navigatorRows as $navOrder)
                @php
                  $navStoreName = data_get($navOrder, 'store.store_name')
                    ?: data_get($navOrder, 'store_name')
                    ?: data_get($navOrder, 'store')
                    ?: 'Store N/A';
                @endphp
                <tr @if((int)$navOrder->id === (int)$order->id) style="background:#f8fafc;" @endif>
                  <td class="ps-3 fw-semibold">{{ $navOrder->order_number ?? ('ORD-' . $navOrder->id) }}</td>
                  <td>{{ $navStoreName }}</td>
                  <td><span class="od-status-chip {{ $statusClass($navOrder->status) }}">{{ $navOrder->status ?? 'Pending' }}</span></td>
                  <td class="text-end">Rs {{ number_format((float) ($navOrder->total_amount ?? $navOrder->amount ?? 0), 2) }}</td>
                  <td class="text-end pe-3">
                    <a href="{{ route('delivery.panel.order.details', ['id' => $navOrder->id]) }}" class="dp-btn dp-btn-ghost @if((int)$navOrder->id === (int)$order->id) disabled @endif">Open</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No assigned/picked orders found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="od-card">
      <div class="hd">Store Details</div>
      <div class="bd">
        @php
          $hasLocation = !empty($storeLatitude) && !empty($storeLongitude);
          $mapsUrl = $hasLocation
            ? 'https://www.google.com/maps?q=' . $storeLatitude . ',' . $storeLongitude
            : null;
        @endphp
        <div class="od-meta">
          <div class="item"><div class="lbl">Store Name</div><div class="val">{{ $storeName ?? 'Store N/A' }}</div></div>
          <div class="item"><div class="lbl">Store Owner</div><div class="val">{{ $storeOwner ?? 'N/A' }}</div></div>
          <div class="item"><div class="lbl">Contact Number</div><div class="val">{{ $storePhone ?? 'N/A' }}</div></div>
          <div class="item"><div class="lbl">Alternate Number</div><div class="val">{{ $storeAltPhone ?? 'N/A' }}</div></div>
          <div class="item" style="grid-column:1/-1;">
            <div class="lbl">Address</div>
            <div class="val">{{ $storeAddress ?? 'N/A' }}@if(!empty($storeCity) && $storeCity !== 'N/A'), {{ $storeCity }}@endif</div>
          </div>
          <div class="item" style="grid-column:1/-1;">
            <div class="lbl">Location</div>
            @if($hasLocation)
              <div class="val" style="font-size:12px;color:#475569;">{{ $storeLatitude }}, {{ $storeLongitude }}</div>
              <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                 style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;padding:9px 16px;background:#2563EB;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                <i class="fas fa-map-marker-alt"></i> Navigate
              </a>
            @else
              <div class="val" style="color:#94A3B8;font-size:13px;">Location not available</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="od-card">
      <div class="hd">Order Items</div>
      <div class="bd p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th class="ps-3">Product</th>
                <th class="text-center">Qty</th>
                <th class="text-end pe-3">Amount</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orderItems as $item)
                @php
                  $amount = (float) ($item->subtotal ?? $item->total ?? (($item->quantity ?? 0) * ($item->unit_price ?? $item->price ?? 0)));
                @endphp
                <tr>
                  <td class="ps-3">
                    <div class="fw-semibold">{{ data_get($item, 'product.product_name', data_get($item, 'product.name', $item->product_name ?? 'Product')) }}</div>
                    <small class="text-muted">SKU: {{ data_get($item, 'product.sku', 'N/A') }}</small>
                  </td>
                  <td class="text-center">{{ $item->quantity ?? 0 }}</td>
                  <td class="text-end pe-3">Rs {{ number_format($amount, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No order items found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="d-grid" style="gap:14px; align-content:start;">
    <div class="od-card">
      <div class="hd">Order Status Timeline</div>
      <div class="bd">
        <div class="od-timeline">
          @forelse(($statusTimeline ?? collect()) as $event)
            <div class="t-item">
              <div class="dot bg-{{ $event['badge'] ?? 'secondary' }}"><i class="fas {{ $event['icon'] ?? 'fa-circle' }}"></i></div>
              <div>
                <div class="t-title">{{ $event['label'] ?? 'Status Update' }}</div>
                <div class="t-time">{{ optional($event['time'] ?? null)->format('d M Y, h:i A') ?? 'N/A' }}</div>
                @if(!empty($event['description']))<div class="t-desc">{{ $event['description'] }}</div>@endif
              </div>
            </div>
          @empty
            <div class="text-muted">No timeline events yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="od-card">
      <div class="hd">Update Order Status</div>
      <div class="bd">
        <form method="POST" action="{{ route('delivery.panel.orders.status', ['order' => $order->id]) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Select New Status</label>
            <select class="form-select" name="status" id="statusSelect" required>
              <option value="Assigned" @selected(($order->status ?? '') === 'Assigned')>Assigned</option>
              <option value="Picked" @selected(($order->status ?? '') === 'Picked')>Picked</option>
              <option value="Out for Delivery" @selected(($order->status ?? '') === 'Out for Delivery')>Out for Delivery</option>
              <option value="Delivered" @selected(($order->status ?? '') === 'Delivered')>Delivered</option>
              <option value="Failed" @selected(($order->status ?? '') === 'Failed')>Failed</option>
              <option value="Returned" @selected(($order->status ?? '') === 'Returned')>Returned</option>
            </select>
          </div>
          <div class="mb-3" id="reasonDiv" style="display:none;">
            <label class="form-label">Failure Reason</label>
            <select class="form-select" name="failure_reason">
              <option value="">Select reason</option>
              <option value="Owner Not Available">Owner Not Available</option>
              <option value="Wrong Location">Wrong Location</option>
              <option value="Order Cancelled">Order Cancelled</option>
              <option value="Payment Failed">Payment Failed</option>
              <option value="Out of Stock">Out of Stock</option>
              <option value="Customer Rejected">Customer Rejected</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" rows="3" name="notes" placeholder="Add notes...">{{ $order->notes ?? '' }}</textarea>
          </div>
          <button class="dp-btn dp-btn-primary dp-btn-full" type="submit"><i class="fas fa-save"></i> Update Status</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Debug: log store location data
  console.log('Order Details - Store Location:', {
    store_id:   {{ $order->store_id ?? 'null' }},
    store_name: '{{ addslashes($storeName ?? '') }}',
    address:    '{{ addslashes($storeAddress ?? '') }}',
    latitude:   {{ $storeLatitude ?? 'null' }},
    longitude:  {{ $storeLongitude ?? 'null' }},
  });

  const statusSelect = document.getElementById('statusSelect');
  const reasonDiv = document.getElementById('reasonDiv');
  function toggleReason() {
    if (!statusSelect || !reasonDiv) return;
    const v = statusSelect.value;
    reasonDiv.style.display = (v === 'Failed' || v === 'Returned') ? 'block' : 'none';
  }
  statusSelect?.addEventListener('change', toggleReason);
  toggleReason();
</script>
@endpush
