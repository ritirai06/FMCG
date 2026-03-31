@extends('layouts.app')

@section('title', 'Warehouse Management | FMCG Admin')

@push('styles')
<style>
/* PANEL */
.panel {
  background: var(--panel);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,.08);
  backdrop-filter: blur(8px);
}

/* TABLE */
.table thead { background: rgba(99,102,241,0.08); }
.table tbody tr:hover { background: rgba(99,102,241,0.05); transition: .3s; }
.status-dot { width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px; }
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}
.manager-img {
  width: 42px; height: 42px; border-radius: 12px; object-fit: cover;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

/* MODAL (Glassmorphic Popup) */
.modal-content {
  border-radius: 16px;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(20px);
  box-shadow: 0 10px 40px rgba(0,0,0,.25);
  border: none;
}
.modal-header {
  border-bottom: none;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: white;
  border-radius: 16px 16px 0 0;
}
.modal-header h5 { font-weight: 600; }
.modal-body {
  padding: 20px;
}
.modal-footer { border-top: none; }
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-building me-2 text-primary"></i>Warehouse Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
      <i class="bi bi-plus-circle me-1"></i>Add Warehouse
    </button>
  </div>

  <div class="row mt-4 g-3">
    <div class="col-md-3">
      <div class="panel text-center">
        <h6 class="text-muted">Total Warehouses</h6>
        <h3 class="fw-bold text-primary">{{ $total }}</h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel text-center">
        <h6 class="text-muted">Active</h6>
        <h3 class="fw-bold text-success">{{ $active }}</h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel text-center">
        <h6 class="text-muted">Inactive</h6>
        <h3 class="fw-bold text-danger">{{ $inactive }}</h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel text-center">
        <h6 class="text-muted">Managers</h6>
        <h3 class="fw-bold text-warning">{{ $managers }}</h3>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
    <input type="text" id="searchInput" class="form-control w-25" placeholder="Search warehouse...">
    <select id="statusFilter" class="form-select w-25">
      <option value="">All Status</option>
      <option value="Active">Active</option>
      <option value="Inactive">Inactive</option>
    </select>
  </div>

  <!-- TABLE AREA -->
  <div class="panel mt-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Warehouse</th>
            <th>Manager</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
@forelse($warehouses as $warehouse)
<tr>
    <td>{{ $warehouse->name }}</td>
    <td class="d-flex align-items-center gap-3">
        <img src="https://via.placeholder.com/40" class="manager-img">
        <div>
            <div class="fw-semibold">{{ $warehouse->manager_name }}</div>
            <small class="text-muted">Manager</small>
        </div>
    </td>
    <td>{{ $warehouse->contact }}</td>
    <td>{{ $warehouse->location }}</td>
    <td>
        @if($warehouse->status == 'Active')
            <span class="status-dot status-active"></span>Active
        @else
            <span class="status-dot status-inactive"></span>Inactive
        @endif
    </td>
    <td class="text-end">
         <button class="btn btn-light btn-sm btn-edit"
         data-id="{{ $warehouse->id }}"
         data-name="{{ $warehouse->name }}"
         data-manager="{{ $warehouse->manager_name }}"
         data-contact="{{ $warehouse->contact }}"
         data-location="{{ $warehouse->location }}"
         data-status="{{ $warehouse->status }}"
         title="Edit">
           <i class="bi bi-pencil text-primary"></i>
         </button>
         <form action="{{ route('warehouse.delete', $warehouse->id) }}"
               method="POST" class="d-inline"
               onsubmit="return confirm('Are you sure you want to delete this warehouse?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-light btn-sm">
                <i class="bi bi-trash text-danger"></i>
            </button>
         </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">No Warehouses Found</td>
</tr>
@endforelse
        </tbody>
      </table>
    </div>
  </div>

<!-- ADD WAREHOUSE MODAL -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <form action="{{ route('warehouse.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5>Add Warehouse</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="name" class="form-control mb-3" placeholder="Warehouse Name" required>
          <input type="text" name="manager_name" class="form-control mb-3" placeholder="Manager Name" required>
          <input type="text" name="contact" class="form-control mb-3" placeholder="Contact Number" required>
          <label class="form-label">Location</label>
          <select name="location" class="form-select mb-3" required>
            <option value="">Select location</option>
            @if(isset($localities) && $localities->count())
              @foreach($localities as $loc)
                <option value="{{ $loc->name }}">{{ $loc->name }} ({{ $loc->pincode }})</option>
              @endforeach
            @endif
          </select>
          <select name="status" class="form-select mb-3">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT WAREHOUSE MODAL -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5>Edit Warehouse</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="name" id="editName" class="form-control mb-3" required>
          <input type="text" name="manager_name" id="editManager" class="form-control mb-3" required>
          <input type="text" name="contact" id="editContact" class="form-control mb-3" required>
          <label class="form-label">Location</label>
          <select name="location" id="editLocation" class="form-select mb-3" required>
            <option value="">Select location</option>
            @if(isset($localities) && $localities->count())
              @foreach($localities as $loc)
                <option value="{{ $loc->name }}">{{ $loc->name }} ({{ $loc->pincode }})</option>
              @endforeach
            @endif
          </select>
          <select name="status" id="editStatus" class="form-select mb-3">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.dataset.id;
      const name = this.dataset.name || '';
      const manager = this.dataset.manager || '';
      const contact = this.dataset.contact || '';
      const location = this.dataset.location || '';
      const status = this.dataset.status || '';

      document.getElementById('editName').value = name;
      document.getElementById('editManager').value = manager;
      document.getElementById('editContact').value = contact;
      document.getElementById('editLocation').value = location;
      document.getElementById('editStatus').value = status;
      document.getElementById('editForm').action = '/warehouse/' + id;

      new bootstrap.Modal(document.getElementById('editWarehouseModal')).show();
    });
  });

  document.getElementById("searchInput").addEventListener("keyup", function() {
      filterTable();
  });

  document.getElementById("statusFilter").addEventListener("change", function() {
      filterTable();
  });

  function filterTable() {
      let search = document.getElementById("searchInput").value.toLowerCase();
      let status = document.getElementById("statusFilter").value;
      let rows = document.querySelectorAll("tbody tr");

      rows.forEach(row => {
          let name = row.cells[0].innerText.toLowerCase();
          let rowStatus = row.cells[4].innerText.trim();
          let matchSearch = name.includes(search);
          let matchStatus = status === "" || rowStatus.includes(status);
          row.style.display = (matchSearch && matchStatus) ? "" : "none";
      });
  }
});
</script>
@endpush
