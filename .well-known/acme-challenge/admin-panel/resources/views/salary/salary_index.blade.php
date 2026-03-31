
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Salary & Incentive Management | FMCG Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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
    <h5><i class="bi bi-cash-stack text-primary me-2"></i>Salary & Incentive Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
      <i class="bi bi-plus-circle me-1"></i> Add Salary
    </button>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="salaryTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#fixed" role="tab">Fixed Salary Setup</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#incentive" role="tab">Incentive Slabs</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#monthly" role="tab">Monthly Salary Summary</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#calc" role="tab">Incentive Calculation</a></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- FIXED SALARY -->
    <div class="tab-pane fade show active" id="fixed" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Employee Salary Setup</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Employee</th><th>Role</th><th>Base Salary</th><th>Allowances</th><th>Total</th><th>Actions</th></tr></thead>
            <tbody id="salaryTbody">
              <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

        <!-- ADD SLAB MODAL -->
        <div class="modal fade" id="addSlabModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-3">
              <form id="addSlabForm">
              <div class="modal-header"><h5 class="modal-title">Add Incentive Slab</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Min Amount</label><input type="number" step="0.01" name="min_amount" id="sl_min" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Max Amount (empty for Above)</label><input type="number" step="0.01" name="max_amount" id="sl_max" class="form-control"></div>
                  <div class="col-md-6"><label class="form-label">Percent</label><input type="number" step="0.01" name="percent" id="sl_percent" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Effective From</label><input type="month" name="effective_from" id="sl_from" class="form-control"></div>
                </div>
              </div>
              <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
              </form>
            </div>
          </div>
        </div>

        <!-- EDIT SLAB MODAL -->
        <div class="modal fade" id="editSlabModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-3">
              <form id="editSlabForm">
              <input type="hidden" id="edit_slab_id">
              <div class="modal-header"><h5 class="modal-title">Edit Incentive Slab</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Min Amount</label><input type="number" step="0.01" name="min_amount" id="edit_sl_min" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Max Amount (empty for Above)</label><input type="number" step="0.01" name="max_amount" id="edit_sl_max" class="form-control"></div>
                  <div class="col-md-6"><label class="form-label">Percent</label><input type="number" step="0.01" name="percent" id="edit_sl_percent" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Effective From</label><input type="month" name="effective_from" id="edit_sl_from" class="form-control"></div>
                </div>
              </div>
              <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Update</button></div>
              </form>
            </div>
          </div>
        </div>

    <!-- INCENTIVE SLABS -->
    <div class="tab-pane fade" id="incentive" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Incentive Slabs</h6>
        <table class="table align-middle">
          <thead><tr><th>Target Range</th><th>Incentive %</th><th>Effective From</th></tr></thead>
          <tbody id="slabsTbody">
            <tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>
          </tbody>
        </table>
        <div class="mt-3">
          <button class="btn btn-sm btn-primary" id="addSlabBtn" data-bs-toggle="modal" data-bs-target="#addSlabModal">Add Slab</button>
        </div>
      </div>
    </div>

    <!-- MONTHLY SUMMARY -->
    <div class="tab-pane fade" id="monthly" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Monthly Salary Summary</h6>
        <table class="table align-middle">
          <thead><tr><th>Employee</th><th>Month</th><th>Salary</th><th>Incentive</th><th>Total Payout</th></tr></thead>
          <tbody>
            <tr><td>Ravi Sharma</td><td>Jan 2026</td><td>₹22,000</td><td>₹2,000</td><td>₹24,000</td></tr>
            <tr><td>Priya Verma</td><td>Jan 2026</td><td>₹16,000</td><td>₹800</td><td>₹16,800</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- INCENTIVE CALCULATION -->
    <div class="tab-pane fade" id="calc" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Incentive Calculation</h6>
        <p class="text-muted">Enter monthly sales for each employee and click <strong>Calculate</strong>. You can save computed payouts to the database.</p>
        <div class="table-responsive">
          <table class="table align-middle" id="calcTable">
            <thead><tr><th>Employee</th><th>Base</th><th>Allowances</th><th>Sales (₹)</th><th>Incentive (₹)</th><th>Total Payout (₹)</th></tr></thead>
            <tbody id="calcTbody"><tr><td colspan="6" class="text-center text-muted">Enter sales and click Recalculate</td></tr></tbody>
          </table>
        </div>
        <div class="d-flex gap-2 justify-content-end">
          <button class="btn btn-outline-secondary" id="calcRefresh">Refresh Employees</button>
          <button class="btn btn-primary" id="btnCalculate">Recalculate</button>
          <button class="btn btn-success" id="btnSavePayouts">Save Payouts</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ADD SALARY MODAL -->
<div class="modal fade" id="addSalaryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Add Salary</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="addSalaryForm">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Employee</label><input type="text" name="employee_name" id="add_employee_name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" id="add_role" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Base Salary</label><input type="number" name="base_salary" id="add_base_salary" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Allowances</label><input type="number" name="allowances" id="add_allowances" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT SALARY MODAL -->
<div class="modal fade" id="editSalaryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Edit Salary</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="editSalaryForm">
      <input type="hidden" id="edit_salary_id" name="id" value="">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Employee</label><input type="text" name="employee_name" id="edit_employee_name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" id="edit_role" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Base Salary</label><input type="number" name="base_salary" id="edit_base_salary" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Allowances</label><input type="number" name="allowances" id="edit_allowances" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Update</button></div>
      </form>
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
const _csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';

async function postJson(url, data){
  const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify(data) });
  let j = null; try{ j = await res.json(); }catch(e){}
  if(res.ok) return j || { ok:true };
  if(res.status===422 && j) return j;
  return { ok:false, status: res.status, data: j };
}

async function delReq(url){
  const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
  let j=null; try{ j = await res.json(); }catch(e){}
  return j || { ok: res.ok };
}

function showToast(msg, ok=true){
  const t = document.createElement('div'); t.innerHTML = `<div class="toast align-items-center text-bg-${ok?'success':'danger'} border-0 show" role="alert"><div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
  document.body.appendChild(t);
  setTimeout(()=>t.remove(),3000);
}

function rowHtml(s){
  const total = (parseFloat(s.base_salary||0)+parseFloat(s.allowances||0)).toFixed(2);
  return `<tr data-id="${s.id}"><td>${s.employee_name}</td><td>${s.role||''}</td><td>₹${Number(s.base_salary||0).toLocaleString()}</td><td>₹${Number(s.allowances||0).toLocaleString()}</td><td>₹${Number(total).toLocaleString()}</td><td class="text-end"><button class="btn btn-light btn-sm edit-salary" data-id="${s.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-salary" data-id="${s.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}

async function loadSalaries(){
  try{
    const res = await fetch('/salary/list', { headers: { 'Accept':'application/json' } });
    const j = await res.json();
    const tbody = document.getElementById('salaryTbody');
    tbody.innerHTML = '';
    if(j && j.ok && j.data.length){
      j.data.forEach(s => tbody.insertAdjacentHTML('beforeend', rowHtml(s)));
    } else {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No records.</td></tr>';
    }
  }catch(err){ console.error(err); showToast('Failed to load salaries', false); }
}

// Slabs management
async function loadSlabs(){
  try{
    const res = await fetch('/salary/slabs/list', { headers: { 'Accept':'application/json' } });
    const j = await res.json();
    const tbody = document.getElementById('slabsTbody'); tbody.innerHTML = '';
    if(j && j.ok && j.data.length){
      j.data.forEach(sl => {
        const maxLabel = sl.max_amount ? Number(sl.max_amount).toLocaleString() : 'Above';
        const eff = sl.effective_from ? new Date(sl.effective_from).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : '-';
        tbody.insertAdjacentHTML('beforeend', `<tr data-id="${sl.id}"><td>₹${Number(sl.min_amount).toLocaleString()} - ${sl.max_amount?('₹'+Number(sl.max_amount).toLocaleString()):'Above'}</td><td>${sl.percent}%</td><td>${eff}</td><td class="text-end"><button class="btn btn-sm btn-light edit-slab" data-id="${sl.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-sm btn-light delete-slab" data-id="${sl.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`);
      });
    } else {
      tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No slabs defined.</td></tr>';
    }
  }catch(err){ console.error(err); showToast('Failed to load slabs', false); }
}

document.addEventListener('click', async function(e){
  const eds = e.target.closest('.edit-slab');
  const dels = e.target.closest('.delete-slab');
  if(eds){
    const id = eds.getAttribute('data-id');
    try{ const r = await fetch('/salary/slabs/list', { headers: { 'Accept':'application/json' } }); const j = await r.json(); const sl = j.data.find(x=>x.id==id);
      document.getElementById('edit_slab_id').value = sl.id;
      document.getElementById('edit_sl_min').value = sl.min_amount;
      document.getElementById('edit_sl_max').value = sl.max_amount||'';
      document.getElementById('edit_sl_percent').value = sl.percent;
      document.getElementById('edit_sl_from').value = sl.effective_from? sl.effective_from.substring(0,7):'';
      new bootstrap.Modal(document.getElementById('editSlabModal')).show();
    }catch(err){ console.error(err); showToast('Load slab failed', false); }
  }
  if(dels){ const id = dels.getAttribute('data-id'); if(!confirm('Delete slab?')) return; const r = await delReq('/salary/slabs/delete/'+id); if(r && r.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); } else showToast('Delete slab failed', false); }
});

document.getElementById('addSlabForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const data = { min_amount: document.getElementById('sl_min').value, max_amount: document.getElementById('sl_max').value || null, percent: document.getElementById('sl_percent').value, effective_from: document.getElementById('sl_from').value ? document.getElementById('sl_from').value+'-01' : null };
  const res = await postJson('/salary/slabs/store', data);
  if(res && res.ok){ loadSlabs(); showToast('Slab added'); const m = bootstrap.Modal.getInstance(document.getElementById('addSlabModal')); if(m) m.hide(); }
  else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false);
  else showToast('Add failed', false);
});

document.getElementById('editSlabForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('edit_slab_id').value;
  const data = { min_amount: document.getElementById('edit_sl_min').value, max_amount: document.getElementById('edit_sl_max').value || null, percent: document.getElementById('edit_sl_percent').value, effective_from: document.getElementById('edit_sl_from').value ? document.getElementById('edit_sl_from').value+'-01' : null };
  const res = await postJson('/salary/slabs/update/'+id, data);
  if(res && res.ok){ loadSlabs(); showToast('Slab updated'); const m = bootstrap.Modal.getInstance(document.getElementById('editSlabModal')); if(m) m.hide(); }
  else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false);
  else showToast('Update failed', false);
});

// ensure slabs load on tab show
document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(a=>a.addEventListener('shown.bs.tab', e=>{ if(e.target.getAttribute('href')==='#incentive') loadSlabs(); }));

// initial load of salaries (and slabs can load when tab clicked)
loadSalaries();

document.addEventListener('click', async function(e){
  const ed = e.target.closest('.edit-salary');
  const del = e.target.closest('.delete-salary');
  if(ed){
    const id = ed.getAttribute('data-id');
    try{ const r = await fetch('/salary/edit/'+id,{headers:{'Accept':'application/json'}}); const s = await r.json();
      document.getElementById('edit_salary_id').value = s.id;
      document.getElementById('edit_employee_name').value = s.employee_name;
      document.getElementById('edit_role').value = s.role || '';
      document.getElementById('edit_base_salary').value = s.base_salary || 0;
      document.getElementById('edit_allowances').value = s.allowances || 0;
      new bootstrap.Modal(document.getElementById('editSalaryModal')).show();
    }catch(err){ console.error(err); showToast('Load failed', false); }
  }
  if(del){
    const id = del.getAttribute('data-id'); if(!confirm('Delete salary?')) return;
    const resp = await delReq('/salary/delete/'+id);
    if(resp && resp.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); }
    else showToast('Delete failed', false);
  }
});

document.getElementById('addSalaryForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const data = {
    employee_name: document.getElementById('add_employee_name').value,
    role: document.getElementById('add_role').value,
    base_salary: document.getElementById('add_base_salary').value,
    allowances: document.getElementById('add_allowances').value
  };
  const res = await postJson('/salary/store', data);
  if(res && res.ok){ const tbody = document.getElementById('salaryTbody'); tbody.insertAdjacentHTML('afterbegin', rowHtml(res.data)); showToast('Added'); this.reset(); const modal = bootstrap.Modal.getInstance(document.getElementById('addSalaryModal')); if(modal) modal.hide(); }
  else if(res && res.errors){ showToast(Object.values(res.errors)[0][0], false); }
  else showToast('Save failed', false);
});

document.getElementById('editSalaryForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('edit_salary_id').value;
  const data = {
    employee_name: document.getElementById('edit_employee_name').value,
    role: document.getElementById('edit_role').value,
    base_salary: document.getElementById('edit_base_salary').value,
    allowances: document.getElementById('edit_allowances').value
  };
  const res = await postJson('/salary/update/'+id, data);
  if(res && res.ok){ const tr = document.querySelector('tr[data-id="'+id+'"]'); if(tr) tr.outerHTML = rowHtml(res.data); showToast('Updated'); const modal = bootstrap.Modal.getInstance(document.getElementById('editSalaryModal')); if(modal) modal.hide(); }
  else if(res && res.errors){ showToast(Object.values(res.errors)[0][0], false); }
  else showToast('Update failed', false);
});

// Monthly summary loader
async function loadMonthly(){
  try{
    const res = await fetch('/salary/monthly-summary', { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify({}) });
    const j = await res.json();
    if(j && j.ok){
      const tbody = document.querySelector('#monthly table tbody'); tbody.innerHTML = '';
      j.data.forEach(r => {
        tbody.insertAdjacentHTML('beforeend', `<tr><td>${r.employee_name}</td><td>${r.month}</td><td>₹${Number(r.base_salary + r.allowances).toLocaleString()}</td><td>₹${Number(r.incentive).toLocaleString()}</td><td>₹${Number(r.total_payout).toLocaleString()}</td></tr>`);
      });
    }
  }catch(err){ console.error(err); showToast('Failed to load monthly summary', false); }
}

// Hook tab load
document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(a=>a.addEventListener('shown.bs.tab', e=>{ const target = e.target.getAttribute('href'); if(target==='#fixed') loadSalaries(); if(target==='#monthly') loadMonthly(); }));

// initial load
loadSalaries();
</script>
<script>
// Calculation table helpers
function buildCalcRow(r){
  return `<tr data-emp="${r.employee_name}">`+
    `<td>${r.employee_name}</td>`+
    `<td>₹${Number(r.base_salary||0).toLocaleString()}</td>`+
    `<td>₹${Number(r.allowances||0).toLocaleString()}</td>`+
    `<td><input type="number" class="form-control form-control-sm calc-sales" value="${r.sales||0}" style="width:140px" /></td>`+
    `<td class="calc-incentive">₹${Number(r.incentive||0).toLocaleString()}</td>`+
    `<td class="calc-total">₹${Number(r.total_payout|| ( (r.base_salary||0)+(r.allowances||0)+ (r.incentive||0) ) ).toLocaleString()}</td>`+
  `</tr>`;
}

async function loadCalcEmployees(){
  try{
    const res = await fetch('/salary/list', { headers:{ 'Accept':'application/json' } });
    const j = await res.json();
    const tbody = document.getElementById('calcTbody'); tbody.innerHTML='';
    if(j && j.ok && j.data.length){
      j.data.forEach(s => tbody.insertAdjacentHTML('beforeend', buildCalcRow({ employee_name: s.employee_name, base_salary: s.base_salary, allowances: s.allowances, sales:0, incentive:0, total_payout: (parseFloat(s.base_salary||0)+parseFloat(s.allowances||0)) }))); 
    } else {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No employees.</td></tr>';
    }
  }catch(e){ console.error(e); showToast('Failed to load employees', false); }
}

// Recalculate incentives by sending sales map to server
document.getElementById('btnCalculate').addEventListener('click', async function(){
  const rows = Array.from(document.querySelectorAll('#calcTbody tr'));
  const sales = {};
  rows.forEach(r=>{
    const emp = r.getAttribute('data-emp');
    const inp = r.querySelector('.calc-sales');
    if(emp && inp) sales[emp] = parseFloat(inp.value||0);
  });
  try{
    const resp = await postJson('/salary/monthly-summary', { sales });
    if(resp && resp.ok){
      const data = resp.data;
      const tbody = document.getElementById('calcTbody'); tbody.innerHTML='';
      data.forEach(d => tbody.insertAdjacentHTML('beforeend', buildCalcRow(d)));
      showToast('Recalculated');
    } else {
      showToast('Recalculate failed', false);
    }
  }catch(err){ console.error(err); showToast('Recalculate failed', false); }
});

// Save payouts
document.getElementById('btnSavePayouts').addEventListener('click', async function(){
  const rows = Array.from(document.querySelectorAll('#calcTbody tr'));
  const payload = [];
  rows.forEach(r=>{
    const emp = r.getAttribute('data-emp');
    if(!emp) return;
    const sales = parseFloat(r.querySelector('.calc-sales')?.value||0);
    const incentiveText = r.querySelector('.calc-incentive')?.textContent.replace(/[^0-9.-]+/g,'')||'0';
    const totalText = r.querySelector('.calc-total')?.textContent.replace(/[^0-9.-]+/g,'')||'0';
    const baseText = r.children[1].textContent.replace(/[^0-9.-]+/g,'')||'0';
    const allowText = r.children[2].textContent.replace(/[^0-9.-]+/g,'')||'0';
    payload.push({ employee_name: emp, month: new Date().getMonth()+1, year: new Date().getFullYear(), base_salary: parseFloat(baseText), allowances: parseFloat(allowText), sales: sales, incentive: parseFloat(incentiveText), total_payout: parseFloat(totalText) });
  });
  if(!payload.length){ showToast('No rows to save', false); return; }
  try{
    const res = await postJson('/salary/payouts/store', { rows: payload });
    if(res && res.ok){ showToast('Payouts saved'); }
    else if(res && res.errors) showToast(Object.values(res.errors)[0][0], false);
    else showToast('Save failed', false);
  }catch(err){ console.error(err); showToast('Save failed', false); }
});

// Refresh employees button
document.getElementById('calcRefresh').addEventListener('click', function(){ loadCalcEmployees(); showToast('Employees refreshed'); });

// initial loads
loadSalaries();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
