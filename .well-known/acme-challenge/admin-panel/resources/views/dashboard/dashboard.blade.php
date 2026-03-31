@extends('layouts.app')

@section('title','Dashboard')

@section('content')

{{-- TOPBAR --}}


<!-- TOGGLE -->
<button class="toggle-btn d-lg-none" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>

<!-- CONTENT -->
<!-- CONTENT -->
<div class="content">

  <!-- TOP BAR -->
  <div class="topbar mb-4">
    <h5>Dashboard Overview</h5>
    <div class="user-panel">
      <i class="bi bi-person"></i>
      <div>
        <div style="font-weight:600">Super Admin</div>
        <div style="font-size:12px;color:var(--text-muted)">Administrator</div>
      </div>
    </div>
  </div>

  <!-- KPI CARDS -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
      <div class="kpi d-flex justify-content-between">
        <div><span>Total Products</span><h3>{{ $totalProducts }}</h3></div>
        <div class="icon"><i class="bi bi-box"></i></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="kpi d-flex justify-content-between">
        <div><span>Today Orders</span><h3>{{ $todayOrders }}</h3></div>
        <div class="icon"><i class="bi bi-cart-check"></i></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="kpi d-flex justify-content-between">
        <div><span>Today Revenue</span><h3>₹{{ number_format($todayRevenue) }}</h3></div>
        <div class="icon"><i class="bi bi-currency-rupee"></i></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="kpi d-flex justify-content-between">
        <div><span>Low Stock</span><h3 class="text-danger">{{ $lowStock }}</h3></div>
        <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
      </div>
    </div>
  </div>

  <!-- CHARTS -->
  <div class="row g-4 mb-4">

    <!-- SALES LINE CHART -->
    <div class="col-lg-8">
      <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold mb-3">Monthly Sales Growth</h5>
          <canvas id="salesChart" height="120"></canvas>
        </div>
      </div>
    </div>

    <!-- ORDER PIE -->
    <div class="col-lg-4">
      <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold mb-3">Order Status</h5>
          <canvas id="orderChart" height="220"></canvas>
        </div>
      </div>
    </div>

  </div>

  <!-- TABLE + ALERT -->
  <div class="row g-4 mb-4">

    <!-- RECENT ORDERS -->
    <div class="col-lg-12">
      <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold mb-3">Recent Orders</h5>
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Store</th>
                <th>Status</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
@foreach($recentOrders as $order)
<tr>
    <td>#ORD-{{ $order->id }}</td>
    <td>{{ optional($order->store)->store_name ?? 'No Store' }}</td>
    <td>
        <span class="badge 
        {{ $order->status=='Delivered'?'bg-success':
           ($order->status=='Pending'?'bg-warning':'bg-danger') }}">
           {{ $order->status }}
        </span>
    </td>
    
    <td>₹{{ $order->amount }}</td>
</tr>
@endforeach
</tbody>
          </table>
        </div>
      </div>
    </div>


</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: @json($months),
    datasets: [{
      label: 'Sales ₹',
      data: @json($salesData),
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,.2)',
      fill: true,
      tension: .4
    }]
  }
});
</script>
<script>
new Chart(document.getElementById('orderChart'), {
  type: 'doughnut',
  data: {
    labels: ['Delivered','Pending','Cancelled'],
    datasets: [{
      data: @json($statusData),
      backgroundColor: ['#22c55e','#facc15','#ef4444']
    }]
  }
});
</script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
</body>
</html>
