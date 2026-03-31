@extends('layouts.app')

@section('title', 'Delivery Partner Management')

@push('styles')
<style>
/* Page-specific styles */
.table-card{
  background:var(--panel, #fff);
  border-radius:18px;
  box-shadow:0 15px 40px rgba(0,0,0,.08);
  margin-top:25px;padding:20px;
  backdrop-filter:blur(10px);
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

.city-header:hover { background: #f0f9ff !important; border-color: #3b82f6 !important; }
.city-header.expanded { background: #dbeafe !important; border-color: #3b82f6 !important; }
.toggle-icon { transition: transform 0.3s ease; }
.toggle-icon.rotated { transform: rotate(180deg); }
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-truck text-primary me-2"></i>Delivery Partner Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
      <i class="bi bi-plus-circle me-1"></i>Add Partner
    </button>
  </div>

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
            <th>Vehicle Number</th>
            <th>Zones</th>
            <th>Assigned Orders</th>
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
            <td class="dp-vehicle-number"><span class="badge bg-secondary">{{ $p->vehicle_number ?? '-' }}</span></td>
            <td class="dp-zones">
              @if($p->zone_labels->isNotEmpty())
                <div class="d-flex flex-wrap gap-1">
                  @foreach($p->zone_labels as $zone)
                    <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:11px;font-weight:500;">{{ $zone }}</span>
                  @endforeach
                </div>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
            <td class="dp-orders">
              @if($p->order_count > 0)
                <span class="badge bg-primary cursor-pointer" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#orders-{{ $p->id }}">{{ $p->order_count }} Orders</span>
                <div class="collapse mt-1" id="orders-{{ $p->id }}">
                  @foreach($p->assigned_orders as $ao)
                  <div style="font-size:11px;padding:2px 0;">
                    <a href="{{ route('orders.show', $ao->id) }}" class="text-primary fw-semibold">#{{ $ao->order_number }}</a>
                    <span class="badge badge-sm ms-1
                      @if($ao->status==='Delivered') bg-success
                      @elseif($ao->status==='Assigned') bg-info
                      @elseif($ao->status==='Picked') bg-warning
                      @else bg-secondary @endif"
                      style="font-size:9px;">{{ $ao->status }}</span>
                  </div>
                  @endforeach
                </div>
              @else
                <span class="text-muted small">No orders</span>
                <a href="#" class="d-block small text-primary dp-assign-order-link" data-id="{{ $p->id }}"><i class="bi bi-plus-circle me-1"></i>Assign Order</a>
              @endif
            </td>
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
        <tbody id="noDp" style="display:none"><tr><td colspan="10" class="text-center text-muted">No partners found.</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- MODALS -->
  <div class="modal fade" id="addPartnerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-plus-circle text-primary me-2"></i><span id="modalTitle">Add Delivery Partner</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="partnerForm" method="POST" action="{{ route('delivery.person.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="partnerId">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" id="partnerName" type="text" class="form-control" placeholder="Enter name" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" id="partnerPhone" type="text" class="form-control" placeholder="+91 ..."></div>
            <div class="col-md-6"><label class="form-label">Email</label><input name="email" id="partnerEmail" type="email" class="form-control" placeholder="example@email.com"></div>
            <div class="col-md-6"><label class="form-label">Vehicle</label><input name="vehicle" id="partnerVehicle" type="text" class="form-control" placeholder="Bike / Car"></div>
            <div class="col-md-6"><label class="form-label">Vehicle Number</label><input name="vehicle_number" id="partnerVehicleNumber" type="text" class="form-control" placeholder="DL01AB1234"></div>
            <div class="col-md-6"><label class="form-label">Status</label><select name="status" id="partnerStatus" class="form-select"><option>Active</option><option>Inactive</option></select></div>
            <div class="col-md-6"><label class="form-label">Profile Image</label><input name="avatar" id="partnerAvatar" type="file" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="partnerSave">Save Partner</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="assignZoneModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-geo-alt text-success me-2"></i>Assign Zones</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label fw-bold">Select Cities & Localities</label>
          <div class="cities-scroll-container border rounded p-3" style="max-height:400px;overflow-y:auto;background:#f8f9fa;">
            @foreach($cities as $city)
            <div class="city-expandable mb-2">
              <div class="city-header p-2 bg-white rounded d-flex align-items-center justify-content-between" style="cursor:pointer;border:1px solid #e2e8f0;" onclick="toggleCityLocalities({{ $city->id }})">
                <span class="fw-semibold"><i class="bi bi-geo-alt-fill text-primary me-2"></i>{{ $city->name }}</span>
                <i class="bi bi-chevron-down toggle-icon" id="icon{{ $city->id }}"></i>
              </div>
              <div class="localities-list ms-4 mt-2" id="localities{{ $city->id }}" style="display:none;">
                @forelse($city->localities as $loc)
                <div class="form-check mb-2">
                  <input class="form-check-input locality-checkbox-zone" type="checkbox" value="{{ $loc->id }}" id="localityZone{{ $loc->id }}" data-city-id="{{ $city->id }}" data-city-name="{{ $city->name }}">
                  <label class="form-check-label" for="localityZone{{ $loc->id }}"><i class="bi bi-pin-map text-success me-1"></i>{{ $loc->name }}</label>
                </div>
                @empty
                <small class="text-muted">No localities</small>
                @endforelse
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveZones">Assign Zones</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="assignOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-box2-heart text-warning me-2"></i>Assign Orders to Delivery Partner</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-3">Select orders to assign. Already assigned orders are pre-checked.</p>
          <input type="text" id="orderSearchInput" class="form-control form-control-sm mb-3" placeholder="Search order number or customer...">
          <div style="max-height:360px;overflow-y:auto;" id="orderListContainer">
            @forelse($allOrders as $ord)
            <div class="order-item d-flex align-items-center justify-content-between p-2 mb-1 rounded border" style="font-size:13px;"
                 data-search="{{ strtolower($ord->order_number . ' ' . $ord->customer_name) }}">
              <div class="d-flex align-items-center gap-2">
                <input type="checkbox" class="order-assign-cb form-check-input" value="{{ $ord->id }}"
                  {{ $ord->assigned_delivery_person_id ? 'checked disabled' : '' }}>
                <div>
                  <span class="fw-semibold">#{{ $ord->order_number }}</span>
                  <span class="text-muted ms-1">{{ $ord->customer_name }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                @if($ord->assigned_delivery_person_id)
                  <span class="badge bg-secondary" style="font-size:10px;">Already Assigned</span>
                @endif
                <span class="badge
                  @if($ord->status==='Pending') bg-warning text-dark
                  @elseif($ord->status==='Assigned') bg-info
                  @else bg-secondary @endif"
                  style="font-size:10px;">{{ $ord->status }}</span>
              </div>
            </div>
            @empty
            <p class="text-muted text-center py-3">No pending orders available.</p>
            @endforelse
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveOrders"><i class="bi bi-check2 me-1"></i>Assign Selected Orders</button>
        </div>
      </div>
    </div>
  </div>

  <div id="dpToast" class="position-fixed bottom-0 end-0 p-3" style="z-index:2000"></div>
@endsection

@push('scripts')
<script>
function toggleCityLocalities(cityId) {
  const container = document.getElementById('localities' + cityId);
  const icon = document.getElementById('icon' + cityId);
  const header = icon.closest('.city-header');
  if (container.style.display === 'none') { container.style.display = 'block'; icon.classList.add('rotated'); header.classList.add('expanded'); }
  else { container.style.display = 'none'; icon.classList.remove('rotated'); header.classList.remove('expanded'); }
}

document.getElementById('selectAllDp')?.addEventListener('change', function(){ document.querySelectorAll('.dp-checkbox').forEach(cb=>cb.checked=this.checked); });
document.getElementById('dpSearch')?.addEventListener('input', function(){
  const q=this.value.toLowerCase();
  Array.from(document.querySelectorAll('#dpBody tr')).forEach(r=>{ r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; });
  const visible = Array.from(document.querySelectorAll('#dpBody tr')).filter(r=>r.style.display!=='none').length;
  document.getElementById('dpCount').textContent = visible;
  document.getElementById('noDp').style.display = visible? 'none':'';
});

let activeAssignId = null;

document.addEventListener('click', async function(e){
  const editBtn = e.target.closest('.dp-edit');
  const az = e.target.closest('.dp-assign-zone');
  const ao = e.target.closest('.dp-assign-order');
  const del = e.target.closest('.dp-delete');

  if(editBtn){
    const id = editBtn.dataset.id;
    const res = await fetch(`{{ url('delivery-person') }}/${id}/details`);
    if(!res.ok) return;
    const data = await res.json();
    document.getElementById('modalTitle').textContent = 'Edit Partner';
    document.getElementById('partnerForm').action = '{{ route('delivery.person.update') }}';
    document.getElementById('partnerId').value=data.id;
    document.getElementById('partnerName').value=data.name;
    document.getElementById('partnerPhone').value=data.phone;
    document.getElementById('partnerEmail').value=data.email;
    document.getElementById('partnerVehicle').value=data.vehicle;
    document.getElementById('partnerVehicleNumber').value=data.vehicle_number || '';
    document.getElementById('partnerStatus').value=data.status || 'Active';
    new bootstrap.Modal(document.getElementById('addPartnerModal')).show();
  }
  if(az){ activeAssignId = az.dataset.id; loadAssignedCities(az.dataset.id); new bootstrap.Modal(document.getElementById('assignZoneModal')).show(); }
  if(ao || e.target.closest('.dp-assign-order-link')){ 
    const btn = ao || e.target.closest('.dp-assign-order-link');
    activeAssignId = btn.dataset.id;
    // Uncheck all, then pre-check orders already assigned to this partner
    document.querySelectorAll('.order-assign-cb').forEach(cb => { if (!cb.disabled) cb.checked = false; });
    // Pre-check orders assigned to this partner
    document.querySelectorAll('.order-assign-cb').forEach(cb => {
      const item = cb.closest('.order-item');
      // We'll reload pre-checks via fetch
    });
    new bootstrap.Modal(document.getElementById('assignOrderModal')).show();
  }
  if(del){ if(!confirm('Delete?')) return; await postJson('{{ route('delivery.person.delete') }}',{id: del.dataset.id}); location.reload(); }
});

async function postJson(url, body){ const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); const res = await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},body:JSON.stringify(body)}); return res.json(); }

async function loadAssignedCities(partnerId) {
  try {
    const res = await fetch(`{{ url('delivery-person') }}/${partnerId}/details`);
    const data = await res.json();
    document.querySelectorAll('.locality-checkbox-zone').forEach(cb => cb.checked = false);
    if (data.locality_ids) data.locality_ids.forEach(lid => {
      const cb = document.querySelector(`.locality-checkbox-zone[value="${lid}"]`);
      if (cb) cb.checked = true;
    });
  } catch (e) {}
}

document.getElementById('saveZones')?.addEventListener('click', async ()=>{
  const lids = Array.from(document.querySelectorAll('.locality-checkbox-zone:checked')).map(cb => parseInt(cb.value));
  const cids = [...new Set(Array.from(document.querySelectorAll('.locality-checkbox-zone:checked')).map(cb => parseInt(cb.dataset.cityId)))];
  await postJson('{{ route('delivery.person.assignZones') }}', { id: activeAssignId, city_ids: cids, locality_ids: lids });
  location.reload();
});

document.getElementById('orderSearchInput')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.order-item').forEach(item => {
    item.style.display = item.dataset.search.includes(q) ? '' : 'none';
  });
});

document.getElementById('saveOrders')?.addEventListener('click', async ()=>{
  const orderIds = Array.from(document.querySelectorAll('.order-assign-cb:checked:not(:disabled)')).map(cb => parseInt(cb.value));
  const res = await postJson('{{ route('delivery.person.assignOrders') }}', { id: activeAssignId, order_ids: orderIds });
  if (res.ok) location.reload();
  else alert('Failed to assign orders.');
});

document.getElementById('partnerForm')?.addEventListener('submit', async function(e){
  // fallback to direct submit if AJAX fails
  // But normally we'd do location.reload() on success
});
</script>
@endpush
