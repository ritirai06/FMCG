
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Warehouse Management | FMCG Admin</title>

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
.status-dot { width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px; }
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}
.manager-img {
  width: 42px; height: 42px; border-radius: 12px; object-fit: cover;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

/* MODAL (Glassmorphic Popup) */
.modal-content {
  border-radius: 16px;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(20px);
  box-shadow: 0 10px 40px rgba(0,0,0,.25);
  border: none;
}
.modal-header {
  border-bottom: none;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: white;
  border-radius: 16px 16px 0 0;
}
.modal-header h5 { font-weight: 600; }
.modal-body {
  padding: 20px;
}
.modal-body .form-control,
.modal-body .form-select {
  border-radius: 10px;
  box-shadow: none;
}
.modal-footer { border-top: none; }

/* PAGINATION */
.pagination .page-link {
  border: none;
  color: var(--primary-dark);
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,.1);
}
.pagination .page-item.active .page-link {
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff;
}

/* RESPONSIVE */
@media(max-width:992px) {
  .sidebar { display: none; }
  .content { margin-left: 0; }
}
</style>
</head>
<body>
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
    <h5><i class="bi bi-building me-2 text-primary"></i>Warehouse Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
      <i class="bi bi-plus-circle me-1"></i>Add Warehouse
    </button>
  </div>
 <div class="row mt-4 g-3">

  <div class="col-md-3">
    <div class="panel text-center">
      <h6 class="text-muted">Total Warehouses</h6>
      <h3 class="fw-bold text-primary">{{ $total }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel text-center">
      <h6 class="text-muted">Active</h6>
      <h3 class="fw-bold text-success">{{ $active }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel text-center">
      <h6 class="text-muted">Inactive</h6>
      <h3 class="fw-bold text-danger">{{ $inactive }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel text-center">
      <h6 class="text-muted">Managers</h6>
      <h3 class="fw-bold text-warning">{{ $managers }}</h3>
    </div>
  </div>

</div>
<div class="d-flex justify-content-between align-items-center mb-3 mt-4">

  <input type="text" id="searchInput"
         class="form-control w-25"
         placeholder="Search warehouse...">

  <select id="statusFilter" class="form-select w-25">
    <option value="">All Status</option>
    <option value="Active">Active</option>
    <option value="Inactive">Inactive</option>
  </select>

</div>
  <!-- TABLE -->
  <div class="panel mt-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Warehouse</th>
            <th>Manager</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
      <tbody>
@forelse($warehouses as $warehouse)
<tr>
    <td>{{ $warehouse->name }}</td>

    <td class="d-flex align-items-center gap-3">
        <img src="https://via.placeholder.com/40" class="manager-img">
        <div>
            <div class="fw-semibold">{{ $warehouse->manager_name }}</div>
            <small class="text-muted">Manager</small>
        </div>
    </td>

    <td>{{ $warehouse->contact }}</td>
    <td>{{ $warehouse->location }}</td>

    <td>
        @if($warehouse->status == 'Active')
            <span class="status-dot status-active"></span>Active
        @else
            <span class="status-dot status-inactive"></span>Inactive
        @endif
    </td>

    <td class="text-end">
         <button class="btn btn-light btn-sm btn-edit"
         data-id="{{ $warehouse->id }}"
         data-name="{{ $warehouse->name }}"
         data-manager="{{ $warehouse->manager_name }}"
         data-contact="{{ $warehouse->contact }}"
         data-location="{{ $warehouse->location }}"
         data-status="{{ $warehouse->status }}"
         title="Edit">
      <i class="bi bi-pencil text-primary"></i>
    </button>
<form action="{{ route('warehouse.delete', $warehouse->id) }}"
      method="POST"
      class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this warehouse?')">

    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-light btn-sm">
        <i class="bi bi-trash text-danger"></i>
    </button>
</form>

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
  </div>
</div>


<!-- ADD WAREHOUSE MODAL -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

     <form action="{{ route('warehouse.store') }}" method="POST">
    @csrf


        <div class="modal-header">
          <h5>Add Warehouse</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="text" name="name" class="form-control mb-3"
                 placeholder="Warehouse Name" required>

          <input type="text" name="manager_name" class="form-control mb-3"
                 placeholder="Manager Name" required>

          <input type="text" name="contact" class="form-control mb-3"
                 placeholder="Contact Number" required>

          <label class="form-label">Location</label>
          <select name="location" class="form-select mb-3" required>
            <option value="">Select location</option>
            @if(isset($localities) && $localities->count())
              @foreach($localities as $loc)
                <option value="{{ $loc->name }}">{{ $loc->name }} ({{ $loc->pincode }})</option>
              @endforeach
            @endif
          </select>

          <select name="status" class="form-select mb-3">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light"
                  data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- EDIT WAREHOUSE MODAL -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <form id="editForm" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5>Edit Warehouse</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="text" name="name" id="editName" class="form-control mb-3" required>
          <input type="text" name="manager_name" id="editManager" class="form-control mb-3" required>
          <input type="text" name="contact" id="editContact" class="form-control mb-3" required>
          <label class="form-label">Location</label>
          <select name="location" id="editLocation" class="form-select mb-3" required>
            <option value="">Select location</option>
            @if(isset($localities) && $localities->count())
              @foreach($localities as $loc)
                <option value="{{ $loc->name }}">{{ $loc->name }} ({{ $loc->pincode }})</option>
              @endforeach
            @endif
          </select>

          <select name="status" id="editStatus" class="form-select mb-3">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </form>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
<script>
function openEditModal(id, name, manager, contact, location, status) {

  // Fill input fields
  document.getElementById('editName').value = name;
  document.getElementById('editManager').value = manager;
  document.getElementById('editContact').value = contact;
  document.getElementById('editLocation').value = location;
  document.getElementById('editStatus').value = status;

  // IMPORTANT: form action set karo dynamically
  document.getElementById('editForm').action = '/warehouse/' + id;

  // Open modal
  var myModal = new bootstrap.Modal(document.getElementById('editWarehouseModal'));
  myModal.show();
}
</script>
<script>
// Attach click listeners to edit buttons (safer than inline onclick)
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.dataset.id;
      const name = this.dataset.name || '';
      const manager = this.dataset.manager || '';
      const contact = this.dataset.contact || '';
      const location = this.dataset.location || '';
      const status = this.dataset.status || '';

      document.getElementById('editName').value = name;
      document.getElementById('editManager').value = manager;
      document.getElementById('editContact').value = contact;
      // select will take the option value
      document.getElementById('editLocation').value = location;
      document.getElementById('editStatus').value = status;
      document.getElementById('editForm').action = '/warehouse/' + id;

      new bootstrap.Modal(document.getElementById('editWarehouseModal')).show();
    });
  });
});
</script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    filterTable();
});

document.getElementById("statusFilter").addEventListener("change", function() {
    filterTable();
});

function filterTable() {
    let search = document.getElementById("searchInput").value.toLowerCase();
    let status = document.getElementById("statusFilter").value;

    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let name = row.cells[0].innerText.toLowerCase();
        let rowStatus = row.cells[4].innerText.trim();

        let matchSearch = name.includes(search);
        let matchStatus = status === "" || rowStatus.includes(status);

        if (matchSearch && matchStatus) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

</body>
</html>
