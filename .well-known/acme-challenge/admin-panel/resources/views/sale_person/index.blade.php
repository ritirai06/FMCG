<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sales Person Management | FMCG Admin</title>

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
.content{margin-left:260px;padding:30px;}
.topbar{
  background:var(--panel);border-radius:var(--radius);
  padding:16px 24px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);backdrop-filter:blur(10px);
}
.topbar h5{font-weight:700;}

/* PANEL */
.panel{background:var(--panel);border-radius:var(--radius);padding:24px;margin-top:20px;box-shadow:0 15px 40px rgba(0,0,0,.08);}
.table tbody tr:hover{background:rgba(99,102,241,.08);transition:.3s;transform:scale(1.01);}
.status-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px;}
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}
.sales-img{width:40px;height:40px;border-radius:50%;object-fit:cover;}

/* TABS */
.nav-tabs .nav-link{border:none;color:#555;font-weight:600;}
.nav-tabs .nav-link.active{color:#fff;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;}

/* MODAL */
.modal-content{border-radius:18px;box-shadow:0 15px 40px rgba(0,0,0,.15);}
.preview-img{width:70px;height:70px;border-radius:50%;object-fit:cover;margin-top:10px;}

/* RESPONSIVE */
@media(max-width:992px){.sidebar{display:none;}.content{margin-left:0;}}
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
    <h5><i class="bi bi-people-fill me-2 text-primary"></i>Sales Person Management</h5>
    <button id="addSalesBtn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#salesModal">
      <i class="bi bi-plus-circle me-1"></i>Add Sales Person
    </button>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="salesTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#listTab">Sales List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#statusTab">Activate / Deactivate</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cityTab">Assign Cities</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#localityTab">Assign Localities</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#salaryTab">Salary Setup</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#incentiveTab">Incentive Rules</button></li>
  </ul>

  <div class="tab-content mt-3">

    <!-- Sales List -->
    <div class="tab-pane fade show active" id="listTab">
      <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex gap-2 align-items-center">
            <input id="searchSp" class="form-control form-control-sm" placeholder="Search by name, phone, email or city" style="min-width:260px">
            <select id="filterStatus" class="form-select form-select-sm" style="width:160px">
              <option value="">All Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
          <div class="text-muted small">Showing <span id="visibleCount">{{ $salesPeople->count() }}</span> of {{ $salesPeople->count() }}</div>
        </div>

        <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
          <thead>
            <tr>
              <th style="width:36px"><input type="checkbox" id="selectAll"></th>
              <th>Sales Person</th>
              <th>Phone</th>
              <th>Email</th>
              <th>City</th>
              <th>Base Salary</th>
              <th>Allowance</th>
              <th>Bonus %</th>
              <th>Bonus Amt</th>
              <th>Total Comp.</th>
              <th>Target Sales</th>
              <th>Incentive %</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="spTableBody">
            @foreach($salesPeople as $sp)
            <tr>
              <td><input type="checkbox" class="sp-checkbox" value="{{ $sp->id }}"></td>
              <td class="d-flex align-items-center gap-3"><img src="{{ $sp->avatar_url ?? 'https://via.placeholder.com/40' }}" class="sales-img"><div><div class="fw-semibold">{{ $sp->name }}</div><small>{{ $sp->role ?? 'Executive' }}</small></div></td>
              <td>{{ $sp->phone }}</td>
              <td>{{ $sp->email }}</td>
              <td>{{ optional($sp->city)->name }}</td>
              @php
                $base = floatval($sp->base_salary ?? 0);
                $allow = floatval($sp->allowance ?? 0);
                $bonusPct = floatval($sp->bonus_percent ?? 0);
                $bonusAmt = ($base * $bonusPct) / 100;
                $totalComp = $base + $allow + $bonusAmt;
                $target = floatval($sp->target_sales ?? 0);
                $incentivePct = floatval($sp->incentive_percent ?? 0);
                $potentialIncentive = ($target * $incentivePct) / 100;
              @endphp
              <td>{{ number_format($base,2) }}</td>
              <td>{{ number_format($allow,2) }}</td>
              <td>{{ number_format($bonusPct,2) }}%</td>
              <td>{{ number_format($bonusAmt,2) }}</td>
              <td>{{ number_format($totalComp,2) }}</td>
              <td>{{ $target ? number_format($target,2) : '-' }}</td>
              <td>{{ $incentivePct ? number_format($incentivePct,2) . '%' : '-' }}</td>
              <td>@if($sp->status=='Active')<span class="status-dot status-active"></span>Active @else <span class="status-dot status-inactive"></span>Inactive @endif</td>
              <td class="text-end">
                 <a href="javascript:void(0)" class="btn btn-light btn-sm editBtn"
                   data-id="{{ $sp->id }}"
                   data-name="{{ $sp->name }}"
                   data-phone="{{ $sp->phone }}"
                   data-email="{{ $sp->email }}"
                   data-city="{{ $sp->city_id }}"
                   data-status="{{ $sp->status }}"
                   data-avatar="{{ $sp->avatar_path ? asset('storage/' . $sp->avatar_path) : '' }}"
                   data-base_salary="{{ $sp->base_salary }}"
                   data-allowance="{{ $sp->allowance }}"
                   data-bonus_percent="{{ $sp->bonus_percent }}"
                   data-target_sales="{{ $sp->target_sales }}"
                   data-incentive_percent="{{ $sp->incentive_percent }}"
                 ><i class="bi bi-pencil text-primary"></i></a>
                <form method="POST" action="{{ route('sales.person.delete') }}" style="display:inline">@csrf<input type="hidden" name="id" value="{{ $sp->id }}"><button class="btn btn-light btn-sm ms-1" onclick="return confirm('Delete?')"><i class="bi bi-trash text-danger"></i></button></form>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tbody id="noResultsRow" style="display:none">
            <tr><td colspan="14" class="text-center text-muted">No matching sales persons found.</td></tr>
          </tbody>
        </table>
        </div>
        </table>
      </div>
    </div>

    <!-- Activate / Deactivate -->
    <div class="tab-pane fade" id="statusTab">
      <div class="panel text-center">
        <h6>Manage Activation Status</h6>
        <p class="text-muted mb-3">Toggle sales person’s active/inactive state.</p>
        <button class="btn btn-success me-2" onclick="toggleSelected('activate')"><i class="bi bi-toggle-on me-1"></i>Activate Selected</button>
        <button class="btn btn-danger" onclick="toggleSelected('deactivate')"><i class="bi bi-toggle-off me-1"></i>Deactivate Selected</button>
      </div>
    </div>

    <!-- Assign Cities -->
    <div class="tab-pane fade" id="cityTab">
      <div class="panel">
        <h6>Assign Cities</h6>
        <p class="text-muted">Select cities assigned to the salesperson.</p>

        <div class="row g-3">
          <div class="col-md-6">
            <label>Select Sales Person</label>
            <select id="assignCitySp" class="form-select">
              <option value="">Select Sales Person</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}">{{ $sp->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label>Select Cities</label>
            <select id="assignCitiesSelect" class="form-select" multiple>
              @foreach($cities as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button class="btn btn-primary btn-sm" id="saveAssignCities">Save Assignment</button>
        </div>
      </div>
    </div>

    <!-- Assign Localities -->
    <div class="tab-pane fade" id="localityTab">
      <div class="panel">
        <h6>Assign Localities</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label>Select Sales Person</label>
            <select id="assignLocalSp" class="form-select">
              <option value="">Select Sales Person</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}">{{ $sp->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label>Select Localities</label>
            <select id="assignLocalitiesSelect" class="form-select" multiple>
              @foreach($localities as $l)
              <option value="{{ $l->id }}">{{ $l->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button class="btn btn-primary btn-sm" id="saveAssignLocalities">Save Localities</button>
        </div>
      </div>
    </div>

    <!-- Salary Setup -->
    <div class="tab-pane fade" id="salaryTab">
      <div class="panel">
        <h6>Salary Setup</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label>Select Sales Person</label>
            <select id="salarySp" class="form-select">
              <option value="">Select Sales Person</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}">{{ $sp->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Base Salary</label><input id="baseSalary" type="number" step="0.01" class="form-control" placeholder="25000"></div>
          <div class="col-md-6"><label>Allowance</label><input id="allowance" type="number" step="0.01" class="form-control" placeholder="3000"></div>
          <div class="col-md-6"><label>Bonus (%)</label><input id="bonusPercent" type="number" step="0.01" class="form-control" placeholder="10"></div>
        </div>
        <div class="mt-3 text-end"><button class="btn btn-primary btn-sm" id="saveSalary">Save Salary</button></div>
      </div>
    </div>

    <!-- Incentive Rules -->
    <div class="tab-pane fade" id="incentiveTab">
      <div class="panel">
        <h6>Incentive Rules</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label>Select Sales Person</label>
            <select id="incentiveSp" class="form-select">
              <option value="">Select Sales Person</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}">{{ $sp->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Target Sales</label><input id="targetSales" type="number" step="0.01" class="form-control" placeholder="100000"></div>
          <div class="col-md-6"><label>Incentive (%)</label><input id="incentivePercent" type="number" step="0.01" class="form-control" placeholder="5"></div>
        </div>
        <div class="mt-3 text-end"><button class="btn btn-primary btn-sm" id="saveIncentive">Save Incentive</button></div>
      </div>
    </div>

  </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="salesModal" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Sales Person</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('sales.person.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="spId">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="name" id="spName" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" id="spPhone" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="spEmail" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">City</label>
            <select name="city_id" id="spCity" class="form-select"><option value="">Select City</option>@foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
          <div class="col-md-6"><label class="form-label">Status</label>
            <select name="status" id="spStatus" class="form-select"><option>Active</option><option>Inactive</option></select></div>
          <div class="col-md-6">
            <label class="form-label">Profile Image</label>
            <input type="file" name="avatar" class="form-control" id="profileImage">
            <img id="previewImg" class="preview-img" src="" alt="">
          </div>
          <div class="col-md-6"><label class="form-label">Base Salary</label><input type="number" name="base_salary" id="spBaseSalary" class="form-control" step="0.01"></div>
          <div class="col-md-6"><label class="form-label">Allowance</label><input type="number" name="allowance" id="spAllowance" class="form-control" step="0.01"></div>
          <div class="col-md-6"><label class="form-label">Bonus %</label><input type="number" name="bonus_percent" id="spBonusPercent" class="form-control" step="0.01"></div>
          <div class="col-md-6"><label class="form-label">Target Sales</label><input type="number" name="target_sales" id="spTargetSales" class="form-control" step="0.01"></div>
          <div class="col-md-12"><label class="form-label">Incentive %</label><input type="number" name="incentive_percent" id="spIncentivePercent" class="form-control" step="0.01"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal=new bootstrap.Modal(document.getElementById('salesModal'));
// handle Add vs Edit
document.getElementById('addSalesBtn')?.addEventListener('click', () => {
  document.querySelector('.modal-title').innerHTML = '<i class="bi bi-plus-circle me-2 text-primary"></i>Add Sales Person';
  // reset form
  document.getElementById('spId').value = '';
  document.getElementById('spName').value = '';
  document.getElementById('spPhone').value = '';
  document.getElementById('spEmail').value = '';
  document.getElementById('spCity').value = '';
  document.getElementById('spStatus').value = 'Active';
  document.getElementById('spBaseSalary').value = '';
  document.getElementById('spAllowance').value = '';
  document.getElementById('spBonusPercent').value = '';
  document.getElementById('spTargetSales').value = '';
  document.getElementById('spIncentivePercent').value = '';
  document.getElementById('previewImg').src = '';
});

document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const el = e.currentTarget;
    document.querySelector('.modal-title').innerHTML = '<i class="bi bi-pencil-square me-2 text-primary"></i>Edit Sales Person';
    document.getElementById('spId').value = el.dataset.id || '';
    document.getElementById('spName').value = el.dataset.name || '';
    document.getElementById('spPhone').value = el.dataset.phone || '';
    document.getElementById('spEmail').value = el.dataset.email || '';
    document.getElementById('spCity').value = el.dataset.city || '';
    document.getElementById('spStatus').value = el.dataset.status || 'Active';
    document.getElementById('spBaseSalary').value = el.dataset.base_salary || '';
    document.getElementById('spAllowance').value = el.dataset.allowance || '';
    document.getElementById('spBonusPercent').value = el.dataset.bonus_percent || '';
    document.getElementById('spTargetSales').value = el.dataset.target_sales || '';
    document.getElementById('spIncentivePercent').value = el.dataset.incentive_percent || '';
    const avatar = el.dataset.avatar || '';
    if (avatar) { document.getElementById('previewImg').src = avatar; }
    new bootstrap.Modal(document.getElementById('salesModal')).show();
  });
});

document.getElementById('profileImage')?.addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) { document.getElementById('previewImg').src = URL.createObjectURL(file); }
});
</script>
<script>
// select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
  const checked = this.checked;
  document.querySelectorAll('.sp-checkbox').forEach(cb => cb.checked = checked);
});

function toggleSelected(action){
  const ids = Array.from(document.querySelectorAll('.sp-checkbox:checked')).map(i => i.value);
  if (!ids.length) { showToast('Please select at least one sales person.', 'warning'); return; }

  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  fetch("{{ route('sales.person.toggleStatus') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ ids: ids, action: action })
  })
  .then(res => {
    if (!res.ok) throw new Error('Network error');
    return res.json().catch(()=>null);
  })
  .then(() => { showToast('Status updated', 'success'); setTimeout(()=>location.reload(),600); })
  .catch(err => { console.error(err); showToast('Failed to update status','danger'); });
}
</script>
<!-- Toasts container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
  <div id="toastContainer"></div>
</div>

<script>
function showToast(message, level = 'info'){
  const container = document.getElementById('toastContainer');
  const bg = level === 'success' ? 'bg-success text-white' : (level === 'danger' ? 'bg-danger text-white' : (level === 'warning' ? 'bg-warning text-dark' : 'bg-primary text-white'));
  const toastId = 't'+Date.now();
  const html = `<div id="${toastId}" class="toast ${bg}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>`;
  container.insertAdjacentHTML('beforeend', html);
  const el = document.getElementById(toastId);
  const t = new bootstrap.Toast(el);
  t.show();
  // remove after hidden
  el.addEventListener('hidden.bs.toast', ()=> el.remove());
}

function setBtnLoading(btn, loading){
  if(!btn) return;
  if(loading){
    btn.dataset.orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>` + (btn.dataset.label || 'Saving');
  } else {
    btn.disabled = false;
    btn.innerHTML = btn.dataset.orig || btn.innerHTML;
  }
}
</script>
<script>
// helper to fetch json
async function fetchJson(url){
  const res = await fetch(url, { headers: { 'Accept':'application/json' } });
  if (!res.ok) throw new Error('Network');
  return res.json();
}

// when selecting a sales person in assign cities tab, load assigned cities
document.getElementById('assignCitySp')?.addEventListener('change', async function(){
  const id = this.value; if(!id) return;
  try{
    const data = await fetchJson(`{{ url('sales-person') }}/${id}/details`);
    // mark cities
    const sel = document.getElementById('assignCitiesSelect');
    Array.from(sel.options).forEach(o => o.selected = data.city_ids.includes(parseInt(o.value)));
  }catch(e){ console.error(e); showToast('Failed to load details','danger'); }
});

document.getElementById('saveAssignCities')?.addEventListener('click', async function(){
  const sp = document.getElementById('assignCitySp').value; if(!sp){ showToast('Select Sales Person','warning'); return; }
  const cityIds = Array.from(document.getElementById('assignCitiesSelect').selectedOptions).map(o=>o.value);
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  try{
    await fetch("{{ route('sales.person.assignCities') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify({ sales_person_id: sp, city_ids: cityIds }) });
    showToast('Saved','success');
  }catch(e){ console.error(e); showToast('Failed to save','danger'); }
});

// assign localities
document.getElementById('assignLocalSp')?.addEventListener('change', async function(){
  const id = this.value; if(!id) return;
  try{
    const data = await fetchJson(`{{ url('sales-person') }}/${id}/details`);
    const sel = document.getElementById('assignLocalitiesSelect');
    Array.from(sel.options).forEach(o => o.selected = data.locality_ids.includes(parseInt(o.value)));
  }catch(e){ console.error(e); showToast('Failed to load details','danger'); }
});

document.getElementById('saveAssignLocalities')?.addEventListener('click', async function(){
  const sp = document.getElementById('assignLocalSp').value; if(!sp){ showToast('Select Sales Person','warning'); return; }
  const ids = Array.from(document.getElementById('assignLocalitiesSelect').selectedOptions).map(o=>o.value);
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  try{
    await fetch("{{ route('sales.person.assignLocalities') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify({ sales_person_id: sp, locality_ids: ids }) });
    showToast('Saved','success');
  }catch(e){ console.error(e); showToast('Failed to save','danger'); }
});

// salary
document.getElementById('salarySp')?.addEventListener('change', async function(){
  const id = this.value; if(!id) return;
  try{
    const data = await fetchJson(`{{ url('sales-person') }}/${id}/details`);
    document.getElementById('baseSalary').value = data.base_salary ?? '';
    document.getElementById('allowance').value = data.allowance ?? '';
    document.getElementById('bonusPercent').value = data.bonus_percent ?? '';
  }catch(e){ console.error(e); }
});

document.getElementById('saveSalary')?.addEventListener('click', async function(){
  const sp = document.getElementById('salarySp').value; if(!sp){ showToast('Select Sales Person','warning'); return; }
  const payload = { sales_person_id: sp, base_salary: document.getElementById('baseSalary').value || null, allowance: document.getElementById('allowance').value || null, bonus_percent: document.getElementById('bonusPercent').value || null };
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const btn = document.getElementById('saveSalary');
  try{ setBtnLoading(btn, true); await fetch("{{ route('sales.person.updateSalary') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify(payload) }); showToast('Saved','success'); }catch(e){ console.error(e); showToast('Failed to save','danger'); } finally { setBtnLoading(btn, false); }
});

// incentive
document.getElementById('incentiveSp')?.addEventListener('change', async function(){
  const id = this.value; if(!id) return;
  try{ const data = await fetchJson(`{{ url('sales-person') }}/${id}/details`); document.getElementById('targetSales').value = data.target_sales ?? ''; document.getElementById('incentivePercent').value = data.incentive_percent ?? ''; }catch(e){ console.error(e); }
});

document.getElementById('saveIncentive')?.addEventListener('click', async function(){
  const sp = document.getElementById('incentiveSp').value; if(!sp){ showToast('Select Sales Person','warning'); return; }
  const payload = { sales_person_id: sp, target_sales: document.getElementById('targetSales').value || null, incentive_percent: document.getElementById('incentivePercent').value || null };
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const btn2 = document.getElementById('saveIncentive');
  try{ setBtnLoading(btn2, true); await fetch("{{ route('sales.person.updateIncentive') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify(payload) }); showToast('Saved','success'); }catch(e){ console.error(e); showToast('Failed to save','danger'); } finally { setBtnLoading(btn2, false); }
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
<script>
// client-side search & filter for sales table
function updateVisibleCount(){
  const total = document.querySelectorAll('#spTableBody tr').length;
  const visible = Array.from(document.querySelectorAll('#spTableBody tr')).filter(r => r.style.display !== 'none').length;
  document.getElementById('visibleCount').textContent = visible;
  document.getElementById('noResultsRow').style.display = visible ? 'none' : '';
}

function matchesRow(row, q, status){
  if (!q && !status) return true;
  const text = (row.textContent || '').toLowerCase();
  if (q && !text.includes(q)) return false;
  if (status){
    const st = row.querySelector('td:nth-last-child(2)').textContent.trim();
    if (st !== status) return false;
  }
  return true;
}

document.getElementById('searchSp')?.addEventListener('input', function(){
  const q = this.value.trim().toLowerCase();
  const status = document.getElementById('filterStatus')?.value || '';
  Array.from(document.querySelectorAll('#spTableBody tr')).forEach(r => {
    r.style.display = matchesRow(r, q, status) ? '' : 'none';
  });
  updateVisibleCount();
});

document.getElementById('filterStatus')?.addEventListener('change', function(){
  const q = document.getElementById('searchSp').value.trim().toLowerCase();
  const status = this.value || '';
  Array.from(document.querySelectorAll('#spTableBody tr')).forEach(r => {
    r.style.display = matchesRow(r, q, status) ? '' : 'none';
  });
  updateVisibleCount();
});

// initialize counts on load
document.addEventListener('DOMContentLoaded', updateVisibleCount);
</script>
</body>
</html>