
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Delivery Partner Management | FMCG Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

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
.content{
  margin-left:270px;
  padding:30px;
  background:var(--bg);
  min-height:100vh;
  transition:.4s;
}
@media(max-width:992px){
  .sidebar{left:-100%;}
  .sidebar.show{left:0;}
  .content{margin-left:0;}
  .toggle-btn{
    position:fixed;top:18px;left:18px;
    background:var(--primary);color:#fff;border:none;
    border-radius:10px;width:44px;height:44px;font-size:22px;z-index:2000;
  }
}


/* CONTENT */
.content{margin-left:250px;padding:30px;}
.topbar{
  background:var(--glass-panel);
  border-radius:16px;
  padding:16px 25px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 30px rgba(0,0,0,.1);
  backdrop-filter:blur(10px);
}
.topbar h5{font-weight:700;}

/* BUTTONS */
.btn-primary{
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  border:none;border-radius:10px;
  font-weight:600;
  transition:0.3s;
}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(99,102,241,0.3);}
.btn-light{border-radius:10px;}

/* TABLE */
.table-card{
  background:var(--glass-panel);
  border-radius:18px;
  box-shadow:0 15px 40px rgba(0,0,0,.08);
  margin-top:25px;padding:20px;
  backdrop-filter:blur(10px);
}
.table tbody tr{
  transition:all 0.3s ease;
}
.table tbody tr:hover{
  background:rgba(99,102,241,.08);
  transform:scale(1.01);
}
.status-dot{
  width:10px;height:10px;border-radius:50%;
  display:inline-block;margin-right:6px;
}
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}
.partner-img{
  width:48px;height:48px;border-radius:50%;object-fit:cover;
  box-shadow:0 4px 8px rgba(0,0,0,0.15);
}

/* MODALS */
.modal-content{
  border:none;
  border-radius:16px;
  background:rgba(255,255,255,0.95);
  backdrop-filter:blur(15px);
  box-shadow:0 10px 40px rgba(0,0,0,0.15);
}
.modal-header{border:none;}
.modal-footer{border:none;}
.form-control, .form-select{
  border-radius:10px;
  border:1px solid #e2e8f0;
}
.form-control:focus, .form-select:focus{
  border-color:var(--primary);
  box-shadow:0 0 0 0.15rem rgba(99,102,241,0.25);
}
.modal h5 i{font-size:20px;}

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
    <h5><i class="bi bi-truck text-primary me-2"></i>Delivery Partner Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
      <i class="bi bi-plus-circle me-1"></i>Add Partner
    </button>
  </div>

  <!-- PARTNER LIST -->
    <div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex gap-2">
        <input id="dpSearch" class="form-control form-control-sm" placeholder="Search partner, phone, vehicle" style="min-width:260px">
      </div>
      <div class="text-muted small">Showing <span id="dpCount">{{ $partners->count() }}</span></div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th style="width:36px"><input type="checkbox" id="selectAllDp"></th>
            <th>Partner</th>
            <th>Phone</th>
            <th>Vehicle</th>
            <th>Zones</th>
            <th>Orders Count</th>
            <th>Order Value</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="dpBody">
          @foreach($partners as $p)
          <tr data-id="{{ $p->id }}">
            <td><input type="checkbox" class="dp-checkbox" value="{{ $p->id }}"></td>
            <td class="d-flex align-items-center gap-3"><img src="{{ $p->avatar_path ? asset('storage/'.$p->avatar_path) : 'https://i.pravatar.cc/45' }}" class="partner-img"><div><div class="fw-semibold">{{ $p->name }}</div><small class="text-muted">{{ $p->email }}</small></div></td>
            <td class="dp-phone">{{ $p->phone }}</td>
            <td class="dp-vehicle">{{ $p->vehicle }}</td>
            <td class="dp-zones">{{ is_array($p->zones_json) ? implode(', ', $p->zones_json) : ($p->zones_json ?? '') }}</td>
            <td class="dp-orders"><span class="badge bg-info">{{ $p->order_count ?? 0 }}</span></td>
            <td class="dp-value"><strong>₹{{ number_format($p->order_total ?? 0, 2) }}</strong></td>
            <td class="dp-status">@if($p->status=='Active')<span class="status-dot status-active"></span>Active @else <span class="status-dot status-inactive"></span>Inactive @endif</td>
            <td class="text-end">
              <button class="btn btn-light btn-sm dp-edit" data-id="{{ $p->id }}"><i class="bi bi-pencil text-primary"></i></button>
              <button class="btn btn-light btn-sm ms-1 dp-assign-zone" data-id="{{ $p->id }}"><i class="bi bi-geo-alt text-success"></i></button>
              <button class="btn btn-light btn-sm ms-1 dp-assign-order" data-id="{{ $p->id }}"><i class="bi bi-box2-heart text-warning"></i></button>
              <button class="btn btn-light btn-sm ms-1 dp-delete" data-id="{{ $p->id }}"><i class="bi bi-trash text-danger"></i></button>
            </td>
          </tr>
          @endforeach
        </tbody>
        <tbody id="noDp" style="display:none"><tr><td colspan="8" class="text-center text-muted">No partners found.</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD / EDIT PARTNER MODAL -->
<div class="modal fade" id="addPartnerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle text-primary me-2"></i><span id="modalTitle">Add Delivery Partner</span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="partnerForm" method="POST" action="{{ route('delivery.person.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="partnerId">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" id="partnerName" type="text" class="form-control" placeholder="Enter name" required></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" id="partnerPhone" type="text" class="form-control" placeholder="+91 ..."></div>
          <div class="col-md-6"><label class="form-label">Email</label><input name="email" id="partnerEmail" type="email" class="form-control" placeholder="example@email.com"></div>
          <div class="col-md-6"><label class="form-label">Vehicle</label><input name="vehicle" id="partnerVehicle" type="text" class="form-control" placeholder="Bike / Car + Number"></div>
          <div class="col-md-6"><label class="form-label">Status</label><select name="status" id="partnerStatus" class="form-select"><option>Active</option><option>Inactive</option></select></div>
          <div class="col-md-6"><label class="form-label">Profile Image</label><input name="avatar" id="partnerAvatar" type="file" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="partnerSave">Save Partner</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT handled by add modal (same form) -->

<!-- ASSIGN ZONES MODAL -->
<div class="modal fade" id="assignZoneModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-geo-alt text-success me-2"></i>Assign Zones</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Zones (comma separated)</label>
        <input id="zoneInput" class="form-control" placeholder="Zone1, Zone2">
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveZones">Assign Zones</button>
      </div>
    </div>
  </div>
</div>

<!-- ASSIGN ORDERS MODAL -->
<div class="modal fade" id="assignOrderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-box2-heart text-warning me-2"></i>Assign Orders</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Order IDs (comma separated)</label>
        <input id="orderInput" class="form-control" placeholder="1021,1022">
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveOrders">Assign Orders</button>
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
function toggleSidebar(){ document.getElementById('sidebar').classList.toggle('show'); }
function toggleSubmenu(el){ el.parentElement.classList.toggle('open'); }

document.getElementById('selectAllDp')?.addEventListener('change', function(){ const checked=this.checked; document.querySelectorAll('.dp-checkbox').forEach(cb=>cb.checked=checked); });
document.getElementById('dpSearch')?.addEventListener('input', function(){ const q=this.value.toLowerCase(); Array.from(document.querySelectorAll('#dpBody tr')).forEach(r=>{ r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; }); const visible = Array.from(document.querySelectorAll('#dpBody tr')).filter(r=>r.style.display!=='none').length; document.getElementById('dpCount').textContent = visible; document.getElementById('noDp').style.display = visible? 'none':''; });

// helper to build a row HTML from partner object
function buildRow(p){
  const img = p.avatar_path ? ('/storage/'+p.avatar_path) : 'https://i.pravatar.cc/45';
  const statusHtml = (p.status==='Active') ? '<span class="status-dot status-active"></span>Active' : '<span class="status-dot status-inactive"></span>Inactive';
  return `<tr data-id="${p.id}">\n            <td><input type="checkbox" class="dp-checkbox" value="${p.id}"></td>\n            <td class="d-flex align-items-center gap-3"><img src="${img}" class="partner-img"><div><div class="fw-semibold">${escapeHtml(p.name||'')}</div><small class="text-muted">${escapeHtml(p.email||'')}</small></div></td>\n            <td class="dp-phone">${escapeHtml(p.phone||'')}</td>\n            <td class="dp-vehicle">${escapeHtml(p.vehicle||'')}</td>\n            <td class="dp-status">${statusHtml}</td>\n            <td class="text-end">\n              <button class="btn btn-light btn-sm dp-edit" data-id="${p.id}"><i class="bi bi-pencil text-primary"></i></button>\n              <button class="btn btn-light btn-sm ms-1 dp-assign-zone" data-id="${p.id}"><i class="bi bi-geo-alt text-success"></i></button>\n              <button class="btn btn-light btn-sm ms-1 dp-assign-order" data-id="${p.id}"><i class="bi bi-box2-heart text-warning"></i></button>\n              <button class="btn btn-light btn-sm ms-1 dp-delete" data-id="${p.id}"><i class="bi bi-trash text-danger"></i></button>\n            </td>\n          </tr>`;
}

function escapeHtml(str){ return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

function updateCount(){ const visible = Array.from(document.querySelectorAll('#dpBody tr')).filter(r=>r.style.display!=='none').length; document.getElementById('dpCount').textContent = visible; document.getElementById('noDp').style.display = visible? 'none':''; }

// prepare add modal
document.querySelector('[data-bs-target="#addPartnerModal"]')?.addEventListener('click', ()=>{
  document.getElementById('modalTitle').textContent = 'Add Delivery Partner';
  document.getElementById('partnerForm').action = '{{ route('delivery.person.store') }}';
  document.getElementById('partnerId').value=''; document.getElementById('partnerName').value=''; document.getElementById('partnerPhone').value=''; document.getElementById('partnerEmail').value=''; document.getElementById('partnerVehicle').value=''; document.getElementById('partnerStatus').value='Active';
});

let activeAssignId = null;

// delegated handlers for edit, assign and delete so newly added rows work too
document.addEventListener('click', async function(e){
  const editBtn = e.target.closest('.dp-edit');
  if(editBtn){
    const id = editBtn.dataset.id;
    const res = await fetch(`{{ url('delivery-person') }}/${id}/details`);
    if(!res.ok) return;
    const data = await res.json();
    document.getElementById('modalTitle').textContent = 'Edit Delivery Partner';
    document.getElementById('partnerForm').action = '{{ route('delivery.person.update') }}';
    document.getElementById('partnerId').value=data.id;
    document.getElementById('partnerName').value=data.name;
    document.getElementById('partnerPhone').value=data.phone;
    document.getElementById('partnerEmail').value=data.email;
    document.getElementById('partnerVehicle').value=data.vehicle;
    document.getElementById('partnerStatus').value=data.status || 'Active';
    new bootstrap.Modal(document.getElementById('addPartnerModal')).show();
    return;
  }

  const az = e.target.closest('.dp-assign-zone');
  if(az){ activeAssignId = az.dataset.id; document.getElementById('zoneInput').value=''; new bootstrap.Modal(document.getElementById('assignZoneModal')).show(); return; }

  const ao = e.target.closest('.dp-assign-order');
  if(ao){ activeAssignId = ao.dataset.id; document.getElementById('orderInput').value=''; new bootstrap.Modal(document.getElementById('assignOrderModal')).show(); return; }

  const del = e.target.closest('.dp-delete');
  if(del){ if(!confirm('Delete?')) return; try{ await postJson('{{ route('delivery.person.delete') }}',{id: del.dataset.id}); const tr = document.querySelector(`#dpBody tr[data-id="${del.dataset.id}"]`); if(tr) tr.remove(); updateCount(); showToast('Deleted','success'); }catch(err){ console.error(err); showToast('Delete failed','danger'); } return; }
});

async function postJson(url, body){ const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); const res = await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},body:JSON.stringify(body)}); if(!res.ok) throw new Error('Request failed'); return res.json().catch(()=>null); }

document.getElementById('saveZones')?.addEventListener('click', async ()=>{
  try{ const raw = document.getElementById('zoneInput').value.trim(); const zones = raw? raw.split(',').map(s=>s.trim()).filter(Boolean):[]; await postJson('{{ route('delivery.person.assignZones') }}',{id: activeAssignId, zones}); showToast('Zones assigned','success'); new bootstrap.Modal(document.getElementById('assignZoneModal')).hide(); }catch(e){ console.error(e); showToast('Failed to assign','danger'); }
});

document.getElementById('saveOrders')?.addEventListener('click', async ()=>{
  try{ const raw = document.getElementById('orderInput').value.trim(); const orders = raw? raw.split(',').map(s=>s.trim()).filter(Boolean):[]; await postJson('{{ route('delivery.person.assignOrders') }}',{id: activeAssignId, orders}); showToast('Orders assigned','success'); new bootstrap.Modal(document.getElementById('assignOrderModal')).hide(); }catch(e){ console.error(e); showToast('Failed to assign','danger'); }
});

function showToast(msg, level='info'){ const container = document.getElementById('dpToast'); const div = document.createElement('div'); div.className = 'toast '+(level==='success'?'bg-success text-white':''); div.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`; container.appendChild(div); const t = new bootstrap.Toast(div); t.show(); div.addEventListener('hidden.bs.toast', ()=>div.remove()); }

// AJAX create/update form handling
document.getElementById('partnerForm')?.addEventListener('submit', async function(e){
  e.preventDefault(); const form = this; const url = form.action; const fd = new FormData(form); const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  try{
    const res = await fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN': token,'Accept':'application/json'}, body: fd});
    if(!res.ok){ showToast('Request failed','danger'); return; }
    const json = await res.json().catch(()=>null);
    if(json && json.ok){ const p = json.data; const isUpdate = document.getElementById('partnerId').value;
      if(isUpdate){ // update existing
          const tr = document.querySelector(`#dpBody tr[data-id="${p.id}"]`);
          if(tr){ tr.querySelector('.fw-semibold').textContent = p.name || ''; tr.querySelector('small.text-muted').textContent = p.email || ''; tr.querySelector('.dp-phone').textContent = p.phone || ''; tr.querySelector('.dp-vehicle').textContent = p.vehicle || ''; tr.querySelector('.dp-status').innerHTML = (p.status==='Active')? '<span class="status-dot status-active"></span>Active' : '<span class="status-dot status-inactive"></span>Inactive'; }
          showToast('Updated','success');
        } else { // new row
          const wrapper = document.createElement('tbody'); wrapper.innerHTML = buildRow(p); const newRow = wrapper.firstChild; document.getElementById('dpBody').appendChild(newRow); showToast('Added','success'); }
      updateCount(); new bootstrap.Modal(document.getElementById('addPartnerModal')).hide(); form.reset(); document.getElementById('partnerId').value='';
    } else {
      form.submit();
    }
  }catch(err){ console.error(err); showToast('Save failed','danger'); }
});
</script>
<div id="dpToast" class="position-fixed bottom-0 end-0 p-3" style="z-index:2000"></div>
</body>
</html>
