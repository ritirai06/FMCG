@extends('layouts.app')

@section('title', 'Sales Person Management')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
/* Page-specific styles */
.panel{background:var(--panel, #fff);border-radius:var(--radius);padding:24px;margin-top:20px;box-shadow:0 15px 40px rgba(0,0,0,.08);}
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
</style>
@endpush

@section('content')
    <div class="topbar">
    <h5><i class="bi bi-people-fill me-2 text-primary"></i>Sales Person Management</h5>
    <button id="addSalesBtn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#salesModal">
      <i class="bi bi-plus-circle me-1"></i>Add Sales Person
    </button>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="salesTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" id="listTabBtn" data-bs-toggle="tab" data-bs-target="#listTab" type="button" role="tab">Sales List</button></li>
    <li class="nav-item"><button class="nav-link" id="statusTabBtn" data-bs-toggle="tab" data-bs-target="#statusTab" type="button" role="tab">Activate / Deactivate</button></li>
    <li class="nav-item"><button class="nav-link" id="cityTabBtn" data-bs-toggle="tab" data-bs-target="#cityTab" type="button" role="tab">Assign Cities</button></li>
    <li class="nav-item"><button class="nav-link" id="geoTabBtn" data-bs-toggle="tab" data-bs-target="#geolocationTab" type="button" role="tab"><i class="bi bi-geo-alt-fill me-1"></i>Geolocation</button></li>
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

        <div class="table-responsive" style="overflow-x:auto;">
        <table class="table table-sm table-hover align-middle" style="min-width:1800px;">
          <thead style="background:#f8f9fa;position:sticky;top:0;z-index:10;">
            <tr>
              <th style="width:40px;min-width:40px;"><input type="checkbox" id="selectAll"></th>
              <th style="min-width:180px;">Sales Person</th>
              <th style="min-width:110px;">Phone</th>
              <th style="min-width:150px;">Email</th>
              <th style="min-width:100px;">City</th>
              <th style="min-width:100px;text-align:right;">Base Salary</th>
              <th style="min-width:100px;text-align:right;">Allowance</th>
              <th style="min-width:80px;text-align:center;">Bonus %</th>
              <th style="min-width:100px;text-align:right;">Bonus Amt</th>
              <th style="min-width:110px;text-align:right;">Target Sales</th>
              <th style="min-width:110px;text-align:center;">Actual Sales</th>
              <th style="min-width:90px;text-align:center;">Incentive %</th>
              <th style="min-width:110px;text-align:center;">Incentive Amt</th>
              <th style="min-width:110px;text-align:right;">Total Salary</th>
              <th style="min-width:150px;text-align:center;">Location</th>
              <th style="min-width:90px;">Status</th>
              <th style="min-width:120px;text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody id="spTableBody">
            @foreach($salesPeople as $sp)
            <tr>
              <td><input type="checkbox" class="sp-checkbox" value="{{ $sp->id }}"></td>
              <td class="d-flex align-items-center gap-3"><img src="{{ $sp->avatar_url ?? 'https://via.placeholder.com/40' }}" class="sales-img"><div><div class="fw-semibold">{{ $sp->name }}</div><small>{{ $sp->role ?? 'Executive' }}</small></div></td>
              <td style="white-space:nowrap;">{{ $sp->phone }}</td>
              <td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px;" title="{{ $sp->email }}">{{ $sp->email }}</td>
              <td>{{ optional($sp->city)->name }}</td>
              @php
                $base = floatval($sp->base_salary ?? 0);
                $allow = floatval($sp->allowance ?? 0);
                $bonusPct = floatval($sp->bonus_percent ?? 0);
                $bonusAmt = $sp->calculated_bonus ?? 0;
                $target = floatval($sp->target_sales ?? 0);
                $actualSales = floatval($sp->actual_sales ?? 0);
                $incentivePct = floatval($sp->incentive_percent ?? 0);
                $incentiveAmt = $sp->calculated_incentive ?? 0;
                $totalSalary = $sp->total_salary ?? ($base + $allow + $bonusAmt + $incentiveAmt);
              @endphp
              <td style="text-align:right;">₹{{ number_format($base,0) }}</td>
              <td style="text-align:right;">₹{{ number_format($allow,0) }}</td>
              <td style="text-align:center;">{{ number_format($bonusPct,2) }}%</td>
              <td style="text-align:right;">₹{{ number_format($bonusAmt,0) }}</td>
              <td style="text-align:right;">₹{{ $target ? number_format($target,0) : '0' }}</td>
              <td style="text-align:center;"><span class="badge bg-info">₹{{ number_format($actualSales,0) }}</span></td>
              <td style="text-align:center;">{{ $incentivePct ? number_format($incentivePct,2) . '%' : '0%' }}</td>
              <td style="text-align:center;"><span class="badge bg-success">₹{{ number_format($incentiveAmt,0) }}</span></td>
              <td style="text-align:right;"><strong>₹{{ number_format($totalSalary,0) }}</strong></td>
              <td style="text-align:center;min-width:180px;">
                @if($sp->current_latitude && $sp->current_longitude)
                  <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="showLocationOnMap({{ $sp->id }}, {{ $sp->current_latitude }}, {{ $sp->current_longitude }}, '{{ addslashes($sp->name) }}')"
                    title="View on map">
                    <i class="bi bi-geo-alt-fill text-danger"></i> View Map
                  </button>
                  @if($sp->address)
                    <div class="small text-muted mt-1" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $sp->address }}">📍 {{ $sp->address }}</div>
                  @else
                    <div class="small text-muted mt-1">{{ number_format($sp->current_latitude,4) }}, {{ number_format($sp->current_longitude,4) }}</div>
                  @endif
                @else
                  <span class="badge bg-light text-muted border">Not Set</span>
                @endif
              </td>
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
            <tr><td colspan="17" class="text-center text-muted py-4">No matching sales persons found.</td></tr>
          </tbody>
        </table>
        </div>
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
        <h5><i class="bi bi-geo-alt me-2"></i><bold>Assign Cities & Localities</bold></h5>
        <div class="row g-3">
          <!-- Step 1: Select Sales Person -->
          <div class="col-md-12">
            <label class="fw-bold">Step 1: Select Sales Person</label>
            <select id="assignCitySp" class="form-select">
              <option value="">Select Sales Person</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}">{{ $sp->name }}</option>
              @endforeach
            </select>
          </div>
          <!-- Step 2: Select Cities -->
          <div class="col-md-6">
            <label class="fw-bold">Step 2: Select Cities</label>
            <div class="border rounded p-2" style="max-height:300px;overflow-y:auto;">
              @foreach($cities as $c)
              <div class="form-check">
                <input class="form-check-input city-checkbox" type="checkbox" value="{{ $c->id }}" id="city{{ $c->id }}" data-city-name="{{ $c->name }}">
                <label class="form-check-label" for="city{{ $c->id }}">
                  {{ $c->name }}
                </label>
              </div>
              @endforeach
            </div>
            <small class="text-muted">Select one or more cities</small>
          </div>
          <!-- Step 3: Select Localities -->
          <div class="col-md-6">
            <label class="fw-bold">Step 3: Select Localities</label>
            <div id="localitiesContainer" class="border rounded p-2" style="max-height:300px;overflow-y:auto;background:#f8f9fa;">
              <div class="text-center text-muted py-4">
                <i class="bi bi-info-circle" style="font-size:24px;"></i>
                <p class="mt-2 mb-0">Select cities first to load localities</p>
              </div>
            </div>
            <small class="text-muted">Localities will load based on selected cities</small>
          </div>
          <!-- Selected Summary -->
          <div class="col-md-12">
            <div class="alert alert-info">
              <strong><i class="bi bi-info-circle me-2"></i>Assignment Summary:</strong>
              <div id="assignmentSummary" class="mt-2">
                <div>Cities: <span id="selectedCitiesCount">0</span> selected</div>
                <div>Localities: <span id="selectedLocalitiesCount">0</span> selected</div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button type="button" class="btn btn-secondary me-2" id="clearAssignment"><i class="bi bi-x-circle me-1"></i>Clear Selection</button>
          <button type="button" class="btn btn-primary" id="saveAssignCities"><i class="bi bi-check-circle me-1"></i>Save Assignment</button>
        </div>
      </div>
    </div>

    <!-- Geolocation Tab -->
    <div class="tab-pane fade" id="geolocationTab">
      <div class="panel">

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="fw-bold">Select Sales Person</label>
            <select id="geoSalesPerson" class="form-select">
              <option value="">-- Select Sales Person --</option>
              @foreach($salesPeople as $sp)
              <option value="{{ $sp->id }}"
                data-lat="{{ $sp->current_latitude }}"
                data-lng="{{ $sp->current_longitude }}"
                data-address="{{ addslashes($sp->address ?? '') }}"
                data-tracking="{{ $sp->location_tracking_enabled ? '1' : '0' }}"
                data-updated="{{ $sp->last_location_update ? $sp->last_location_update->diffForHumans() : 'Never' }}">
                {{ $sp->name }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="fw-bold">Latitude</label>
            <input type="number" id="geoLat" class="form-control" step="any" placeholder="e.g. 19.0760" readonly>
          </div>
          <div class="col-md-2">
            <label class="fw-bold">Longitude</label>
            <input type="number" id="geoLng" class="form-control" step="any" placeholder="e.g. 72.8777" readonly>
          </div>
          <div class="col-md-4">
            <label class="fw-bold">Address <span class="text-muted fw-normal small">(auto-fetched)</span></label>
            <input type="text" id="geoAddress" class="form-control" placeholder="Click map to auto-fill address">
          </div>
        </div>

        <div class="row g-2 mb-3 align-items-center">
          <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="useMyGeoLocation">
              <i class="bi bi-crosshair me-1"></i>Use My Location
            </button>
          </div>
          <div class="col-auto">
            <button type="button" class="btn btn-primary btn-sm" id="updateGeoLocation">
              <i class="bi bi-save me-1"></i>Save Location
            </button>
          </div>
          <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-info" id="viewHistory">
              <i class="bi bi-clock-history me-1"></i>History
            </button>
          </div>
          <div class="col-auto ms-auto d-flex align-items-center gap-3">
            <span class="text-muted small">Last saved: <strong id="lastUpdated">—</strong></span>
            <div id="geoStatusBadge"></div>
          </div>
        </div>

        <!-- Map -->
        <div id="mapContainer" style="height:480px;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">
          <div id="leafletMap" style="width:100%;height:100%;"></div>
        </div>
        <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Click anywhere on the map to set location. Drag the marker to adjust.</div>
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
          <h6 class="text-primary mb-3"><i class="bi bi-person-circle me-2"></i>Sales Person Details</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" name="name" id="spName" class="form-control" required></div>
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
          </div>
          <h6 class="text-primary mb-3"><i class="bi bi-cash-stack me-2"></i>Salary & Incentive Details</h6>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Base Salary (₹)</label><input type="number" name="base_salary" id="spBaseSalary" class="form-control" step="0.01" placeholder="20000"></div>
            <div class="col-md-6"><label class="form-label">Allowance (₹)</label><input type="number" name="allowance" id="spAllowance" class="form-control" step="0.01" placeholder="5000"></div>
            <div class="col-md-6">
              <label class="form-label">Bonus (%)</label><input type="number" name="bonus_percent" id="spBonusPercent" class="form-control" step="0.01" placeholder="10">
              <small class="text-muted">Percentage of base salary</small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Target Sales (₹)</label><input type="number" name="target_sales" id="spTargetSales" class="form-control" step="0.01" placeholder="100000">
              <small class="text-muted">Monthly sales target</small>
            </div>
            <div class="col-md-12">
              <label class="form-label">Incentive Percentage (%)</label><input type="number" name="incentive_percent" id="spIncentivePercent" class="form-control" step="0.01" placeholder="10" value="10">
              <small class="text-muted">Applied on sales exceeding target (Default: 10%)</small>
            </div>
          </div>
          <div class="alert alert-info mt-3 mb-0">
            <small>
              <strong><i class="bi bi-info-circle me-1"></i>Incentive Calculation:</strong><br>
              • If Actual Sales ≤ Target Sales → Incentive = ₹0<br>
              • If Actual Sales > Target Sales → Incentive = (Extra Sales × Incentive %) / 100<br>
              • Total Salary = Base Salary + Allowance + Bonus + Incentive
            </small>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  <!-- History Modal -->
  <div class="modal fade" id="locationHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Location History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <table class="table table-sm">
            <thead><tr><th>Date & Time</th><th>Location</th><th>Activity</th><th>Notes</th></tr></thead>
            <tbody id="locationHistoryBody"><tr><td colspan="4" class="text-center text-muted">Loading...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Toasts container -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    <div id="toastContainer"></div>
  </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// UI Helpers
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

async function fetchJson(url){
  const res = await fetch(url, { headers: { 'Accept':'application/json' } });
  if (!res.ok) throw new Error('Network');
  return res.json();
}

// Sidebar & Menu
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}

// Sales Table Filter
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

// Modals
document.getElementById('addSalesBtn')?.addEventListener('click', () => {
  document.querySelector('.modal-title').innerHTML = '<i class="bi bi-plus-circle me-2 text-primary"></i>Add Sales Person';
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

// Status Toggle
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
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
    body: JSON.stringify({ ids: ids, action: action })
  })
  .then(res => res.json())
  .then(() => { showToast('Status updated', 'success'); setTimeout(()=>location.reload(),600); })
  .catch(() => showToast('Failed to update status','danger'));
}

// Territory Assignment
document.getElementById('assignCitySp')?.addEventListener('change', async function(){
  const id = this.value;
  document.querySelectorAll('.city-checkbox').forEach(cb => cb.checked = false);
  document.getElementById('localitiesContainer').innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-info-circle" style="font-size:24px;"></i><p class="mt-2 mb-0">Select cities first to load localities</p></div>';
  updateAssignmentSummary();
  if(!id) return;
  try{
    const data = await fetchJson(`{{ url('sales-person') }}/${id}/details`);
    data.city_ids.forEach(cityId => {
      const checkbox = document.querySelector(`.city-checkbox[value="${cityId}"]`);
      if (checkbox) checkbox.checked = true;
    });
    if (data.city_ids.length > 0) { await loadLocalitiesForCities(data.city_ids, data.locality_ids); }
    updateAssignmentSummary();
  }catch(e){ showToast('Failed to load details','danger'); }
});

document.querySelectorAll('.city-checkbox').forEach(checkbox => {
  checkbox.addEventListener('change', async function() {
    const selectedCities = Array.from(document.querySelectorAll('.city-checkbox:checked')).map(cb => parseInt(cb.value));
    if (selectedCities.length > 0) { await loadLocalitiesForCities(selectedCities); }
    else { document.getElementById('localitiesContainer').innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-info-circle" style="font-size:24px;"></i><p class="mt-2 mb-0">Select cities first to load localities</p></div>'; }
    updateAssignmentSummary();
  });
});

async function loadLocalitiesForCities(cityIds, preselectedLocalityIds = []) {
  const container = document.getElementById('localitiesContainer');
  container.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 mb-0">Loading localities...</p></div>';
  try {
    const promises = cityIds.map(cityId => 
      fetch(`{{ url('territory/get-localities') }}/${cityId}`).then(res => res.json()).then(localities => ({ cityId, localities }))
    );
    const results = await Promise.all(promises);
    let html = '';
    results.forEach(result => {
      const cityCheckbox = document.querySelector(`.city-checkbox[value="${result.cityId}"]`);
      const cityName = cityCheckbox ? cityCheckbox.dataset.cityName : `City ${result.cityId}`;
      if (result.localities && result.localities.length > 0) {
        html += `<div class="mb-3"><strong class="text-primary">${cityName}</strong><div class="ms-3 mt-2">`;
        result.localities.forEach(locality => {
          const isChecked = preselectedLocalityIds.includes(locality.id) ? 'checked' : '';
          html += `<div class="form-check">
            <input class="form-check-input locality-checkbox" type="checkbox" value="${locality.id}" id="locality${locality.id}" ${isChecked} onchange="updateAssignmentSummary()">
            <label class="form-check-label" for="locality${locality.id}">${locality.name}</label>
          </div>`;
        });
        html += `</div></div>`;
      }
    });
    container.innerHTML = html || '<div class="text-center text-muted py-4"><p class="mt-2 mb-0">No localities found</p></div>';
    updateAssignmentSummary();
  } catch (error) { container.innerHTML = '<div class="text-center text-danger py-4"><p class="mt-2 mb-0">Failed to load</p></div>'; }
}

function updateAssignmentSummary() {
  document.getElementById('selectedCitiesCount').textContent = document.querySelectorAll('.city-checkbox:checked').length;
  document.getElementById('selectedLocalitiesCount').textContent = document.querySelectorAll('.locality-checkbox:checked').length;
}

document.getElementById('clearAssignment')?.addEventListener('click', () => {
  document.querySelectorAll('.city-checkbox').forEach(cb => cb.checked = false);
  document.getElementById('localitiesContainer').innerHTML = '<div class="text-center text-muted py-4"><p class="mt-2 mb-0">Select cities first</p></div>';
  updateAssignmentSummary();
});

document.getElementById('saveAssignCities')?.addEventListener('click', async function(){
  const sp = document.getElementById('assignCitySp').value;
  if(!sp){ showToast('Select Sales Person','warning'); return; }
  const cityIds = Array.from(document.querySelectorAll('.city-checkbox:checked')).map(cb => parseInt(cb.value));
  const localityIds = Array.from(document.querySelectorAll('.locality-checkbox:checked')).map(cb => parseInt(cb.value));
  if (cityIds.length === 0) { showToast('Select at least one city','warning'); return; }
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const btn = this; setBtnLoading(btn, true);
  try{
    await fetch("{{ route('sales.person.assignCities') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify({ sales_person_id: sp, city_ids: cityIds }) });
    if (localityIds.length > 0) {
      await fetch("{{ route('sales.person.assignLocalities') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, body: JSON.stringify({ sales_person_id: sp, locality_ids: localityIds }) });
    }
    showToast(`Saved successfully`,'success');
  }catch(e){ showToast('Failed to save assignment','danger'); } finally { setBtnLoading(btn, false); }
});

// Geolocation
let locationMap = null;
let locationMarker = null;

function reverseGeocode(lat, lng) {
  fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
    headers: { 'Accept-Language': 'en' }
  })
  .then(r => r.json())
  .then(data => {
    const addr = data.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    document.getElementById('geoAddress').value = addr;
  })
  .catch(() => {
    document.getElementById('geoAddress').value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
  });
}

function placeMarker(lat, lng, label) {
  if (locationMarker) locationMap.removeLayer(locationMarker);
  locationMarker = L.marker([lat, lng], { draggable: true })
    .addTo(locationMap)
    .bindPopup(`<b>${label || 'Location'}</b><br>${lat.toFixed(5)}, ${lng.toFixed(5)}`)
    .openPopup();

  locationMarker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    document.getElementById('geoLat').value = pos.lat.toFixed(7);
    document.getElementById('geoLng').value = pos.lng.toFixed(7);
    reverseGeocode(pos.lat, pos.lng);
  });
}

function initializeMap(lat, lng, zoom) {
  if (!locationMap) {
    locationMap = L.map('leafletMap').setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors', maxZoom: 19
    }).addTo(locationMap);

    locationMap.on('click', function(e) {
      const { lat: nLat, lng: nLng } = e.latlng;
      document.getElementById('geoLat').value = nLat.toFixed(7);
      document.getElementById('geoLng').value = nLng.toFixed(7);
      const spName = document.getElementById('geoSalesPerson').options[document.getElementById('geoSalesPerson').selectedIndex]?.text || 'Location';
      placeMarker(nLat, nLng, spName);
      reverseGeocode(nLat, nLng);
    });
  } else {
    locationMap.setView([lat, lng], zoom);
    locationMap.invalidateSize();
  }
}

document.getElementById('geoTabBtn')?.addEventListener('shown.bs.tab', () => {
  if (!locationMap) initializeMap(20.5937, 78.9629, 5);
  else locationMap.invalidateSize();
});

document.getElementById('geoSalesPerson')?.addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  const lat = parseFloat(opt.dataset.lat);
  const lng = parseFloat(opt.dataset.lng);
  const addr = opt.dataset.address || '';

  document.getElementById('lastUpdated').textContent = opt.dataset.updated || '—';
  document.getElementById('geoAddress').value = addr;
  document.getElementById('geoStatusBadge').innerHTML = '';

  if (!locationMap) initializeMap(20.5937, 78.9629, 5);

  if (this.value && lat && lng) {
    document.getElementById('geoLat').value = lat.toFixed(7);
    document.getElementById('geoLng').value = lng.toFixed(7);
    locationMap.setView([lat, lng], 13);
    placeMarker(lat, lng, opt.text);
  } else {
    document.getElementById('geoLat').value = '';
    document.getElementById('geoLng').value = '';
    if (locationMarker) { locationMap.removeLayer(locationMarker); locationMarker = null; }
    locationMap.setView([20.5937, 78.9629], 5);
  }
});

document.getElementById('useMyGeoLocation')?.addEventListener('click', function() {
  if (!navigator.geolocation) { showToast('Geolocation not supported', 'warning'); return; }
  this.disabled = true;
  this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Locating...';
  const btn = this;
  navigator.geolocation.getCurrentPosition(
    pos => {
      const lat = pos.coords.latitude, lng = pos.coords.longitude;
      document.getElementById('geoLat').value = lat.toFixed(7);
      document.getElementById('geoLng').value = lng.toFixed(7);
      if (!locationMap) initializeMap(lat, lng, 13);
      else locationMap.setView([lat, lng], 13);
      const spName = document.getElementById('geoSalesPerson').options[document.getElementById('geoSalesPerson').selectedIndex]?.text || 'My Location';
      placeMarker(lat, lng, spName);
      reverseGeocode(lat, lng);
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Use My Location';
    },
    () => {
      showToast('Could not get location', 'danger');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-crosshair me-1"></i>Use My Location';
    }
  );
});

document.getElementById('updateGeoLocation')?.addEventListener('click', async function() {
  const sp  = document.getElementById('geoSalesPerson').value;
  const lat = document.getElementById('geoLat').value;
  const lng = document.getElementById('geoLng').value;
  const addr = document.getElementById('geoAddress').value;

  if (!sp)  { showToast('Select a sales person first', 'warning'); return; }
  if (!lat || !lng) { showToast('Click the map to set a location first', 'warning'); return; }

  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  setBtnLoading(this, true);

  try {
    const res = await fetch('{{ route("sales.person.updateLocation") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      body: JSON.stringify({ sales_person_id: sp, latitude: lat, longitude: lng, address: addr })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Location saved successfully', 'success');
      document.getElementById('lastUpdated').textContent = 'Just now';
      document.getElementById('geoStatusBadge').innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Saved</span>';

      // Sync the dropdown option data so re-selecting shows correct location
      const opt = document.getElementById('geoSalesPerson').options[document.getElementById('geoSalesPerson').selectedIndex];
      opt.dataset.lat     = lat;
      opt.dataset.lng     = lng;
      opt.dataset.address = addr;
      opt.dataset.updated = 'Just now';

      // Sync list table location cell
      syncListTableLocation(sp, lat, lng, addr);
    } else {
      showToast(data.message || 'Save failed', 'danger');
    }
  } catch(e) {
    showToast('Server error', 'danger');
  } finally {
    setBtnLoading(this, false);
  }
});

function syncListTableLocation(spId, lat, lng, addr) {
  // Find the row in the list table and update the location cell without reload
  const rows = document.querySelectorAll('#spTableBody tr');
  rows.forEach(row => {
    const editBtn = row.querySelector('.editBtn');
    if (editBtn && editBtn.dataset.id == spId) {
      const locCell = row.querySelector('td:nth-last-child(3)');
      if (locCell) {
        const displayAddr = addr ? `<div class="small text-muted mt-1" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${addr}">📍 ${addr}</div>` :
          `<div class="small text-muted mt-1">${parseFloat(lat).toFixed(4)}, ${parseFloat(lng).toFixed(4)}</div>`;
        locCell.innerHTML = `<button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="showLocationOnMap(${spId}, ${lat}, ${lng}, '')"><i class="bi bi-geo-alt-fill text-danger"></i> View Map</button>${displayAddr}`;
      }
    }
  });
}

function showLocationOnMap(spId, lat, lng, name) {
  const geoTab = document.getElementById('geoTabBtn');
  new bootstrap.Tab(geoTab).show();
  setTimeout(() => {
    const sel = document.getElementById('geoSalesPerson');
    sel.value = spId;
    sel.dispatchEvent(new Event('change'));
  }, 200);
}

document.getElementById('viewHistory')?.addEventListener('click', async function() {
  const sp = document.getElementById('geoSalesPerson').value;
  if (!sp) { showToast('Select a sales person first', 'warning'); return; }
  try {
    const data = await fetchJson(`{{ url('sales-person') }}/${sp}/location-history`);
    const tbody = document.getElementById('locationHistoryBody');
    if (!data.locations.length) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No history found</td></tr>';
    } else {
      tbody.innerHTML = data.locations.map(l => `
        <tr>
          <td>${l.recorded_at}</td>
          <td>${l.address || `${parseFloat(l.latitude).toFixed(5)}, ${parseFloat(l.longitude).toFixed(5)}`}</td>
          <td><span class="badge bg-secondary">${l.activity_type || 'update'}</span></td>
          <td>${l.notes || '—'}</td>
        </tr>`).join('');
    }
    new bootstrap.Modal(document.getElementById('locationHistoryModal')).show();
  } catch(e) { showToast('Failed to load history', 'danger'); }
});

document.addEventListener('DOMContentLoaded', () => {
  updateVisibleCount();
});
</script>
@endpush