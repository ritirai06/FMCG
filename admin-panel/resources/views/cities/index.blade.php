@extends('layouts.app')

@section('title', 'City & Locality Management')

@push('styles')
<style>
/* Page-specific TABS */
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
  background:var(--panel, #fff);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  margin-top:20px;
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
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-geo-alt text-primary me-2"></i>City & Locality Management</h5>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#city">City List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#locality">Locality List</button></li>
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
          <thead><tr><th>City</th><th>State</th><th>Pin Code</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
            @foreach($cities as $city)
            <tr>
                <td>{{ $city->name }}</td>
                <td>{{ $city->state }}</td>
                <td>{{ $city->pincode ?: '-' }}</td>
                <td>
                    @if($city->status == 'Active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td class="text-end">
                  <button class="btn btn-light btn-sm"
                    onclick="editCity('{{ $city->id }}','{{ $city->name }}','{{ $city->state }}','{{ $city->pincode }}','{{ $city->status }}')">
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
                <td>{{ $locality->pincode ?: '-' }}</td>
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
          <form method="POST" action="{{ route('city.store') }}" class="row g-3">
              @csrf
            <input type="hidden" name="id" id="cityId">
            <div class="col-md-6"><label class="form-label">City Name</label><input type="text" name="name" id="cityName" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">State</label><input type="text" name="state" id="cityState" class="form-control"></div>
            <div class="col-md-12"><label class="form-label">Pin Code</label><input type="text" name="pincode" id="cityPincode" class="form-control" placeholder="Enter 6 Digit Pin Code" minlength="6" maxlength="6" pattern="\d{6}"></div>
            <div class="col-md-12"><label class="form-label">Status</label><select name="status" id="cityStatus" class="form-select"><option>Active</option><option>Inactive</option></select></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
            </div>
          </form>
        </div>
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
              <input type="text" name="pincode" id="localityPincode" class="form-control" placeholder="Enter 6 Digit Pin Code" required minlength="6" maxlength="6" pattern="\d{6}">
            </div>
            <div class="col-md-6">
              <label>Status</label>
              <select name="status" id="localityStatus" class="form-select">
                <option>Active</option>
                <option>Inactive</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function editCity(id,name,state,pincode,status){
  document.getElementById('cityModalTitle').innerHTML='<i class="bi bi-pencil me-2"></i>Edit City';
  document.getElementById('cityId').value = id || '';
  document.getElementById('cityName').value = name || '';
  document.getElementById('cityState').value = state || '';
  document.getElementById('cityPincode').value = pincode || '';
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
@endpush
