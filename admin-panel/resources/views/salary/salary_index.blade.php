@extends('layouts.app')

@section('title', 'Salary & Incentive Management')

@push('styles')
<style>
/* Page-specific styles */
.nav-tabs {
  background: var(--panel, #fff);
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

.table-card {
  background: var(--panel, #fff);
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
</style>
@endpush

@section('content')
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
        <h6 class="fw-bold mb-3">Salesperson Salary Overview</h6>
        <div class="table-responsive">
          <table class="table align-middle table-hover">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Role</th>
                <th>City</th>
                <th>Base Salary</th>
                <th>Allowances</th>
                <th>Bonus</th>
                <th>Incentive</th>
                <th>Total Salary</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="salaryTbody">
              @forelse($salesPeople as $sp)
              <tr>
                <td>
                  <a href="{{ route('salary.show', $sp->id) }}" class="fw-semibold text-primary text-decoration-none">
                    <i class="bi bi-person-circle me-1"></i>{{ $sp->name }}
                  </a>
                </td>
                <td>{{ $sp->role ?? 'Executive' }}</td>
                <td>{{ optional($sp->city)->name ?? '-' }}</td>
                <td>₹{{ number_format($sp->base_salary ?? 0, 0) }}</td>
                <td>₹{{ number_format($sp->allowance ?? 0, 0) }}</td>
                <td>₹{{ number_format($sp->calculated_bonus ?? 0, 0) }}</td>
                <td><span class="badge bg-success">₹{{ number_format($sp->calculated_incentive ?? 0, 0) }}</span></td>
                <td><strong>₹{{ number_format($sp->total_salary ?? 0, 0) }}</strong></td>
                <td>
                  <a href="{{ route('salary.show', $sp->id) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-eye text-info"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr><td colspan="9" class="text-center text-muted">No salespersons found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- INCENTIVE SLABS -->
    <div class="tab-pane fade" id="incentive" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Incentive Slabs</h6>
        <table class="table align-middle">
          <thead><tr><th>Target Range</th><th>Incentive %</th><th>Effective From</th><th class="text-end">Actions</th></tr></thead>
          <tbody id="slabsTbody">
            <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
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
        <p class="text-muted">Enter monthly sales for each employee and click <strong>Calculate</strong>.</p>
        <div class="table-responsive">
          <table class="table align-middle" id="calcTable">
            <thead><tr><th>Employee</th><th>Base</th><th>Allowances</th><th>Sales (₹)</th><th>Incentive (₹)</th><th>Total Payout (₹)</th></tr></thead>
            <tbody id="calcTbody"><tr><td colspan="6" class="text-center text-muted">Enter sales and click Recalculate</td></tr></tbody>
          </table>
        </div>
        <div class="d-flex gap-2 justify-content-end">
          <button type="button" class="btn btn-outline-secondary" id="calcRefresh">Refresh Employees</button>
          <button type="button" class="btn btn-primary" id="btnCalculate">Recalculate</button>
          <button type="button" class="btn btn-success" id="btnSavePayouts">Save Payouts</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODALS -->
  <div class="modal fade" id="addSlabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <form id="addSlabForm">
        <div class="modal-header"><h5 class="modal-title">Add Incentive Slab</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Min Amount</label><input type="number" step="0.01" name="min_amount" id="sl_min" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Max Amount</label><input type="number" step="0.01" name="max_amount" id="sl_max" class="form-control" placeholder="Above"></div>
            <div class="col-md-6"><label class="form-label">Percent</label><input type="number" step="0.01" name="percent" id="sl_percent" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Effective From</label><input type="month" name="effective_from" id="sl_from" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editSlabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <form id="editSlabForm">
        <input type="hidden" id="edit_slab_id">
        <div class="modal-header"><h5 class="modal-title">Edit Incentive Slab</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Min Amount</label><input type="number" step="0.01" name="min_amount" id="edit_sl_min" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Max Amount</label><input type="number" step="0.01" name="max_amount" id="edit_sl_max" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Percent</label><input type="number" step="0.01" name="percent" id="edit_sl_percent" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Effective From</label><input type="month" name="effective_from" id="edit_sl_from" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Update</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Add Salary</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="addSalaryForm">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Employee</label><input type="text" name="employee_name" id="add_employee_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" id="add_role" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Base Salary</label><input type="number" name="base_salary" id="add_base_salary" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Allowances</label><input type="number" name="allowances" id="add_allowances" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editSalaryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Edit Salary</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="editSalaryForm">
        <input type="hidden" id="edit_salary_id" name="id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Employee</label><input type="text" name="employee_name" id="edit_employee_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" id="edit_role" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Base Salary</label><input type="number" name="base_salary" id="edit_base_salary" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Allowances</label><input type="number" name="allowances" id="edit_allowances" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Update</button></div>
        </form>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;"></div>
@endsection

@push('scripts')
<script>
const _csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';

async function postJson(url, data){
  const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify(data) });
  let j = null; try{ j = await res.json(); }catch(e){}
  if(res.ok) return j || { ok:true };
  return { ok:false, errors: j?.errors || null };
}

async function delReq(url){
  const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
  let j=null; try{ j = await res.json(); }catch(e){}
  return j || { ok: res.ok };
}

function showToast(msg, ok=true){
  const container = document.getElementById('toastContainer');
  const t = document.createElement('div');
  t.className = `toast align-items-center text-bg-${ok?'success':'danger'} border-0 show mb-2`;
  t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
  container.appendChild(t);
  setTimeout(()=> { t.remove(); }, 3000);
}

function rowHtml(s){
  const total = (parseFloat(s.base_salary||0)+parseFloat(s.allowances||0)).toFixed(2);
  return `<tr data-id="${s.id}"><td>${s.employee_name}</td><td>${s.role||''}</td><td>₹${Number(s.base_salary||0).toLocaleString()}</td><td>₹${Number(s.allowances||0).toLocaleString()}</td><td>₹${Number(total).toLocaleString()}</td><td class="text-end"><button class="btn btn-light btn-sm edit-salary" data-id="${s.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-light btn-sm delete-salary" data-id="${s.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`;
}

async function loadSlabs(){
  try{
    const res = await fetch('/salary/slabs/list', { headers: { 'Accept':'application/json' } });
    const j = await res.json();
    const tbody = document.getElementById('slabsTbody'); tbody.innerHTML = '';
    if(j && j.ok && j.data.length){
      j.data.forEach(sl => {
        const maxLabel = sl.max_amount ? '₹'+Number(sl.max_amount).toLocaleString() : 'Above';
        const eff = sl.effective_from ? new Date(sl.effective_from).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : '-';
        tbody.insertAdjacentHTML('beforeend', `<tr data-id="${sl.id}"><td>₹${Number(sl.min_amount).toLocaleString()} - ${maxLabel}</td><td>${sl.percent}%</td><td>${eff}</td><td class="text-end"><button class="btn btn-sm btn-light edit-slab" data-id="${sl.id}"><i class="bi bi-pencil text-primary"></i></button> <button class="btn btn-sm btn-light delete-slab" data-id="${sl.id}"><i class="bi bi-trash text-danger"></i></button></td></tr>`);
      });
    } else { tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No slabs defined.</td></tr>'; }
  }catch(err){ console.error(err); }
}

document.addEventListener('click', async function(e){
  const eds = e.target.closest('.edit-slab');
  const dels = e.target.closest('.delete-slab');
  const ed = e.target.closest('.edit-salary');
  const del = e.target.closest('.delete-salary');

  if(eds){
    const id = eds.getAttribute('data-id');
    const r = await fetch('/salary/slabs/list', { headers: { 'Accept':'application/json' } }); 
    const j = await r.json(); const sl = j.data.find(x=>x.id==id);
    document.getElementById('edit_slab_id').value = sl.id;
    document.getElementById('edit_sl_min').value = sl.min_amount;
    document.getElementById('edit_sl_max').value = sl.max_amount||'';
    document.getElementById('edit_sl_percent').value = sl.percent;
    document.getElementById('edit_sl_from').value = sl.effective_from? sl.effective_from.substring(0,7):'';
    new bootstrap.Modal(document.getElementById('editSlabModal')).show();
  }
  if(dels){
    const id = dels.getAttribute('data-id'); if(!confirm('Delete slab?')) return;
    const r = await delReq('/salary/slabs/delete/'+id);
    if(r && r.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); }
  }
  if(ed){
    const id = ed.getAttribute('data-id');
    const r = await fetch('/salary/edit/'+id,{headers:{'Accept':'application/json'}}); const s = await r.json();
    document.getElementById('edit_salary_id').value = s.id;
    document.getElementById('edit_employee_name').value = s.employee_name;
    document.getElementById('edit_role').value = s.role || '';
    document.getElementById('edit_base_salary').value = s.base_salary || 0;
    document.getElementById('edit_allowances').value = s.allowances || 0;
    new bootstrap.Modal(document.getElementById('editSalaryModal')).show();
  }
  if(del){
    const id = del.getAttribute('data-id'); if(!confirm('Delete salary?')) return;
    const resp = await delReq('/salary/delete/'+id);
    if(resp && resp.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); }
  }
});

document.getElementById('addSlabForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const data = { min_amount: document.getElementById('sl_min').value, max_amount: document.getElementById('sl_max').value || null, percent: document.getElementById('sl_percent').value, effective_from: document.getElementById('sl_from').value ? document.getElementById('sl_from').value+'-01' : null };
  const res = await postJson('/salary/slabs/store', data);
  if(res && res.ok){ loadSlabs(); showToast('Slab added'); bootstrap.Modal.getInstance(document.getElementById('addSlabModal')).hide(); }
});

document.getElementById('editSlabForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('edit_slab_id').value;
  const data = { min_amount: document.getElementById('edit_sl_min').value, max_amount: document.getElementById('edit_sl_max').value || null, percent: document.getElementById('edit_sl_percent').value, effective_from: document.getElementById('edit_sl_from').value ? document.getElementById('edit_sl_from').value+'-01' : null };
  const res = await postJson('/salary/slabs/update/'+id, data);
  if(res && res.ok){ loadSlabs(); showToast('Slab updated'); bootstrap.Modal.getInstance(document.getElementById('editSlabModal')).hide(); }
});

document.getElementById('addSalaryForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const res = await postJson('/salary/store', Object.fromEntries(fd));
  if(res && res.ok){ location.reload(); }
});

document.getElementById('editSalaryForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('edit_salary_id').value;
  const fd = new FormData(this);
  const res = await postJson('/salary/update/'+id, Object.fromEntries(fd));
  if(res && res.ok){ location.reload(); }
});

// Incentive Recalculation
document.getElementById('btnCalculate')?.addEventListener('click', async function(){
  const sales = {};
  document.querySelectorAll('#calcTbody tr').forEach(r => {
    const emp = r.getAttribute('data-emp');
    const val = r.querySelector('.calc-sales')?.value;
    if(emp) sales[emp] = parseFloat(val||0);
  });
  const resp = await postJson('/salary/monthly-summary', { sales });
  if(resp && resp.ok){
    const tbody = document.getElementById('calcTbody'); tbody.innerHTML = '';
    resp.data.forEach(d => {
      tbody.insertAdjacentHTML('beforeend', `<tr data-emp="${d.employee_name}"><td>${d.employee_name}</td><td>₹${Number(d.base_salary||0).toLocaleString()}</td><td>₹${Number(d.allowances||0).toLocaleString()}</td><td><input type="number" class="form-control form-control-sm calc-sales" value="${d.sales||0}" style="width:140px" /></td><td class="calc-incentive">₹${Number(d.incentive||0).toLocaleString()}</td><td class="calc-total">₹${Number(d.total_payout||0).toLocaleString()}</td></tr>`);
    });
    showToast('Recalculated');
  }
});

document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(a=>a.addEventListener('shown.bs.tab', e=>{
  const target = e.target.getAttribute('href');
  if(target==='#incentive') loadSlabs();
}));
</script>
@endpush
