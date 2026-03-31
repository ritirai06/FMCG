<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Warehouse Status | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
/* CONTENT */
.content { margin-left: 260px; padding: 30px; }

/* TOPBAR */
.topbar {
  background: var(--panel);
  border-radius: var(--radius);
  padding: 16px 24px;
  display: flex; align-items: center; justify-content: space-between;
  box-shadow: 0 10px 40px rgba(2,6,23,.1);
  backdrop-filter: blur(10px);
}
.topbar h5 { font-weight: 700; }

/* PANEL */
.panel {
  background: var(--panel);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,.08);
  backdrop-filter: blur(8px);
}

/* TABLE */
.table thead { background: rgba(99,102,241,0.08); }
.table tbody tr:hover { background: rgba(99,102,241,0.05); transition: .3s; }
.status-badge {
  padding: 6px 12px;
  border-radius: 10px;
  font-weight: 500;
}
.status-active { background: #dcfce7; color: #16a34a; }
.status-inactive { background: #fee2e2; color: #dc2626; }

/* SWITCH */
.form-switch .form-check-input {
  width: 2.5em; height: 1.3em;
  background-color: #d1d5db;
  border: none;
  transition: 0.3s;
}
.form-switch .form-check-input:checked {
  background-color: var(--primary);
}
.form-switch .form-check-input:focus {
  box-shadow: 0 0 0 0.25rem rgba(99,102,241,0.3);
}

/* FILTER BAR */
.filter-card {
  background: var(--panel);
  border-radius: var(--radius);
  padding: 16px 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,.06);
  margin-top: 20px;
}

/* RESPONSIVE */
@media(max-width:992px) {
  .sidebar { display: none; }
  .content { margin-left: 0; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
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



<!-- CONTENT -->


@section('content')

<div class="content">

  <div class="topbar mb-4">
      <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Warehouse Status</h5>

      <div>
          <span class="badge bg-primary">
              Total: {{ $warehouses->total() }}
          </span>
      </div>
  </div>


  <!-- FILTER BAR -->
  <form method="GET" class="mb-4">
    <div class="row g-3">

        <div class="col-md-6">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Search warehouse...">
        </div>

        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </div>
</form>



  <!-- TABLE -->
  <div class="panel mt-4">
      <div class="table-responsive">
          <table class="table align-middle">
              <thead>
                  <tr>
                      <th>Warehouse</th>
                      
                      <th>Manager</th>
                      <th>Contact</th>
                      <th>Status</th>
                      <th class="text-center">Action</th>
                  </tr>
              </thead>

              <tbody>
                  @forelse($warehouses as $warehouse)
                  <tr>
                      <td>{{ $warehouse->name }}</td>
                     
                      <td>{{ $warehouse->manager_name }}</td>
                      <td>{{ $warehouse->contact }}</td>

                      <td>
                          <span class="status-badge
                              {{ $warehouse->status == 'Active'
                                  ? 'status-active'
                                  : 'status-inactive' }}">
                              {{ $warehouse->status }}
                          </span>
                      </td>

                      <td class="text-center">
                          <div class="form-check form-switch">
                              <input class="form-check-input toggle-status"
                                     type="checkbox"
                                     data-id="{{ $warehouse->id }}"
                                     {{ $warehouse->status == 'Active' ? 'checked' : '' }}>
                          </div>
                      </td>
                  </tr>
                  @empty
                  <tr>
                      <td colspan="6" class="text-center text-muted">
                          No Warehouses Found
                      </td>
                  </tr>
                  @endforelse
              </tbody>
          </table>
      </div>

      <div class="mt-3">
          {{ $warehouses->links() }}
      </div>
  </div>
</div>


<script>
document.querySelectorAll('.toggle-status').forEach(toggle => {
    toggle.addEventListener('change', function() {

        let warehouseId = this.dataset.id;

        fetch(`/warehouse/${warehouseId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            location.reload();
        });

    });
});
</script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleStatus(toggle, row) {
  const statusBadge = row.querySelector('.status-badge');
  if (toggle.checked) {
    statusBadge.textContent = "Active";
    statusBadge.className = "status-badge status-active";
  } else {
    statusBadge.textContent = "Inactive";
    statusBadge.className = "status-badge status-inactive";
  }
}
</script>
</body>
</html>
