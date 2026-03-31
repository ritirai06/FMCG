@extends('layouts.app')

@section('title', 'Brand Status')

@push('styles')
<style>
/* Page-specific styles */
.table-card {
  background: var(--panel, #fff);
  border-radius: 18px;
  box-shadow: 0 15px 40px rgba(0,0,0,.06);
  margin-top: 24px;
  padding: 20px;
}
.brand-logo {
  width: 45px; height: 45px; border-radius: 12px; object-fit: cover;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.status-active { color: #22c55e; font-weight: 600; }
.status-inactive { color: #ef4444; font-weight: 600; }

.form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
.form-switch .form-check-input:checked { background-color: #22c55e; border-color: #22c55e; }
</style>
@endpush

@section('content')
  <div class="row g-4 mb-4 mt-2">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-muted">Total Brands</h6>
            <h2 id="totalCount" class="fw-bold">{{ $total }}</h2>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-success">Active Brands</h6>
            <h2 id="activeCount" class="fw-bold text-success">{{ $active }}</h2>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-danger">Inactive Brands</h6>
            <h2 id="inactiveCount" class="fw-bold text-danger">{{ $inactive }}</h2>
        </div>
    </div>
  </div>

  <div class="topbar">
    <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Brand Activation Management</h5>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Logo</th>
            <th>Brand Name</th>
            <th>Status</th>
            <th class="text-center">Toggle</th>
          </tr>
        </thead>
        <tbody>
          @foreach($brands as $brand)
          <tr id="row-{{ $brand->id }}">
            <td><img src="{{ asset('storage/'.$brand->logo) }}" class="brand-logo"></td>
            <td><strong>{{ $brand->name }}</strong></td>
            <td id="status-{{ $brand->id }}">
              @if($brand->status == 'Active')
                <span class="status-active">Active</span>
              @else
                <span class="status-inactive">Inactive</span>
              @endif
            </td>
            <td class="text-center">
              <div class="form-check form-switch d-inline-block">
                <input class="form-check-input toggleSwitch" type="checkbox" data-id="{{ $brand->id }}" {{ $brand->status == 'Active' ? 'checked' : '' }}>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- CONFIRM MODAL -->
  <div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <div class="modal-header">
          <h5 class="modal-title text-primary"><i class="bi bi-exclamation-circle me-2"></i>Confirm Action</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><p id="confirmText"></p></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmBtn">Yes, Proceed</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
let selectedBrandId = null;
let selectedCheckbox = null;
const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
const confirmText = document.getElementById('confirmText');
const confirmBtn = document.getElementById('confirmBtn');

document.querySelectorAll('.toggleSwitch').forEach(switchBtn => {
    switchBtn.addEventListener('change', function(e){
        selectedBrandId = this.dataset.id;
        selectedCheckbox = this;
        let action = this.checked ? "activate" : "deactivate";
        confirmText.innerHTML = `Are you sure you want to <strong>${action}</strong> this brand?`;
        confirmModal.show();
    });
});

confirmBtn.addEventListener('click', function(){
    fetch("{{ route('brands.toggleStatus') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
        body: JSON.stringify({ id: selectedBrandId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            let statusCell = document.getElementById("status-"+selectedBrandId);
            if(data.status === "Active"){
                statusCell.innerHTML = '<span class="status-active">Active</span>';
                selectedCheckbox.checked = true;
            } else {
                statusCell.innerHTML = '<span class="status-inactive">Inactive</span>';
                selectedCheckbox.checked = false;
            }
            document.getElementById('totalCount').innerText = data.total;
            document.getElementById('activeCount').innerText = data.active;
            document.getElementById('inactiveCount').innerText = data.inactive;
        }
        confirmModal.hide();
    });
});
</script>
@endpush
