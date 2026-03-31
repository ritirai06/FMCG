
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Inventory Management | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
.content {
  margin-left:260px;
  padding:30px;
}

/* TOPBAR */
.topbar {
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}
.topbar h5 {font-weight:700;}

/* TABS */
.nav-tabs {
  border-bottom:none;
  margin-top:20px;
}
.nav-tabs .nav-link {
  background:rgba(255,255,255,0.4);
  border:none;
  border-radius:10px;
  margin-right:8px;
  color:#4f46e5;
  font-weight:600;
  transition:.3s;
}
.nav-tabs .nav-link.active {
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff;
}

/* PANELS */
.tab-content .panel {
  background:var(--panel);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  margin-top:20px;
}

/* TABLE */
.table thead {background:rgba(99,102,241,0.08);}
.table tbody tr:hover {
  background:rgba(99,102,241,0.05);
  transform:scale(1.01);
  transition:all .2s;
}

/* MODAL */
.modal-content {
  border-radius:18px;
  box-shadow:0 15px 40px rgba(0,0,0,.2);
}
.modal-header {
  border:none;
}
.modal-footer {
  border:none;
}

/* RESPONSIVE */
@media(max-width:992px){
  .sidebar{display:none;}
  .content{margin-left:0;}
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
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-stack text-primary me-2"></i>Inventory Management</h5>
   
  </div>

  <!-- NAV TABS -->
  <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#list">Inventory List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#in">Stock In</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#out">Stock Out</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#adjust">Adjustment</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#audit">Audit Logs</button></li>
  </ul>

  <!-- TAB CONTENT -->
  <div class="tab-content">

    <!-- Inventory List -->
    <div class="tab-pane fade show active" id="list">
      <div class="panel">
        <h6 class="fw-bold mb-3">Current Inventory (Warehouse-wise)</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Warehouse</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Available Stock</th>
                <th>Last Updated</th>
                <th>Status</th>
              </tr>
            </thead>
           <tbody>
@foreach($inventories as $inventory)
<tr>
    <td>{{ $inventory->warehouse->name }}</td>
    <td>{{ $inventory->product->name }}</td>
    <td>{{ $inventory->product->sku }}</td>
    <td>{{ $inventory->quantity }}</td>
    <td>{{ $inventory->updated_at->format('d M Y') }}</td>
    <td>
        @if($inventory->quantity > 50)
            <span class="badge bg-success">In Stock</span>
        @elseif($inventory->quantity > 0)
            <span class="badge bg-warning">Low</span>
        @else
            <span class="badge bg-danger">Out of Stock</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>

          </table>
        </div>
      </div>
    </div>

    
    <!-- Stock In -->
<div class="tab-pane fade" id="in">
  <div class="panel">
    <h6 class="fw-bold mb-3">Add Stock Entry</h6>

    <form action="{{ route('inventory.stockIn') }}" method="POST" class="row g-3">
      @csrf

      <!-- Warehouse -->
      <div class="col-md-4">
        <label class="form-label">Warehouse</label>
        <select name="warehouse_id" class="form-select" required>
          <option value="">Select Warehouse</option>
          @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}">
              {{ $warehouse->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Product -->
      <div class="col-md-4">
        <label class="form-label">Product</label>
        <select name="product_id" class="form-select" required>
          <option value="">Select Product</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}">
              {{ $product->name }} ({{ $product->sku }})
            </option>
          @endforeach
        </select>
        <small class="current-stock text-muted"></small>
      </div>

      <!-- Quantity -->
      <div class="col-md-4">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
      </div>

      <!-- Supplier -->
      <div class="col-md-6">
        <label class="form-label">Supplier</label>
        <input type="text" name="supplier" class="form-control">
      </div>

      <!-- Date -->
      <div class="col-md-6">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control">
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i>Save Entry
        </button>
      </div>

    </form>
  </div>
</div>


    <!-- Stock Out -->
<div class="tab-pane fade" id="out">
  <div class="panel">
    <h6 class="fw-bold mb-3">Stock Out</h6>

    <form action="{{ route('inventory.stockOut') }}" method="POST" class="row g-3">
      @csrf

      <!-- Warehouse -->
      <div class="col-md-4">
        <label class="form-label">Warehouse</label>
        <select name="warehouse_id" class="form-select" required>
          <option value="">Select Warehouse</option>
          @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}">
              {{ $warehouse->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Product -->
      <div class="col-md-4">
        <label class="form-label">Product</label>
        <select name="product_id" class="form-select" required>
          <option value="">Select Product</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}">
              {{ $product->name }} ({{ $product->sku }})
            </option>
          @endforeach
        </select>
        <small class="current-stock text-muted"></small>
      </div>

      <!-- Quantity -->
      <div class="col-md-4">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
      </div>

      <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-danger">
          <i class="bi bi-arrow-up-circle me-1"></i>Confirm Out
        </button>
      </div>

    </form>
  </div>
</div>


   <!-- Adjustment -->
<div class="tab-pane fade" id="adjust">
  <div class="panel">
    <hr class="my-4">

<h6 class="fw-bold mb-3">Adjustment History</h6>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Date</th>
            <th>Warehouse</th>
            <th>Product</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        @forelse($adjustments as $adj)
        <tr>
            <td>{{ $adj->created_at->format('d-m-Y') }}</td>
            <td>{{ $adj->warehouse->name }}</td>
            <td>{{ $adj->product->name }}</td>
            <td>
                @if($adj->type == 'add')
                    <span class="badge bg-success">Add</span>
                @else
                    <span class="badge bg-danger">Remove</span>
                @endif
            </td>
            <td>{{ $adj->quantity }}</td>
            <td>{{ $adj->reason }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">
                No adjustment history
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

    <h6 class="fw-bold mb-3">Inventory Adjustment</h6>

    <form action="{{ route('inventory.adjust') }}" method="POST" class="row g-3">
      @csrf

      <!-- Warehouse -->
      <div class="col-md-4">
        <label class="form-label">Warehouse</label>
        <select name="warehouse_id" class="form-select" required>
          <option value="">Select Warehouse</option>
          @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}">
              {{ $warehouse->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Product -->
      <div class="col-md-4">
        <label class="form-label">Product</label>
        <select name="product_id" class="form-select" required>
          <option value="">Select Product</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}">
              {{ $product->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Adjustment Type -->
      <div class="col-md-4">
        <label class="form-label">Adjustment Type</label>
        <select name="type" class="form-select" required>
          <option value="add">Add</option>
          <option value="remove">Remove</option>
        </select>
      </div>

      <!-- Quantity -->
      <div class="col-md-6">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
      </div>

      <!-- Reason -->
      <div class="col-md-6">
        <label class="form-label">Reason</label>
        <input type="text" name="reason" class="form-control" required>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-warning">
          <i class="bi bi-pencil-square me-1"></i> Adjust Stock
        </button>
      </div>

    </form>
  </div>
</div>


    <!-- Audit Logs -->
    <div class="tab-pane fade" id="audit">
      <div class="panel">
        <h6 class="fw-bold mb-3">Audit Logs</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Details</th></tr></thead>
           <tbody>
@forelse($auditLogs as $log)
<tr>
    <td>{{ $log->created_at->format('d M Y') }}</td>
    <td>{{ $log->user }}</td>
    <td>
        <span class="badge bg-primary">
            {{ $log->action }}
        </span>
    </td>
    <td>{{ $log->details }}</td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted">
        No audit records found
    </td>
</tr>
@endforelse
</tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function loadProductsForWarehouse(selectElement) {
    const warehouseId = selectElement.value;
    const form = selectElement.closest('form');
    const productSelect = form.querySelector('select[name="product_id"]');
    const tabId = selectElement.closest('.tab-pane').id;

    productSelect.innerHTML = '<option value="">Select Product</option>';
    productSelect.dataset.items = '';
    const stockHint = form.querySelector('.current-stock');
    if (stockHint) stockHint.textContent = '';
    if (!warehouseId) return;

    fetch(`{{ url('inventory/warehouse') }}/${warehouseId}/products`)
        .then(r=>r.json())
        .then(items=>{
            productSelect.dataset.items = JSON.stringify(items);
            items.forEach(item=>{
                if (tabId === 'out' && item.quantity <= 0) return;
                let text = `${item.name} (${item.sku})`;
                if (tabId === 'out') text += ` - Avail: ${item.quantity}`;
                productSelect.innerHTML += `<option value="${item.id}">${text}</option>`;
            });
        })
        .catch(e=>console.error('load products failed',e));
}

function showCurrentStock(sel) {
    const hint = sel.closest('form').querySelector('.current-stock');
    hint.textContent = '';
    if (!sel.value) return;
    let items = [];
    try { items = JSON.parse(sel.dataset.items||'[]'); } catch(_){}
    const found = items.find(i=>i.id == sel.value);
    if (found) hint.textContent = `Current stock: ${found.quantity}`;
}

 document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('select[name="warehouse_id"]').forEach(el=>{
        el.addEventListener('change',function(){ loadProductsForWarehouse(this); });
    });
    document.querySelectorAll('select[name="product_id"]').forEach(el=>{
        el.addEventListener('change',function(){ showCurrentStock(this); });
    });
 });
</script>
</body>
</html>
