
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Store Management | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<script>
const _csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';
async function postJson(url, data){
  const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify(data) });
  let j = null; let text = null;
  try{ j = await res.json(); }catch(e){ try{ text = await res.text(); }catch(e){} }
  if(res.ok) return j || { ok:true };
  // validation errors
  if(res.status===422 && j) return j;
  // other errors: normalize
  const out = { ok:false, status: res.status };
  if(j){ out.errors = j.errors || null; out.message = j.message || null; out.data = j; }
  else if(text) { out.message = text; }
  return out;
}
async function delReq(url){
  const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
  let j=null; let text=null; try{ j = await res.json(); }catch(e){ try{ text = await res.text(); }catch(e){} }
  if(j) return j;
  return { ok: res.ok, status: res.status, message: text };
}

function storeRowHtml(s){
  const statusBadge = s.status?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>';
  return `<tr data-id="${s.id}"><td>${s.store_name}</td><td>${s.code||''}</td><td>${s.manager||''}</td><td>${s.phone||''}</td><td>${s.address||''}</td><td>${statusBadge}</td><td class="text-end"><button class="btn btn-light btn-sm edit-store" data-id="${s.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-store" data-id="${s.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}

async function loadStores(){
  try{
    const res = await fetch('/store/list', { headers:{ 'Accept':'application/json' } });
    const j = await res.json();
    const tbody = document.getElementById('storesTbody'); tbody.innerHTML='';
    if(j && j.ok && j.data.length){
      j.data.forEach(s=> tbody.insertAdjacentHTML('beforeend', storeRowHtml(s)));
    } else {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No stores.</td></tr>';
    }
  }catch(err){ console.error(err); document.getElementById('storesTbody').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Load failed.</td></tr>'; }
}

// initial load
loadStores();
</script>
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
.content { margin-left: 290px; padding: 30px; }

/* HEADER */
.topbar {
  background: var(--glass);
  border-radius: 20px;
  padding: 18px 25px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  backdrop-filter: blur(12px);
  position: relative;
  z-index: 1100;
}
.topbar h5 { font-weight: 700; }

/* TABS */
.nav-tabs {
  background: var(--glass);
  border-radius: 15px;
  padding: 5px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  backdrop-filter: blur(10px);
}
.nav-tabs .nav-link {
  color: #334155;
  border: none;
  font-weight: 500;
  border-radius: 10px;
  transition: 0.3s;
}
.nav-tabs .nav-link.active {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  box-shadow: 0 3px 10px rgba(99,102,241,0.3);
}

/* CARD */
.table-card {
  background: var(--glass);
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
  padding: 25px;
  backdrop-filter: blur(12px);
  margin-top: 25px;
}
.table tbody tr:hover {
  background: rgba(99,102,241,0.08);
  transition: 0.3s;
  transform: scale(1.01);
}

/* MODAL */
.modal-content {
  border-radius: 18px;
  border: none;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(15px);
  box-shadow: 0 10px 35px rgba(0,0,0,0.2);
}
.modal-header, .modal-footer { border: none; }
.form-control, .form-select {
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}
.form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.25);
}

/* RESPONSIVE */
@media(max-width:992px){
  .sidebar{display:none;}
  .content{margin-left:0;}
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
    <h5><i class="bi bi-shop text-primary me-2"></i>Store Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStoreModal">
      <i class="bi bi-plus-circle me-1"></i> Add Store
    </button>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="storeTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#list" role="tab">Store List</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inventory" role="tab">Inventory</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contacts" role="tab">Contacts</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab">Settings</a></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- STORE LIST -->
    <div class="tab-pane fade show active" id="list" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Stores</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Store Name</th><th>Code</th><th>Manager</th><th>Phone</th><th>Address</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="storesTbody">
              <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- INVENTORY -->
    <div class="tab-pane fade" id="inventory" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Store Inventory Overview</h6>
          <div><button class="btn btn-sm btn-primary" id="addInventoryBtn" data-bs-toggle="modal" data-bs-target="#addInventoryModal"><i class="bi bi-plus-circle me-1"></i> Add Inventory</button></div>
        </div>
        <p class="text-muted">Quick snapshot of stock levels by store.</p>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Store</th><th>SKU Count</th><th>Low Stock Items</th><th>Last Sync</th><th>Actions</th></tr></thead>
            <tbody id="inventoryTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CONTACTS -->
    <div class="tab-pane fade" id="contacts" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Store Contacts</h6>
          <div><button class="btn btn-sm btn-primary" id="addContactBtn" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="bi bi-plus-circle me-1"></i> Add Contact</button></div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Store</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody id="contactsTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SETTINGS -->
    <div class="tab-pane fade" id="settings" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Store Settings</h6>
          <div><button class="btn btn-sm btn-primary" id="addSettingBtn" data-bs-toggle="modal" data-bs-target="#addSettingModal"><i class="bi bi-plus-circle me-1"></i> Add Setting</button></div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Store</th><th>Notifications</th><th>Sync Enabled</th><th>Notes</th><th>Actions</th></tr></thead>
            <tbody id="settingsTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ADD STORE MODAL -->
<div class="modal fade" id="addStoreModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Add Store</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="addStoreForm">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Store Name</label><input type="text" name="store_name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Code</label><input type="text" name="code" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Manager</label><input type="text" name="manager" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
          <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Status</label>
            <select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select>
          </div>
        </div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveStoreBtn">Save</button></div>
    </div>
  </div>
</div>

<!-- EDIT STORE MODAL -->
<div class="modal fade" id="editStoreModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Edit Store</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="editStoreForm">
        <input type="hidden" id="edit_store_id" name="id" />
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Store Name</label><input type="text" name="store_name" id="edit_store_name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Code</label><input type="text" name="code" id="edit_code" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Manager</label><input type="text" name="manager" id="edit_manager" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
          <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" id="edit_address" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select>
          </div>
        </div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="updateStoreBtn">Update</button></div>
    </div>
  </div>
</div>
    <!-- Additional modals for Inventory, Contacts, Settings -->
    <!-- ADD INVENTORY MODAL -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Add Inventory</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="addInventoryForm">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="inv_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">SKU Count</label><input type="number" name="sku_count" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Low Stock Items</label><input type="number" name="low_stock_items" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Last Sync</label><input type="date" name="last_sync" class="form-control"></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveInventoryBtn">Save</button></div>
        </div>
      </div>
    </div>

    <!-- EDIT INVENTORY MODAL -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Edit Inventory</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="editInventoryForm"><input type="hidden" id="edit_inventory_id" name="id">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="edit_inv_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">SKU Count</label><input type="number" name="sku_count" id="edit_sku_count" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Low Stock Items</label><input type="number" name="low_stock_items" id="edit_low_stock_items" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Last Sync</label><input type="date" name="last_sync" id="edit_last_sync" class="form-control"></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="updateInventoryBtn">Update</button></div>
        </div>
      </div>
    </div>

    <!-- ADD CONTACT MODAL -->
    <div class="modal fade" id="addContactModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Add Contact</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="addContactForm">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="contact_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">Contact Person</label><input type="text" name="contact_person" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveContactBtn">Save</button></div>
        </div>
      </div>
    </div>

    <!-- EDIT CONTACT MODAL -->
    <div class="modal fade" id="editContactModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Edit Contact</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="editContactForm"><input type="hidden" id="edit_contact_id" name="id">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="edit_contact_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">Contact Person</label><input type="text" name="contact_person" id="edit_contact_person" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" id="edit_contact_phone" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="edit_contact_email" class="form-control"></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="updateContactBtn">Update</button></div>
        </div>
      </div>
    </div>

    <!-- ADD SETTING MODAL -->
    <div class="modal fade" id="addSettingModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Add Setting</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="addSettingForm">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="setting_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">Notifications</label><select name="notifications_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
              <div class="col-md-6"><label class="form-label">Sync Enabled</label><select name="sync_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
              <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control"></textarea></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="saveSettingBtn">Save</button></div>
        </div>
      </div>
    </div>

    <!-- EDIT SETTING MODAL -->
    <div class="modal fade" id="editSettingModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
          <div class="modal-header"><h5 class="modal-title">Edit Setting</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="editSettingForm"><input type="hidden" id="edit_setting_id" name="id">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Store</label><select name="store_id" id="edit_setting_store_select" class="form-select"></select></div>
              <div class="col-md-6"><label class="form-label">Notifications</label><select name="notifications_enabled" id="edit_notifications_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
              <div class="col-md-6"><label class="form-label">Sync Enabled</label><select name="sync_enabled" id="edit_sync_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
              <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" id="edit_notes" class="form-control"></textarea></div>
            </div>
            </form>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="updateSettingBtn">Update</button></div>
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
<script>
function showToast(msg, ok=true){
  let container = document.getElementById('toastContainer');
  if(!container){
    container = document.createElement('div');
    container.id = 'toastContainer';
    Object.assign(container.style, { position: 'fixed', top: '18px', right: '18px', zIndex: 3000, display: 'flex', flexDirection: 'column', gap: '10px', maxWidth: '360px' });
    document.body.appendChild(container);
  }
  const wrapper = document.createElement('div');
  wrapper.innerHTML = `<div class="toast align-items-center text-bg-${ok?'success':'danger'} border-0 show" role="alert"><div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
  const toastEl = wrapper.firstElementChild;
  container.appendChild(toastEl);
  setTimeout(()=>{ toastEl.classList.remove('show'); toastEl.remove(); }, 3500);
}
</script>
<script>
// Store modal handlers (UI only) - use closest() so inner clicks work
document.addEventListener('click', function(e){
  const saveStoreBtn = e.target.closest && e.target.closest('#saveStoreBtn');
  if(saveStoreBtn){
    e.preventDefault();
    const form = document.getElementById('addStoreForm');
    const fd = new FormData(form);
    const data = Object.fromEntries(fd.entries());
    postJson('/store/store', data).then(res=>{
      if(res && res.ok){
        loadStores();
        showToast('Store saved');
        const modalEl = document.getElementById('addStoreModal');
        const m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        m.hide();
        form.reset();
      } else if(res && res.errors){ showToast(Object.values(res.errors)[0][0], false); }
      else showToast('Save failed', false);
    }).catch(err=>{ console.error(err); showToast('Save failed', false); });
    return;
  }

  const editStoreBtn = e.target.closest && e.target.closest('.edit-store');
  if(editStoreBtn){
    const id = editStoreBtn.getAttribute('data-id');
    fetch(`/store/edit/${id}`, { headers:{ 'Accept':'application/json' } }).then(r=>r.json()).then(j=>{
      if(j && j.ok){ const s = j.data; document.getElementById('edit_store_id').value = s.id; document.getElementById('edit_store_name').value = s.store_name; document.getElementById('edit_code').value = s.code||''; document.getElementById('edit_manager').value = s.manager||''; document.getElementById('edit_phone').value = s.phone||''; document.getElementById('edit_address').value = s.address||''; document.getElementById('edit_status').value = s.status?1:0; new bootstrap.Modal(document.getElementById('editStoreModal')).show(); }
    }).catch(err=>{ console.error(err); showToast('Load failed', false); });
    return;
  }

  const delStoreBtn = e.target.closest && e.target.closest('.delete-store');
  if(delStoreBtn){
    const id = delStoreBtn.getAttribute('data-id'); if(!confirm('Delete store?')) return;
    delReq(`/store/delete/${id}`).then(r=>{ if(r && r.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); } else showToast('Delete failed', false); }).catch(err=>{ console.error(err); showToast('Delete failed', false); });
    return;
  }

  const updateStoreBtn = e.target.closest && e.target.closest('#updateStoreBtn');
  if(updateStoreBtn){
    e.preventDefault();
    const id = document.getElementById('edit_store_id').value;
    const form = document.getElementById('editStoreForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson(`/store/update/${id}`, data).then(res=>{ if(res && res.ok){ loadStores(); showToast('Updated'); const modalEl = document.getElementById('editStoreModal'); const m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); m.hide(); } else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false); else showToast('Update failed', false); }).catch(err=>{ console.error(err); showToast('Update failed', false); });
    return;
  }
});
</script>
<script>
// Populate store select elements
function populateStoreSelects(list){
  const ids = ['inv_store_select','edit_inv_store_select','contact_store_select','edit_contact_store_select','setting_store_select','edit_setting_store_select'];
  ids.forEach(id=>{
    const sel = document.getElementById(id);
    if(!sel) return;
    sel.innerHTML = '<option value="">Select store</option>';
    list.forEach(s=> sel.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.store_name}</option>`));
  });
}

// INVENTORY
function inventoryRowHtml(it){
  return `<tr data-id="${it.id}"><td>${it.store?.store_name||''}</td><td>${it.sku_count||0}</td><td>${it.low_stock_items||0}</td><td>${it.last_sync||''}</td><td class="text-end"><button class="btn btn-light btn-sm edit-inv" data-id="${it.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-inv" data-id="${it.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}
async function loadInventory(){
  try{
    const res = await fetch('/store/inventory/list',{ headers:{ 'Accept':'application/json' }});
    const j = await res.json(); const tbody = document.getElementById('inventoryTbody'); tbody.innerHTML='';
    if(j && j.ok && j.data.length){ j.data.forEach(it=> tbody.insertAdjacentHTML('beforeend', inventoryRowHtml(it))); }
    else tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No inventory.</td></tr>';
  }catch(e){ console.error(e); document.getElementById('inventoryTbody').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Load failed.</td></tr>'; }
}

// CONTACTS
function contactRowHtml(c){
  return `<tr data-id="${c.id}"><td>${c.store?.store_name||''}</td><td>${c.contact_person||''}</td><td>${c.phone||''}</td><td>${c.email||''}</td><td class="text-end"><button class="btn btn-light btn-sm edit-contact" data-id="${c.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-contact" data-id="${c.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}
async function loadContacts(){
  try{ const res = await fetch('/store/contacts/list',{ headers:{ 'Accept':'application/json' }}); const j = await res.json(); const tbody = document.getElementById('contactsTbody'); tbody.innerHTML='';
    if(j && j.ok && j.data.length){ j.data.forEach(c=> tbody.insertAdjacentHTML('beforeend', contactRowHtml(c))); }
    else tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No contacts.</td></tr>';
  }catch(e){ console.error(e); document.getElementById('contactsTbody').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Load failed.</td></tr>'; }
}

// SETTINGS
function settingRowHtml(s){
  const notif = s.notifications_enabled? 'Enabled':'Disabled';
  const sync = s.sync_enabled? 'Enabled':'Disabled';
  return `<tr data-id="${s.id}"><td>${s.store?.store_name||''}</td><td>${notif}</td><td>${sync}</td><td>${(s.notes||'').slice(0,60)}</td><td class="text-end"><button class="btn btn-light btn-sm edit-setting" data-id="${s.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-setting" data-id="${s.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}
async function loadSettings(){
  try{ const res = await fetch('/store/settings/list',{ headers:{ 'Accept':'application/json' }}); const j = await res.json(); const tbody = document.getElementById('settingsTbody'); tbody.innerHTML='';
    if(j && j.ok && j.data.length){ j.data.forEach(s=> tbody.insertAdjacentHTML('beforeend', settingRowHtml(s))); }
    else tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No settings.</td></tr>';
  }catch(e){ console.error(e); document.getElementById('settingsTbody').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Load failed.</td></tr>'; }
}

// Hook into existing loadStores to also populate selects and lists
const _origLoadStores = loadStores;
loadStores = async function(){
  await _origLoadStores();
  // fetch list of stores to populate selects
  try{
    const res = await fetch('/store/list',{ headers:{ 'Accept':'application/json' }});
    const j = await res.json();
    if(j && j.ok){ populateStoreSelects(j.data); }
  }catch(e){ console.error('populate stores failed', e); }
  // load other lists
  loadInventory(); loadContacts(); loadSettings();
}

// Event delegation for inventory/contacts/settings actions (use closest())
document.addEventListener('click', function(e){
  const saveInv = e.target.closest && e.target.closest('#saveInventoryBtn');
  if(saveInv){
    const form = document.getElementById('addInventoryForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson('/store/inventory/store', data).then(res=>{
      if(res && res.ok){
        loadInventory(); showToast('Inventory saved'); bootstrap.Modal.getInstance(document.getElementById('addInventoryModal'))?.hide(); form.reset();
      } else if(res && res.errors){
        console.error('Inventory save validation failed', res);
        showToast(Object.values(res.errors)[0][0], false);
      } else {
        console.error('Inventory save failed', res);
        showToast(res.message || 'Save failed', false);
      }
    }).catch(err=>{ console.error('Inventory save exception', err); showToast('Save failed', false); });
    return;
  }

  const editInv = e.target.closest && e.target.closest('.edit-inv');
  if(editInv){
    const id = editInv.getAttribute('data-id'); fetch(`/store/inventory/edit/${id}`,{ headers:{ 'Accept':'application/json' }}).then(r=>r.json()).then(j=>{ if(j && j.ok){ const it=j.data; document.getElementById('edit_inventory_id').value=it.id; document.getElementById('edit_sku_count').value=it.sku_count||''; document.getElementById('edit_low_stock_items').value=it.low_stock_items||''; document.getElementById('edit_last_sync').value=it.last_sync||''; document.getElementById('edit_inv_store_select').value = it.store_id||''; new bootstrap.Modal(document.getElementById('editInventoryModal')).show(); } }).catch(err=>{ console.error(err); showToast('Load failed', false); });
    return;
  }

  const updateInv = e.target.closest && e.target.closest('#updateInventoryBtn');
  if(updateInv){
    const id = document.getElementById('edit_inventory_id').value; const form = document.getElementById('editInventoryForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson(`/store/inventory/update/${id}`, data).then(res=>{ if(res && res.ok){ loadInventory(); showToast('Inventory updated'); bootstrap.Modal.getInstance(document.getElementById('editInventoryModal'))?.hide(); } else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false); else showToast('Update failed', false); }).catch(err=>{ console.error(err); showToast('Update failed', false); });
    return;
  }

  const delInv = e.target.closest && e.target.closest('.delete-inv');
  if(delInv){
    const id = delInv.getAttribute('data-id'); if(!confirm('Delete inventory?')) return; delReq(`/store/inventory/delete/${id}`).then(r=>{ if(r && r.ok){ document.querySelector('#inventoryTbody tr[data-id="'+id+'"]').remove(); showToast('Deleted'); } else showToast('Delete failed', false); }).catch(err=>{ console.error(err); showToast('Delete failed', false); });
    return;
  }

  const saveContact = e.target.closest && e.target.closest('#saveContactBtn');
  if(saveContact){
    const form = document.getElementById('addContactForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson('/store/contacts/store', data).then(res=>{ if(res && res.ok){ loadContacts(); showToast('Contact saved'); bootstrap.Modal.getInstance(document.getElementById('addContactModal'))?.hide(); form.reset(); } else if(res && res.errors){ console.error('Contact save validation failed', res); showToast(Object.values(res.errors)[0][0], false); } else { console.error('Contact save failed', res); showToast(res.message || 'Save failed', false); } }).catch(err=>{ console.error('Contact save exception', err); showToast('Save failed', false); });
    return;
  }

  const editContact = e.target.closest && e.target.closest('.edit-contact');
  if(editContact){
    const id = editContact.getAttribute('data-id'); fetch(`/store/contacts/edit/${id}`,{ headers:{ 'Accept':'application/json' }}).then(r=>r.json()).then(j=>{ if(j && j.ok){ const c=j.data; document.getElementById('edit_contact_id').value=c.id; document.getElementById('edit_contact_person').value=c.contact_person||''; document.getElementById('edit_contact_phone').value=c.phone||''; document.getElementById('edit_contact_email').value=c.email||''; document.getElementById('edit_contact_store_select').value=c.store_id||''; new bootstrap.Modal(document.getElementById('editContactModal')).show(); } }).catch(err=>{ console.error(err); showToast('Load failed', false); });
    return;
  }

  const updateContact = e.target.closest && e.target.closest('#updateContactBtn');
  if(updateContact){
    const id = document.getElementById('edit_contact_id').value; const form = document.getElementById('editContactForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson(`/store/contacts/update/${id}`, data).then(res=>{ if(res && res.ok){ loadContacts(); showToast('Contact updated'); bootstrap.Modal.getInstance(document.getElementById('editContactModal'))?.hide(); } else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false); else showToast('Update failed', false); }).catch(err=>{ console.error(err); showToast('Update failed', false); });
    return;
  }

  const delContact = e.target.closest && e.target.closest('.delete-contact');
  if(delContact){
    const id = delContact.getAttribute('data-id'); if(!confirm('Delete contact?')) return; delReq(`/store/contacts/delete/${id}`).then(r=>{ if(r && r.ok){ document.querySelector('#contactsTbody tr[data-id="'+id+'"]').remove(); showToast('Deleted'); } else showToast('Delete failed', false); }).catch(err=>{ console.error(err); showToast('Delete failed', false); });
    return;
  }

  const saveSetting = e.target.closest && e.target.closest('#saveSettingBtn');
  if(saveSetting){
    const form = document.getElementById('addSettingForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson('/store/settings/store', data).then(res=>{ if(res && res.ok){ loadSettings(); showToast('Setting saved'); bootstrap.Modal.getInstance(document.getElementById('addSettingModal'))?.hide(); form.reset(); } else if(res && res.errors){ console.error('Setting save validation failed', res); showToast(Object.values(res.errors)[0][0], false); } else { console.error('Setting save failed', res); showToast(res.message || 'Save failed', false); } }).catch(err=>{ console.error('Setting save exception', err); showToast('Save failed', false); });
    return;
  }

  const editSetting = e.target.closest && e.target.closest('.edit-setting');
  if(editSetting){
    const id = editSetting.getAttribute('data-id'); fetch(`/store/settings/edit/${id}`,{ headers:{ 'Accept':'application/json' }}).then(r=>r.json()).then(j=>{ if(j && j.ok){ const s=j.data; document.getElementById('edit_setting_id').value=s.id; document.getElementById('edit_notifications_enabled').value = s.notifications_enabled?1:0; document.getElementById('edit_sync_enabled').value = s.sync_enabled?1:0; document.getElementById('edit_notes').value = s.notes||''; document.getElementById('edit_setting_store_select').value = s.store_id||''; new bootstrap.Modal(document.getElementById('editSettingModal')).show(); } }).catch(err=>{ console.error(err); showToast('Load failed', false); });
    return;
  }

  const updateSetting = e.target.closest && e.target.closest('#updateSettingBtn');
  if(updateSetting){
    const id = document.getElementById('edit_setting_id').value; const form = document.getElementById('editSettingForm'); const fd = new FormData(form); const data = Object.fromEntries(fd.entries());
    postJson(`/store/settings/update/${id}`, data).then(res=>{ if(res && res.ok){ loadSettings(); showToast('Setting updated'); bootstrap.Modal.getInstance(document.getElementById('editSettingModal'))?.hide(); } else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false); else showToast('Update failed', false); }).catch(err=>{ console.error(err); showToast('Update failed', false); });
    return;
  }

  const delSetting = e.target.closest && e.target.closest('.delete-setting');
  if(delSetting){
    const id = delSetting.getAttribute('data-id'); if(!confirm('Delete setting?')) return; delReq(`/store/settings/delete/${id}`).then(r=>{ if(r && r.ok){ document.querySelector('#settingsTbody tr[data-id="'+id+'"]').remove(); showToast('Deleted'); } else showToast('Delete failed', false); }).catch(err=>{ console.error(err); showToast('Delete failed', false); });
    return;
  }
});

// Initial load (populate store selects and lists)
document.addEventListener('DOMContentLoaded', function(){ loadStores(); });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

