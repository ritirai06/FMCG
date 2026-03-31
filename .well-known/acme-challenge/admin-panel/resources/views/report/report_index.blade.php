
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports & Analytics | FMCG Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#1e3a8a;
  --accent:#3b82f6;
  --highlight:#60a5fa;
  --bg:#f1f5f9;
  --panel:#ffffff;
  --radius:18px;
  --text-dark:#0f172a;
  --text-muted:#6b7280;
}

/* BODY */
body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#e0e7ff,#f9fafb);
  color:var(--text-dark);
  min-height:100vh;
  overflow-x:hidden;
}

/* SIDEBAR */
.sidebar{
  position:fixed;
  top:0;left:0;height:100%;width:270px;
  background:linear-gradient(180deg,#0f172a,#1e3a8a,#1d4ed8);
  color:#fff;padding:28px 18px;
  box-shadow:0 8px 25px rgba(0,0,0,.4);
  overflow-y:auto;z-index:1000;
  transition:all .3s ease;
}
.sidebar::-webkit-scrollbar{width:6px;}
.sidebar::-webkit-scrollbar-thumb{background:#3b82f6;border-radius:6px;}

/* BRAND */
.brand{
  display:flex;align-items:center;gap:12px;
  font-size:20px;font-weight:800;margin-bottom:30px;
}
.brand i{
  width:46px;height:46px;border-radius:12px;
  background:rgba(255,255,255,.1);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;color:#fff;
}

/* MENU */
.menu a{
  display:flex;align-items:center;gap:12px;
  color:rgba(255,255,255,.85);
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:500;
  transition:.3s;
  margin-bottom:6px;
}
.menu a:hover{
  background:rgba(59,130,246,.15);
  transform:translateX(6px);
  color:#fff;
  box-shadow:inset 3px 0 0 var(--highlight);
}
.menu a.active{
  background:rgba(59,130,246,.25);
  box-shadow:inset 4px 0 0 var(--highlight);
  color:#fff;
}

/* SUBMENU */
.menu-item .submenu{
  display:none;
  flex-direction:column;
  margin-left:36px;
  padding-left:10px;
  border-left:2px solid rgba(255,255,255,.15);
  margin-top:4px;
}
.menu-item.open .submenu{display:flex;animation:fadeIn .3s ease;}
.submenu a{
  font-size:14px;
  color:rgba(255,255,255,.8);
  padding:8px 14px;
  border-radius:10px;
  margin-bottom:4px;
}
.submenu a:hover{background:rgba(59,130,246,.25);color:#fff;}
.content {
    margin-left: 250px;
    padding: 24px;
}

.topbar {
    background: #fff;
    padding: 14px 20px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    margin-bottom: 24px;
}

.card-box {
    background: #fff;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.05);
}

.chart-container {
    position: relative;
    height: 300px;
}

@media(max-width: 992px) {
    .sidebar {
        position: relative;
        width: 100%;
    }
    .content {
        margin-left: 0;
    }
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="brand">
   <img src="{{ asset('uploads/admin/'.admin_setting()->profile_image) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
   <span>{{ admin_setting()->company_name ?? 'Admin Panel' }}</span>
  </div>

  <div class="menu">
   
    <a href="{{ route('dashboard') }}" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('product.index') }}"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="{{ route('product.create') }}"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="{{ route('product.status') }}"><i class="bi bi-toggle-on"></i> Product Status</a>
        <a href="#"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
      </div>
    </div>
 
  <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('brands.index') }}"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="{{ route('brands.create') }}"><i class="bi bi-plus-circle"></i> Add Brands</a>
        <a href="{{ route('brands.status') }}"><i class="bi bi-toggle-on"></i>Brands Status </a>
     
      </div>
    </div>
     <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('categories.index') }}"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="{{ route('categories.create') }}"><i class="bi bi-plus-circle"></i> Add categories</a>
        <a href="{{ route('categories.status') }}"><i class="bi bi-toggle-on"></i> Categories Status</a>
        <a href="{{ route('subcategories.index') }}"><i class="bi bi-list-ul"></i> SubCategories</a>
        <a href="{{ route('subcategories.create') }}"><i class="bi bi-plus-circle"></i> Add SubCategory</a>
      </div>
    </div>
	 <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('warehouse.create') }}"><i class="bi bi-list-ul"></i> Warehouses List</a>
        <a href="{{ route('warehouse.status') }}"><i class="bi bi-plus-circle"></i> Status Warehouses</a>
       
     
      </div>
    </div>
   
    <a href="{{ route('inventory.index') }}"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="{{ route('cities.index') }}"><i class="bi bi-geo-alt"></i><span>Cities & Localities</span></a>
    <a href="{{ route('sales.person.index') }}"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="{{ route('delivery.index') }}"><i class="bi bi-truck"></i><span>Delivery Persons</span></a>
    <a href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="{{ route('salary.salary_index') }}"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="{{ route('store.store_index') }}"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="{{ route('order_management.order_index') }}"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="{{ route('report.report_index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
  </div>
</div>

<!-- Content -->
<div class="content">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reports & Analytics</h5>
        <button class="btn btn-success btn-sm">
            <i class="bi bi-download"></i> Export Excel
        </button>
    </div>

    <!-- Filters -->
    <div class="card-box mb-4">
        <form method="GET">
<div class="row g-3">

<div class="col-md-3">
<label>Start Date</label>
<input type="date" name="start_date"
value="{{ request('start_date') }}"
class="form-control">
</div>

<div class="col-md-3">
<label>End Date</label>
<input type="date" name="end_date"
value="{{ request('end_date') }}"
class="form-control">
</div>

<div class="col-md-3">
<label>City</label>
<select name="city" class="form-select">
<option value="">All</option>
@foreach($cities as $city)
<option value="{{ $city->id }}"
{{ request('city')==$city->id?'selected':'' }}>
{{ $city->name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<label>Sales Person</label>
<select name="sales_person" class="form-select">
<option value="">All</option>
@foreach($salesPersons as $user)
<option value="{{ $user->id }}"
{{ request('sales_person')==$user->id?'selected':'' }}>
{{ $user->name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary mt-4">Filter</button>
</div>

</div>
</form>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Total Orders</h6>
                <h4>{{ $totalOrders }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Total Sales</h6>
                <h4>₹ {{ number_format($totalSales) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Pending Deliveries</h6>
                <h4>{{ $pending }}</h4>

            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <h6>Active Sales Persons</h6>
                <h4>{{ $activeSalesPersons }}</h4>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card-box">
                <h6>Sales Trend</h6>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-box">
                <h6>Order Status</h6>
                <div class="chart-container">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="card-box">
        <h6>Detailed Reports</h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sales Person</th>
                        <th>Store</th>
                        <th>City</th>
                        <th>Orders</th>
                        <th>Sales Amount</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>
                <tbody>

@forelse($orders as $order)
<tr>

<td>
{{ \Carbon\Carbon::parse($order->created_at)->format('d-M-Y') }}
</td>

<td>
{{ $order->user->name ?? 'N/A' }}
</td>

<td>
{{ $order->store->name ?? 'N/A' }}
</td>

<td>
{{ $order->city->name ?? 'N/A' }}
</td>

<td>
1
</td>

<td>
₹ {{ number_format($order->amount,2) }}
</td>

<td>
@if($order->status=='delivered')
<span class="badge bg-success">Delivered</span>

@elseif($order->status=='pending')
<span class="badge bg-warning text-dark">Pending</span>

@else
<span class="badge bg-danger">
{{ ucfirst($order->status) }}
</span>
@endif
</td>

</tr>

@empty
<tr>
<td colspan="7" class="text-center">
No Orders Found
</td>
</tr>
@endforelse

</tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Charts JS -->
<script>

/* ===============================
   SALES TREND CHART (DYNAMIC)
================================ */

const salesCtx =
document.getElementById('salesChart').getContext('2d');

new Chart(salesCtx,{
    type:'line',
    data:{
        labels: @json($salesDates),
        datasets:[{
            label:'Sales',
            data: @json($salesTotals),
            backgroundColor:'rgba(59,130,246,0.2)',
            borderColor:'rgba(59,130,246,1)',
            borderWidth:2,
            tension:0.3,
            fill:true
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{beginAtZero:true}
        }
    }
});


/* ===============================
   ORDER STATUS CHART (DYNAMIC)
================================ */

const orderCtx =
document.getElementById('orderChart').getContext('2d');

new Chart(orderCtx,{
    type:'doughnut',
    data:{
        labels: @json($orderStatus->keys()),
        datasets:[{
            data: @json($orderStatus->values()),
            backgroundColor:[
                '#16a34a', // delivered
                '#facc15', // pending
                '#ef4444'  // cancelled
            ]
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{position:'bottom'}
        }
    }
});


/* ===============================
   SIDEBAR SCRIPT
================================ */

function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}

function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}

</script>
</body>
</html>
