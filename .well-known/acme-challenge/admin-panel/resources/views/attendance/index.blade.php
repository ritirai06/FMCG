
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Attendance Management | FMCG Admin</title>
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
.content { margin-left: 250px; padding: 30px; }

/* HEADER */
.topbar {
  background: var(--glass);
  border-radius: 20px;
  padding: 18px 25px;
  display: flex; align-items: center; justify-content: space-between;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  backdrop-filter: blur(12px);
}
.topbar h5 { font-weight: 700; margin: 0; }

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
  font-weight: 600;
  box-shadow: 0 3px 10px rgba(99,102,241,0.3);
}

/* TABLE CARD */
.table-card {
  background: var(--glass);
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
  padding: 25px;
  backdrop-filter: blur(12px);
  margin-top: 25px;
}
.table tbody tr:hover { background: rgba(99,102,241,0.08); transition: 0.3s; transform: scale(1.01); }

/* STATUS DOTS */
.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.status-present { background: #22c55e; }
.status-absent { background: #ef4444; }
.status-late { background: #f59e0b; }

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
    <i class="bi bi-box-seam"></i>
    <span>Admin Panel</span>
  </div>

  <div class="menu">
   
    <a href="dashboard.html" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="products.html"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="product/add_product.html"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="product/product_status.html"><i class="bi bi-toggle-on"></i> Product Status</a>
        <a href="product/product_margin.html"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
      </div>
    </div>
 
  <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="brands.html"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="brands/add_brand.html"><i class="bi bi-plus-circle"></i> Add Brands</a>
        <a href="brands/brads_status.html"><i class="bi bi-toggle-on"></i>Brands Status </a>
     
      </div>
    </div>
     <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="categories.html"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="category/assign-products.html"><i class="bi bi-plus-circle"></i> Assign Product</a>
        <a href="category/sub_cate.html"><i class="bi bi-toggle-on"></i>Sub Categories </a>
     
      </div>
    </div>
	 <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="warehouses.html"><i class="bi bi-list-ul"></i> Warehouses List</a>
        <a href="warehouse/status_warehouse.html"><i class="bi bi-plus-circle"></i> Status Warehouses</a>
       
     
      </div>
    </div>
   
    <a href="inventory.html"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="territory.html"><i class="bi bi-geo-alt"></i><span>Cities & Localities</span></a>
    <a href="salary_management.html"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="delivery_partners.html"><i class="bi bi-truck"></i><span>Delivery Partners</span></a>
    <a href="attendance_management.html"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="salary_management.html"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="store_management.html"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="order_management.html"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="reports_analytics.html"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="admin_settings"><i class="bi bi-gear"></i><span>Settings</span></a>
  </div>
</div>



<!-- CONTENT -->
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-calendar3 text-primary me-2"></i>Attendance Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manualModal">
      <i class="bi bi-pencil-square me-1"></i> Manual Override
    </button>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="attendanceTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#daily" role="tab">Daily Attendance</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#monthly" role="tab">Monthly Attendance</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#auto" role="tab">Auto Attendance View</a></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- DAILY ATTENDANCE -->
    <div class="tab-pane fade show active" id="daily" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Today: 09 Jan 2026</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="attendanceTbody">
              @php $todayStr = $today ?? now()->toDateString(); @endphp
              @forelse($attendances->where('date', $todayStr) as $a)
                <tr data-id="{{ $a->id }}">
                  <td class="emp-name">{{ $a->employee_name }}</td>
                  <td class="time-in">{{ $a->time_in ?: '--' }}</td>
                  <td class="time-out">{{ $a->time_out ?: '--' }}</td>
                  <td class="status-cell"><span class="status-dot {{ $a->status=='Present' ? 'status-present' : ($a->status=='Late' ? 'status-late' : 'status-absent') }}"></span>{{ $a->status }}</td>
                  <td class="text-end">
                    <button class="btn btn-light btn-sm edit-att" data-id="{{ $a->id }}"><i class="bi bi-pencil text-primary"></i></button>
                    <button class="btn btn-light btn-sm delete-att" data-id="{{ $a->id }}"><i class="bi bi-trash text-danger"></i></button>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">No attendance for today.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MONTHLY ATTENDANCE -->
    <div class="tab-pane fade" id="monthly" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Monthly Summary (January 2026)</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Attendance %</th>
              </tr>
            </thead>
            <tbody>
              @if(isset($monthly) && $monthly->count())
                @foreach($monthly as $m)
                  <tr>
                    <td>{{ $m->employee_name }}</td>
                    <td>{{ $m->present }}</td>
                    <td>{{ $m->absent }}</td>
                    <td>{{ $m->late }}</td>
                    <td>{{ intval((($m->present) / max(1, ($m->present + $m->absent))) * 100) }}%</td>
                  </tr>
                @endforeach
              @else
                <tr><td colspan="5" class="text-center text-muted">No monthly data.</td></tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- AUTO ATTENDANCE -->
    <div class="tab-pane fade" id="auto" role="tabpanel">
      <div class="table-card text-center p-5">
        <i class="bi bi-gear-wide-connected text-primary display-5 mb-3"></i>
        <h5 class="fw-semibold">Auto Attendance System Active</h5>
        <p class="text-muted mb-2">Employee attendance is automatically recorded through GPS and biometric integration.</p>
        <button class="btn btn-primary btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Refresh View</button>
      </div>
    </div>
  </div>
</div>

<!-- MANUAL OVERRIDE MODAL -->
<div class="modal fade" id="manualModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <form id="attendanceForm">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square text-primary me-2"></i>Manual Override</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_id" name="edit_id" value="">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Employee</label><input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Enter name" required></div>
          <div class="col-md-6"><label class="form-label">Date</label><input type="date" name="date" id="date" class="form-control" value="{{ $today ?? now()->toDateString() }}" required></div>
          <div class="col-md-6"><label class="form-label">Time In</label>
            <div class="input-group">
              <input type="text" name="time_in" id="time_in" class="form-control" placeholder="07:30">
              <select id="time_in_ampm" class="form-select"><option>AM</option><option>PM</option></select>
            </div>
          </div>
          <div class="col-md-6"><label class="form-label">Time Out</label>
            <div class="input-group">
              <input type="text" name="time_out" id="time_out" class="form-control" placeholder="05:30">
              <select id="time_out_ampm" class="form-select"><option>AM</option><option>PM</option></select>
            </div>
          </div>
          <div class="col-md-12"><label class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
              <option value="Present">Present</option>
              <option value="Absent">Absent</option>
              <option value="Late">Late</option>
            </select>
          </div>
          <div class="col-md-12"><label class="form-label">Notes</label><textarea name="notes" id="notes" class="form-control" rows="2"></textarea></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Override</button>
      </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const _csrfMeta = document.querySelector('meta[name="csrf-token"]');
const csrf = _csrfMeta ? _csrfMeta.getAttribute('content') : '';

async function postForm(url, formData){
  const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }, body: formData });
  try{ return await res.json(); }catch(e){ return { ok: res.ok }; }
}

async function delReq(url){
  const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
  try{ return await res.json(); }catch(e){ return { ok: res.ok }; }
}

function buildRow(a){
  const fmt = time24To12(a.time_in);
  const fmtOut = time24To12(a.time_out);
  return `<tr data-id="${a.id}">
    <td class="emp-name">${a.employee_name}</td>
    <td class="time-in">${fmt.display}</td>
    <td class="time-out">${fmtOut.display}</td>
    <td class="status-cell"><span class="status-dot ${a.status=='Present'?'status-present':(a.status=='Late'?'status-late':'status-absent')}"></span>${a.status}</td>
    <td class="text-end">
      <button class="btn btn-light btn-sm edit-att" data-id="${a.id}"><i class="bi bi-pencil text-primary"></i></button>
      <button class="btn btn-light btn-sm delete-att" data-id="${a.id}"><i class="bi bi-trash text-danger"></i></button>
    </td>
  </tr>`;
}

// Helpers: convert 24h HH:MM to 12h display and parse 12h->24h
function time24To12(v){
  if(!v) return {hour:null, minute:null, ampm:'', display:'--'};
  const parts = v.split(':');
  let h = parseInt(parts[0],10); const m = parts[1]||'00';
  const ampm = h>=12? 'PM':'AM';
  let hr12 = h%12; if(hr12===0) hr12 = 12;
  const display = `${String(hr12).padStart(2,'0')}:${m} ${ampm}`;
  return {hour:hr12, minute:m, ampm, display};
}

function time12To24(time12, ampm){
  if(!time12) return '';
  const parts = time12.split(':');
  let h = parseInt(parts[0].trim(),10); let m = (parts[1]||'00').trim();
  if(isNaN(h)) return '';
  if(ampm==='AM'){
    if(h===12) h = 0;
  } else {
    if(h<12) h = h + 12;
  }
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}

function showToast(msg, ok=true){
  const t = document.createElement('div'); t.innerHTML = `<div class="toast align-items-center text-bg-${ok?'success':'danger'} border-0 show" role="alert"><div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
  document.getElementById('attToast').appendChild(t);
  setTimeout(()=>t.remove(),3500);
}

document.getElementById('attendanceForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  // Convert 12h inputs to 24h for backend
  const tin = document.getElementById('time_in').value.trim();
  const tinAmp = document.getElementById('time_in_ampm') ? document.getElementById('time_in_ampm').value : '';
  const tout = document.getElementById('time_out').value.trim();
  const toutAmp = document.getElementById('time_out_ampm') ? document.getElementById('time_out_ampm').value : '';
  if(tin) fd.set('time_in', time12To24(tin, tinAmp));
  else fd.set('time_in', '');
  if(tout) fd.set('time_out', time12To24(tout, toutAmp));
  else fd.set('time_out', '');
  const editId = document.getElementById('edit_id').value;
  let url = '/attendance/store';
  if(editId) url = '/attendance/update/' + editId;
  const res = await postForm(url, fd);
  if(res && res.ok){
    const data = res.data;
    if(editId){ const tr = document.querySelector('tr[data-id="'+editId+'"]'); if(tr) tr.outerHTML = buildRow(data); showToast('Updated'); }
    else { const tbody = document.getElementById('attendanceTbody'); tbody.insertAdjacentHTML('afterbegin', buildRow(data)); showToast('Added'); }
    this.reset(); document.getElementById('edit_id').value = '';
    const modal = bootstrap.Modal.getInstance(document.getElementById('manualModal'));
    if(modal) modal.hide();
  } else {
    // show validation errors if present
    if(res && res.errors){
      const first = Object.values(res.errors)[0];
      showToast(first[0] || 'Validation failed', false);
    } else {
      showToast('Save failed', false);
    }
  }
});

document.addEventListener('click', async function(e){
  const ed = e.target.closest('.edit-att');
  const del = e.target.closest('.delete-att');
  if(ed){
    const id = ed.getAttribute('data-id');
    try{
      const resp = await fetch('/attendance/edit/' + id, { headers: { 'Accept':'application/json' } });
      const rec = await resp.json();
      document.getElementById('edit_id').value = rec.id;
      document.getElementById('employee_name').value = rec.employee_name || '';
      document.getElementById('date').value = rec.date || '{{ $today ?? now()->toDateString() }}';
      // populate time fields: convert stored 24h to 12h inputs + AM/PM
      const tin = time24To12(rec.time_in);
      const tout = time24To12(rec.time_out);
      document.getElementById('time_in').value = tin.hour ? `${String(tin.hour).padStart(2,'0')}:${tin.minute}` : '';
      if(document.getElementById('time_in_ampm')) document.getElementById('time_in_ampm').value = tin.ampm || 'AM';
      document.getElementById('time_out').value = tout.hour ? `${String(tout.hour).padStart(2,'0')}:${tout.minute}` : '';
      if(document.getElementById('time_out_ampm')) document.getElementById('time_out_ampm').value = tout.ampm || 'AM';
      document.getElementById('status').value = rec.status || 'Present';
      document.getElementById('notes').value = rec.notes || '';
      const modal = new bootstrap.Modal(document.getElementById('manualModal'));
      modal.show();
    }catch(err){ console.error(err); showToast('Load failed', false); }
  }
  if(del){
    const id = del.getAttribute('data-id'); if(!confirm('Delete attendance?')) return;
    const resp = await delReq('/attendance/delete/' + id);
    if(resp && resp.ok){ const tr = document.querySelector('tr[data-id="'+id+'"]'); if(tr) tr.remove(); showToast('Deleted'); } else showToast('Delete failed', false);
  }
});

// Fetch and render monthly data
async function fetchMonthly(){
  try{
    const res = await fetch('/attendance/monthly-data', { headers: { 'Accept':'application/json' } });
    const j = await res.json();
    if(j && j.ok){
      const tbody = document.querySelector('#monthly table tbody');
      tbody.innerHTML = '';
      if(j.data.length){
        j.data.forEach(m => {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${m.employee_name}</td><td>${m.present}</td><td>${m.absent}</td><td>${m.late}</td><td>${m.attendance_percent}%</td>`;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No monthly data.</td></tr>';
      }
    }
  }catch(err){ console.error(err); showToast('Failed to load monthly data', false); }
}

// Fetch and render auto attendance list
async function fetchAuto(){
  try{
    const res = await fetch('/attendance/auto-data', { headers: { 'Accept':'application/json' } });
    const j = await res.json();
    if(j && j.ok){
      const container = document.querySelector('#auto .table-card');
      // replace content with table of recent auto records
      let html = '<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>Employee</th><th>Time In</th><th>Time Out</th><th>Status</th></tr></thead><tbody>';
      if(j.data.length){
        j.data.forEach(a => {
          html += `<tr><td>${a.date}</td><td>${a.employee_name}</td><td>${a.time_in||'--'}</td><td>${a.time_out||'--'}</td><td>${a.status}</td></tr>`;
        });
      } else html += '<tr><td colspan="5" class="text-center text-muted">No auto records.</td></tr>';
      html += '</tbody></table></div>';
      container.innerHTML = html;
    }
  }catch(err){ console.error(err); showToast('Failed to load auto data', false); }
}

// Hook tab shown events to lazy-load data
document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(a => {
  a.addEventListener('shown.bs.tab', function(e){
    const target = e.target.getAttribute('href');
    if(target === '#monthly') fetchMonthly();
    if(target === '#auto') fetchAuto();
  });
});
</script>
<div id="attToast" class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;"></div>
</body>
</html>
