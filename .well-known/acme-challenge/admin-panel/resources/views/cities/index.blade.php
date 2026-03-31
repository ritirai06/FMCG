
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>City & Locality Management | FMCG Admin</title>

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
.nav-tabs {border-bottom:none;margin-top:20px;}
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

/* PANEL */
.panel {
  background:var(--panel);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  margin-top:20px;
}

/* MODAL */
.modal-content {
  border-radius:16px;
  box-shadow:0 10px 30px rgba(0,0,0,0.15);
  border:none;
}
.modal-header {
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff;
  border-top-left-radius:16px;
  border-top-right-radius:16px;
}

/* CHECKBOX GRID */
.checkbox-grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
  gap:10px;
  margin-top:15px;
}
.checkbox-grid label {
  background:#f8fafc;
  border:1px solid #e2e8f0;
  padding:8px 12px;
  border-radius:10px;
  cursor:pointer;
  transition:.2s;
}
.checkbox-grid input[type="checkbox"]{margin-right:6px;}
.checkbox-grid label:hover {background:#eef2ff;border-color:#c7d2fe;}

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
    <h5><i class="bi bi-geo-alt text-primary me-2"></i>City & Locality Management</h5>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#city">City List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#locality">Locality List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#assign">Assign Localities</button></li>
  </ul>

  <div class="tab-content">
    <!-- CITY TAB -->
    <div class="tab-pane fade show active" id="city">
      <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold">All Cities</h6>
          <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCityModal">
            <i class="bi bi-plus-lg me-1"></i>Add City
          </button>
        </div>
        <table class="table align-middle">
          <thead><tr><th>City</th><th>State</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
         <tbody>
@foreach($cities as $city)
<tr>
    <td>{{ $city->name }}</td>
    <td>{{ $city->state }}</td>

    <td>
        @if($city->status == 'Active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>

    <td class="text-end">
      <button class="btn btn-light btn-sm"
        onclick="editCity('{{ $city->id }}','{{ $city->name }}','{{ $city->state }}','{{ $city->status }}')">
        <i class="bi bi-pencil text-primary"></i>
      </button>

      <form method="POST" action="{{ route('city.delete') }}" style="display:inline">
        @csrf
        <input type="hidden" name="id" value="{{ $city->id }}">
        <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Delete this city?')">
          <i class="bi bi-trash text-danger"></i>
        </button>
      </form>
    </td>
</tr>
@endforeach
</tbody>

        </table>
      </div>
    </div>

    <!-- LOCALITY TAB -->
    <div class="tab-pane fade" id="locality">
      <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold">All Localities</h6>
          <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLocalityModal">
            <i class="bi bi-plus-circle me-1"></i>Add Locality
          </button>
        </div>
        <table class="table align-middle">
          <thead><tr><th>Locality</th><th>City</th><th>Pincode</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
@foreach($localities as $locality)
<tr>
    <td>{{ $locality->name }}</td>

    <td>{{ $locality->city->name }}</td>

    <td>{{ $locality->pincode }}</td>

    <td>
        @if($locality->status=='Active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>

    <td class="text-end">
      <button class="btn btn-light btn-sm" onclick="editLocality('{{ $locality->id }}','{{ $locality->name }}','{{ $locality->city_id }}','{{ $locality->pincode }}','{{ $locality->status }}')">
        <i class="bi bi-pencil text-primary"></i>
      </button>

      <form method="POST" action="{{ route('locality.delete') }}" style="display:inline">
        @csrf
        <input type="hidden" name="id" value="{{ $locality->id }}">
        <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Delete this locality?')">
          <i class="bi bi-trash text-danger"></i>
        </button>
      </form>
    </td>
</tr>
@endforeach
</tbody>

        </table>
      </div>
    </div>

    <!-- ASSIGN TAB -->
    <div class="tab-pane fade" id="assign">
<div class="panel">

<h6 class="fw-bold mb-3">
<i class="bi bi-link-45deg me-2 text-primary"></i>
Assign Localities to City
</h6>

<form method="POST" action="{{ route('assign.localities') }}">
@csrf

<div class="row g-3">

<div class="col-md-6">
<label>Select City</label>

<select id="citySelect" name="city_id" class="form-select">
<option value="">Select City</option>

@foreach($cities as $city)
<option value="{{ $city->id }}">
{{ $city->name }}
</option>
@endforeach

</select>
</div>

</div>

<label class="form-label mt-4">Select Localities</label>

<div id="localityBox" class="checkbox-grid">
<!-- AJAX DATA -->
</div>

<div class="mt-4 text-end">
<button class="btn btn-primary">
<i class="bi bi-check2-circle me-1"></i>
Assign Selected
</button>
</div>

</form>

</div>
</div>


<!-- CITY MODAL -->
<div class="modal fade" id="addCityModal" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cityModalTitle"><i class="bi bi-building me-2"></i>Add City</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('city.store') }}"class="row g-3">
            @csrf
          <input type="hidden" name="id" id="cityId">
          <div class="col-md-6"><label class="form-label">City Name</label><input type="text"name="name" id="cityName" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">State</label><input type="text" name="state" id="cityState" class="form-control"></div>
          <div class="col-md-12"><label class="form-label">Status</label><select name="status" id="cityStatus" class="form-select"><option>Active</option><option>Inactive</option></select></div>
           <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button></div>
    </div>
        </form>
      </div>
     
  </div>
</div>

<!-- LOCALITY MODAL -->
<div class="modal fade" id="addLocalityModal" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="localityModalTitle"><i class="bi bi-geo-alt me-2"></i>Add Locality</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('locality.store') }}" class="row g-3">
@csrf

<input type="hidden" name="id" id="localityId">
<div class="col-md-6">
<label>Locality Name</label>
<input type="text" name="name" id="localityName" class="form-control">
</div>

<div class="col-md-6">
<label>City</label>

<select name="city_id" id="localityCity" class="form-select">
@foreach($cities as $city)
<option value="{{ $city->id }}">{{ $city->name }}</option>
@endforeach
</select>

</div>

<div class="col-md-6">
<label>Pincode</label>
<input type="text" name="pincode" id="localityPincode" class="form-control">
</div>

<div class="col-md-6">
<label>Status</label>
<select name="status" id="localityStatus" class="form-select">
<option>Active</option>
<option>Inactive</option>
</select>
</div>

<div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button></div>
    </div>

</form>

      </div>
     
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCity(id,name,state,status){
  document.getElementById('cityModalTitle').innerHTML='<i class="bi bi-pencil me-2"></i>Edit City';
  document.getElementById('cityId').value = id || '';
  document.getElementById('cityName').value = name || '';
  document.getElementById('cityState').value = state || '';
  document.getElementById('cityStatus').value = status || 'Active';
  new bootstrap.Modal(document.getElementById('addCityModal')).show();
}

function editLocality(id,name,city,pincode,status){
  document.getElementById('localityModalTitle').innerHTML='<i class="bi bi-pencil me-2"></i>Edit Locality';
  document.getElementById('localityId').value = id || '';
  document.getElementById('localityName').value = name || '';
  document.getElementById('localityCity').value = city || '';
  document.getElementById('localityPincode').value = pincode || '';
  document.getElementById('localityStatus').value = status || 'Active';
  new bootstrap.Modal(document.getElementById('addLocalityModal')).show();
}
</script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
<script>
(() => {
  // use named route as template and replace placeholder with selected id
  const baseLocalitiesUrl = "{{ route('get.localities', ['city' => 'CITY_ID_REPLACE']) }}";
  const citySelect = document.getElementById('citySelect');
  const box = document.getElementById('localityBox');

  if (!citySelect || !box) return;

  citySelect.addEventListener('change', function () {
    loadLocalities(this.value);
  });

  // helper to load localities for a given city id
  function loadLocalities(cityId){
    if (!cityId) { box.innerHTML = ''; return; }
    box.innerHTML = '<p>Loading...</p>';

    // replace placeholder with actual city id (named route template)
    const url = baseLocalitiesUrl.replace('CITY_ID_REPLACE', cityId);
    console.log('loadLocalities ->', cityId, url);

    fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok ' + response.status);
        return response.json();
      })
      .then(data => {
        console.log('localities response', data);
        box.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
          box.innerHTML = '<p>No Localities Found</p>';
          return;
        }

        // build HTML string for performance and consistent rendering
        box.innerHTML = data.map(locality => {
          return `<label><input type="checkbox" name="localities[]" value="${locality.id}"> ${locality.name}</label>`;
        }).join('');
      })
      .catch(err => {
        console.error('Error fetching localities:', err);
        box.innerHTML = '<p class="text-danger">Error loading localities</p>';
      });
  }

  // if a city is already selected on page load, auto-load its localities
  if (citySelect.value) {
    loadLocalities(citySelect.value);
  }
})();
</script>


</body>
</html>
