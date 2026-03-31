@extends('layouts.app')

@section('title', 'Party Management')

@push('styles')
<style>
.nav-tabs { border-bottom: 1px solid var(--border); }
.nav-tabs .nav-link { border: none; border-radius: 8px 8px 0 0; color: var(--muted); font-weight: 500; font-size: 13.5px; padding: 9px 16px; transition: .2s; }
.nav-tabs .nav-link:hover { color: var(--primary); background: var(--primary-light); }
.nav-tabs .nav-link.active { background: var(--primary); color: #fff; }
.table-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); margin-top: 16px; }
</style>
@endpush

@section('page_title', 'Party Management')
@section('navbar_right')
  <button class="btn btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#addStoreModal">
    <i class="bi bi-plus-circle me-1"></i> Add Party
  </button>
@endsection

@section('content')

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="storeTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#list" role="tab">Party List</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inventory" role="tab">Inventory</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contacts" role="tab">Contacts</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab">Settings</a></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- STORE LIST -->
    <div class="tab-pane fade show active" id="list" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Parties</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Party Name</th><th>Code</th><th>Manager</th><th>Phone</th><th>Address</th><th>Location</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
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
          <h6 class="fw-bold mb-0">Party Inventory</h6>
          <div><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal"><i class="bi bi-plus-circle me-1"></i> Add Inventory</button></div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Party</th><th>SKU Count</th><th>Low Stock</th><th>Last Sync</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="inventoryTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CONTACTS -->
    <div class="tab-pane fade" id="contacts" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Party Contacts</h6>
          <div><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="bi bi-plus-circle me-1"></i> Add Contact</button></div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Party</th><th>Contact Person</th><th>Phone</th><th>Email</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="contactsTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SETTINGS -->
    <div class="tab-pane fade" id="settings" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Party Settings</h6>
          <div><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal"><i class="bi bi-plus-circle me-1"></i> Add Setting</button></div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Party</th><th>Notifications</th><th>Sync</th><th>Notes</th><th class="text-end">Actions</th></tr></thead>
            <tbody id="settingsTbody"><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- MODALS -->
  <div class="modal fade" id="addStoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Add Party</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="addStoreForm">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Party Name</label><input type="text" name="store_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Code</label><input type="text" name="code" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Manager</label><input type="text" name="manager" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
            <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select>
            </div>
            <div class="col-12">
              <label class="form-label">Location (Geolocation)</label>
              <div style="display:flex;gap:8px;align-items:center;">
                <input type="text" name="latitude" id="add_lat" class="form-control" placeholder="Latitude" readonly style="flex:1;">
                <input type="text" name="longitude" id="add_lng" class="form-control" placeholder="Longitude" readonly style="flex:1;">
                <button type="button" onclick="captureLocation('add_lat','add_lng','add_map_preview')" class="btn btn-outline-primary btn-sm" style="white-space:nowrap;">
                  <i class="bi bi-geo-alt-fill"></i> Get Location
                </button>
              </div>
              <div id="add_map_preview" style="display:none;margin-top:8px;border-radius:8px;overflow:hidden;height:160px;border:1px solid #E2E8F0;">
                <iframe id="add_map_frame" src="" style="width:100%;height:100%;border:none;"></iframe>
              </div>
            </div>
          </div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary text-white" id="saveStoreBtn">Save</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editStoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Edit Party</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="editStoreForm">
          <input type="hidden" id="edit_store_id" name="id">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Party Name</label><input type="text" name="store_name" id="edit_store_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Code</label><input type="text" name="code" id="edit_code" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Manager</label><input type="text" name="manager" id="edit_manager" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
            <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" id="edit_address" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select name="status" id="edit_status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select>
            </div>
            <div class="col-12">
              <label class="form-label">Location (Geolocation)</label>
              <div style="display:flex;gap:8px;align-items:center;">
                <input type="text" name="latitude" id="edit_lat" class="form-control" placeholder="Latitude" readonly style="flex:1;">
                <input type="text" name="longitude" id="edit_lng" class="form-control" placeholder="Longitude" readonly style="flex:1;">
                <button type="button" onclick="captureLocation('edit_lat','edit_lng','edit_map_preview')" class="btn btn-outline-primary btn-sm" style="white-space:nowrap;">
                  <i class="bi bi-geo-alt-fill"></i> Get Location
                </button>
              </div>
              <div id="edit_map_preview" style="display:none;margin-top:8px;border-radius:8px;overflow:hidden;height:160px;border:1px solid #E2E8F0;">
                <iframe id="edit_map_frame" src="" style="width:100%;height:100%;border:none;"></iframe>
              </div>
            </div>
          </div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary text-white" id="updateStoreBtn">Update</button></div>
      </div>
    </div>
  </div>

  <!-- Other modals: Inventory, Contact, Setting -->
  <div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Add Inventory</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="addInventoryForm">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Party</label><select name="store_id" class="form-select store-select"></select></div>
            <div class="col-md-6"><label class="form-label">SKU Count</label><input type="number" name="sku_count" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Low Stock</label><input type="number" name="low_stock_items" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Last Sync</label><input type="date" name="last_sync" class="form-control"></div>
          </div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary text-white" id="saveInventoryBtn">Save</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Add Contact</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="addContactForm">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Party</label><select name="store_id" class="form-select store-select"></select></div>
            <div class="col-md-6"><label class="form-label">Contact Person</label><input type="text" name="contact_person" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
          </div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary text-white" id="saveContactBtn">Save</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addSettingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header"><h5 class="modal-title">Add Setting</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="addSettingForm">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Party</label><select name="store_id" class="form-select store-select"></select></div>
            <div class="col-md-6"><label class="form-label">Notifications</label><select name="notifications_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-md-6"><label class="form-label">Sync</label><select name="sync_enabled" class="form-select"><option value="1">Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control"></textarea></div>
          </div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary text-white" id="saveSettingBtn">Save</button></div>
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
  return res.json();
}
async function delReq(url){
  const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
  return res.json();
}

function showToast(msg, ok=true){
  const container = document.getElementById('toastContainer');
  const t = document.createElement('div');
  t.className = `toast align-items-center text-bg-${ok?'success':'danger'} border-0 show mb-2`;
  t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
  container.appendChild(t);
  setTimeout(()=> t.remove(), 3000);
}

function storeRowHtml(s){
  const statusBadge = s.status?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>';
  const typeBadge = s.type === 'customer'
    ? '<span class="badge bg-info text-dark ms-1" style="font-size:10px;">Customer</span>'
    : '<span class="badge bg-primary ms-1" style="font-size:10px;">Store</span>';
  let locationCell = '<span style="color:#94A3B8;font-size:12px;">—</span>';
  if(s.latitude && s.longitude){
    const lat = parseFloat(s.latitude).toFixed(6);
    const lng = parseFloat(s.longitude).toFixed(6);
    locationCell = `
      <div style="display:flex;flex-direction:column;gap:4px;">
        <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank"
           style="display:inline-flex;align-items:center;gap:5px;background:#EFF6FF;color:#2563EB;border:1px solid #BFDBFE;border-radius:6px;padding:4px 10px;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
          <i class="bi bi-geo-alt-fill"></i> View on Map
        </a>
        <span style="font-size:10px;color:#94A3B8;">${lat}, ${lng}</span>
      </div>`;
  }
  const editBtn = s.type !== 'customer'
    ? `<button class="btn btn-light btn-sm edit-store" data-id="${s.real_id}"><i class="bi bi-pencil text-primary"></i></button>`
    : `<a href="/customers/${s.real_id}/edit" class="btn btn-light btn-sm"><i class="bi bi-pencil text-primary"></i></a>`;
  const delBtn = s.type !== 'customer'
    ? `<button class="btn btn-light btn-sm delete-store" data-id="${s.real_id}"><i class="bi bi-trash text-danger"></i></button>`
    : '';
  return `<tr data-id="${s.id}"><td>${s.store_name}${typeBadge}</td><td>${s.code||''}</td><td>${s.manager||''}</td><td>${s.phone||''}</td><td>${s.address||''}</td><td>${locationCell}</td><td>${statusBadge}</td><td class="text-end">${editBtn} ${delBtn}</td></tr>`;
}

async function loadStores(){
  const res = await fetch('/store/list', { headers:{ 'Accept':'application/json' } });
  const j = await res.json();
  const tbody = document.getElementById('storesTbody'); tbody.innerHTML='';
  if(j && j.ok && j.data.length){
    j.data.forEach(s=> tbody.insertAdjacentHTML('beforeend', storeRowHtml(s)));
    populateStoreSelects(j.data);
  } else { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No parties.</td></tr>'; }
}

function populateStoreSelects(list){
  document.querySelectorAll('.store-select').forEach(sel => {
    sel.innerHTML = '<option value="">Select Party</option>';
    list.forEach(s => sel.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.store_name}</option>`));
  });
}

document.addEventListener('click', function(e){
  const saveStore = e.target.closest('#saveStoreBtn');
  const editStore = e.target.closest('.edit-store');
  const updateStore = e.target.closest('#updateStoreBtn');
  const delStore = e.target.closest('.delete-store');

  if(saveStore){
    const fd = new FormData(document.getElementById('addStoreForm'));
    postJson('/store/store', Object.fromEntries(fd)).then(res => {
      if(res.ok){ loadStores(); showToast('Saved'); bootstrap.Modal.getInstance(document.getElementById('addStoreModal')).hide(); }
    });
  }
  if(editStore){
    const id = editStore.dataset.id;
    fetch(`/store/edit/${id}`, { headers:{ 'Accept':'application/json' } }).then(r=>r.json()).then(j=>{
      if(j.ok){
        const s = j.data;
        document.getElementById('edit_store_id').value = s.id;
        document.getElementById('edit_store_name').value = s.store_name;
        document.getElementById('edit_status').value = s.status?1:0;
        // populate location
        document.getElementById('edit_lat').value = s.latitude || '';
        document.getElementById('edit_lng').value = s.longitude || '';
        if(s.latitude && s.longitude){
          const preview = document.getElementById('edit_map_preview');
          document.getElementById('edit_map_frame').src = `https://maps.google.com/maps?q=${s.latitude},${s.longitude}&z=15&output=embed`;
          preview.style.display = 'block';
        }
        new bootstrap.Modal(document.getElementById('editStoreModal')).show();
      }
    });
  }
  if(updateStore){
    const id = document.getElementById('edit_store_id').value;
    const fd = new FormData(document.getElementById('editStoreForm'));
    postJson(`/store/update/${id}`, Object.fromEntries(fd)).then(res => {
      if(res.ok){ loadStores(); showToast('Updated'); bootstrap.Modal.getInstance(document.getElementById('editStoreModal')).hide(); }
    });
  }
  if(delStore){
    const id = delStore.dataset.id; if(!confirm('Delete?')) return;
    delReq(`/store/delete/${id}`).then(r => { if(r.ok) location.reload(); });
  }
});

function captureLocation(latId, lngId, previewId) {
  if (!navigator.geolocation) { alert('Geolocation not supported by your browser.'); return; }
  const btn = event.currentTarget;
  const origText = btn.innerHTML;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Getting...';
  btn.disabled = true;
  navigator.geolocation.getCurrentPosition(
    pos => {
      btn.innerHTML = origText; btn.disabled = false;
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      document.getElementById(latId).value = lat;
      document.getElementById(lngId).value = lng;
      const preview = document.getElementById(previewId);
      const frameId = previewId.replace('_preview', '_frame');
      document.getElementById(frameId).src =
        `https://www.openstreetmap.org/export/embed.html?bbox=${parseFloat(lng)-.01},${parseFloat(lat)-.01},${parseFloat(lng)+.01},${parseFloat(lat)+.01}&layer=mapnik&marker=${lat},${lng}`;
      preview.style.display = 'block';
    },
    err => { btn.innerHTML = origText; btn.disabled = false; alert('Unable to get location. Please allow location access in your browser.'); },
    { enableHighAccuracy: true, timeout: 15000 }
  );
}

document.addEventListener('DOMContentLoaded', loadStores);
</script>
@endpush
