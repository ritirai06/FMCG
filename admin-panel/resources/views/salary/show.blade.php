<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $salesPerson->name }} - Salary Details | FMCG Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#1e3a8a;
  --accent:#3b82f6;
  --bg:#f1f5f9;
  --panel:#ffffff;
}
body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#e0e7ff,#f9fafb);
  min-height:100vh;
}
.content{
  max-width:1400px;
  margin:0 auto;
  padding:30px;
}
.header-card{
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff;
  border-radius:20px;
  padding:30px;
  box-shadow:0 15px 40px rgba(99,102,241,0.3);
  margin-bottom:30px;
}
.info-card{
  background:var(--panel);
  border-radius:18px;
  padding:25px;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
  margin-bottom:20px;
}
.stat-box{
  background:linear-gradient(135deg,#f0f9ff,#e0f2fe);
  border-left:4px solid #3b82f6;
  padding:20px;
  border-radius:12px;
  margin-bottom:15px;
}
.stat-label{
  font-size:14px;
  color:#64748b;
  font-weight:500;
}
.stat-value{
  font-size:24px;
  font-weight:700;
  color:#0f172a;
}
.table-card{
  background:var(--panel);
  border-radius:18px;
  padding:25px;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
}
.badge-custom{
  padding:8px 16px;
  border-radius:20px;
  font-weight:600;
}
</style>
</head>
<body>

<div class="content">
  <!-- Back Button -->
  <div class="mb-3">
    <a href="{{ route('salary.salary_index') }}" class="btn btn-light">
      <i class="bi bi-arrow-left me-2"></i>Back to Salary List
    </a>
  </div>

  <!-- Header Card -->
  <div class="header-card">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h2 class="mb-2"><i class="bi bi-person-circle me-2"></i>{{ $salesPerson->name }}</h2>
        <p class="mb-0 opacity-75">{{ $salesPerson->role ?? 'Sales Executive' }} • {{ $salesPerson->email }}</p>
      </div>
      <div class="col-md-4 text-end">
        <div class="badge badge-custom bg-white text-primary">
          <i class="bi bi-phone me-1"></i>{{ $salesPerson->phone }}
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Left Column -->
    <div class="col-md-8">
      <!-- Sales Performance -->
      <div class="info-card">
        <h5 class="fw-bold mb-4"><i class="bi bi-graph-up text-primary me-2"></i>Sales Performance</h5>
        <div class="row">
          <div class="col-md-6">
            <div class="stat-box">
              <div class="stat-label">Total Orders</div>
              <div class="stat-value">{{ $totalOrders }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="stat-box">
              <div class="stat-label">Total Sales Amount</div>
              <div class="stat-value">₹{{ number_format($actualSales, 0) }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="stat-box">
              <div class="stat-label">Target Sales</div>
              <div class="stat-value">₹{{ number_format($salesPerson->target_sales ?? 0, 0) }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="stat-box" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);border-left-color:#22c55e;">
              <div class="stat-label">Extra Sales (Above Target)</div>
              <div class="stat-value text-success">₹{{ number_format($extraSales, 0) }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Salary Breakdown -->
      <div class="info-card">
        <h5 class="fw-bold mb-4"><i class="bi bi-cash-stack text-success me-2"></i>Salary Breakdown</h5>
        <table class="table table-borderless">
          <tr>
            <td class="fw-semibold">Base Salary</td>
            <td class="text-end">₹{{ number_format($salesPerson->base_salary ?? 0, 0) }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">Allowances</td>
            <td class="text-end">₹{{ number_format($salesPerson->allowance ?? 0, 0) }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">Bonus ({{ $salesPerson->bonus_percent ?? 0 }}%)</td>
            <td class="text-end">₹{{ number_format($bonus, 0) }}</td>
          </tr>
          <tr style="background:#f0f9ff;">
            <td class="fw-semibold">Incentive ({{ $salesPerson->incentive_percent ?? 0 }}% on extra sales)</td>
            <td class="text-end"><span class="badge bg-success">₹{{ number_format($incentive, 0) }}</span></td>
          </tr>
          <tr style="background:#dbeafe;">
            <td class="fw-bold fs-5">Total Salary</td>
            <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($totalSalary, 0) }}</td>
          </tr>
        </table>
      </div>

      <!-- Incentive Calculation -->
      <div class="info-card">
        <h5 class="fw-bold mb-3"><i class="bi bi-calculator text-warning me-2"></i>Incentive Calculation</h5>
        <div class="alert alert-info">
          <strong>Formula:</strong><br>
          @if($actualSales > ($salesPerson->target_sales ?? 0))
            Extra Sales = Actual Sales - Target Sales<br>
            Extra Sales = ₹{{ number_format($actualSales, 0) }} - ₹{{ number_format($salesPerson->target_sales ?? 0, 0) }} = <strong>₹{{ number_format($extraSales, 0) }}</strong><br><br>
            Incentive = (Extra Sales × {{ $salesPerson->incentive_percent ?? 0 }}%) / 100<br>
            Incentive = (₹{{ number_format($extraSales, 0) }} × {{ $salesPerson->incentive_percent ?? 0 }}%) / 100 = <strong>₹{{ number_format($incentive, 0) }}</strong>
          @else
            <strong>No incentive earned.</strong><br>
            Actual Sales (₹{{ number_format($actualSales, 0) }}) did not exceed Target Sales (₹{{ number_format($salesPerson->target_sales ?? 0, 0) }})
          @endif
        </div>
      </div>

      <!-- Orders List -->
      <div class="table-card">
        <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-info me-2"></i>Recent Orders</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders->take(10) as $order)
              <tr>
                <td><strong>{{ $order->order_number }}</strong></td>
                <td>{{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}</td>
                <td>{{ $order->customer_name ?? '-' }}</td>
                <td>₹{{ number_format($order->total_amount ?? 0, 0) }}</td>
                <td>
                  @if($order->status == 'Delivered')
                    <span class="badge bg-success">Delivered</span>
                  @elseif($order->status == 'Pending')
                    <span class="badge bg-warning">Pending</span>
                  @else
                    <span class="badge bg-secondary">{{ $order->status }}</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted">No orders found</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($orders->count() > 10)
        <div class="text-center mt-3">
          <small class="text-muted">Showing 10 of {{ $orders->count() }} orders</small>
        </div>
        @endif
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-4">
      <!-- Salesperson Details -->
      <div class="info-card">
        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Salesperson Details</h6>
        <div class="mb-3">
          <small class="text-muted">Name</small>
          <div class="fw-semibold">{{ $salesPerson->name }}</div>
        </div>
        <div class="mb-3">
          <small class="text-muted">Email</small>
          <div class="fw-semibold">{{ $salesPerson->email }}</div>
        </div>
        <div class="mb-3">
          <small class="text-muted">Phone</small>
          <div class="fw-semibold">{{ $salesPerson->phone }}</div>
        </div>
        <div class="mb-3">
          <small class="text-muted">Status</small>
          <div>
            @if($salesPerson->status == 'Active')
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-secondary">Inactive</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Assigned Cities -->
      <div class="info-card">
        <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-success me-2"></i>Assigned Cities</h6>
        @if($salesPerson->assignedCities && $salesPerson->assignedCities->count() > 0)
          @foreach($salesPerson->assignedCities as $city)
            <div class="badge bg-light text-dark mb-2 me-1">{{ $city->name }}</div>
          @endforeach
        @else
          <small class="text-muted">No cities assigned</small>
        @endif
      </div>

      <!-- Assigned Localities -->
      <div class="info-card">
        <h6 class="fw-bold mb-3"><i class="bi bi-pin-map text-warning me-2"></i>Assigned Localities</h6>
        @if($salesPerson->localities && $salesPerson->localities->count() > 0)
          <div style="max-height:200px;overflow-y:auto;">
            @foreach($salesPerson->localities as $locality)
              <div class="badge bg-light text-dark mb-2 me-1" style="font-size:11px;">{{ $locality->name }}</div>
            @endforeach
          </div>
        @else
          <small class="text-muted">No localities assigned</small>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
