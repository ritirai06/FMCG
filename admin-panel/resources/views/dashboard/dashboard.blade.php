@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<!-- KPI CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="kpi d-flex justify-content-between align-items-center">
      <div><span>Total Products</span><h3>{{ $totalProducts }}</h3></div>
      <div class="icon"><i class="bi bi-box"></i></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi d-flex justify-content-between align-items-center">
      <div><span>Today Orders</span><h3>{{ $todayOrders }}</h3></div>
      <div class="icon"><i class="bi bi-cart-check"></i></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi d-flex justify-content-between align-items-center">
      <div><span>Today Revenue</span><h3>₹{{ number_format($todayRevenue) }}</h3></div>
      <div class="icon"><i class="bi bi-currency-rupee"></i></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi d-flex justify-content-between align-items-center">
      <div><span>Low Stock</span><h3 class="text-danger">{{ $lowStock }}</h3></div>
      <div class="icon" style="background:#FEF2F2;color:#DC2626;"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
  </div>
</div>

<!-- CHARTS -->
<div class="row g-3 mb-4">
  <div class="col-12 col-lg-8">
    <div class="table-card">
      <h6>Monthly Sales Growth</h6>
      <div style="position:relative;height:200px;">
        <canvas id="salesChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="table-card">
      <h6>Order Status</h6>
      <div style="position:relative;height:200px;display:flex;align-items:center;justify-content:center;">
        <canvas id="orderChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- RECENT ORDERS -->
<div class="table-card">
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h6 class="mb-0">Recent Orders</h6>
    <a href="{{ route('orders.index') }}" class="btn btn-gradient btn-sm">View All</a>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Store</th>
          <th>Status</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentOrders as $order)
        <tr>
          <td class="fw-semibold">#{{ $order->order_number ?? $order->id }}</td>
          <td>{{ optional($order->store)->store_name ?? 'N/A' }}</td>
          <td>
            <span class="badge {{ $order->status=='Delivered' ? 'bg-success' : ($order->status=='Pending' ? 'bg-warning text-dark' : ($order->status=='Assigned' ? 'bg-info' : 'bg-secondary')) }}">
              {{ $order->status }}
            </span>
          </td>
          <td class="fw-semibold">₹{{ number_format($order->total_amount ?? $order->amount ?? 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-4">No orders yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: @json($months),
    datasets: [{ label: 'Sales ₹', data: @json($salesData), borderColor: '#2563EB', backgroundColor: 'rgba(37,99,235,.1)', fill: true, tension: .4, pointRadius: 3 }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { x: { grid: { display: false } }, y: { grid: { color: '#F1F5F9' } } }
  }
});
new Chart(document.getElementById('orderChart'), {
  type: 'doughnut',
  data: {
    labels: ['Delivered','Pending','Cancelled'],
    datasets: [{ data: @json($statusData), backgroundColor: ['#22c55e','#facc15','#ef4444'], borderWidth: 0 }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } } },
    cutout: '65%'
  }
});
</script>
@endpush
@endsection
