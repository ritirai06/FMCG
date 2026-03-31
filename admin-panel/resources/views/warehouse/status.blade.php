@extends('layouts.app')

@section('title', 'Warehouse Status')

@push('styles')
<style>
/* Page-specific PANEL */
.panel {
  background: var(--panel, #fff);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,.08);
  backdrop-filter: blur(8px);
}

/* TABLE */
.table thead { background: rgba(99,102,241,0.08); }
.table tbody tr:hover { background: rgba(99,102,241,0.05); transition: .3s; }
.status-badge {
  padding: 6px 12px;
  border-radius: 10px;
  font-weight: 500;
}
.status-active { background: #dcfce7; color: #16a34a; }
.status-inactive { background: #fee2e2; color: #dc2626; }

/* SWITCH */
.form-switch .form-check-input {
  width: 2.5em; height: 1.3em;
  background-color: #d1d5db;
  border: none;
  transition: 0.3s;
}
.form-switch .form-check-input:checked {
  background-color: var(--primary);
}
.form-switch .form-check-input:focus {
  box-shadow: 0 0 0 0.25rem rgba(99,102,241,0.3);
}
</style>
@endpush

@section('content')
  <div class="topbar mb-4">
      <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Warehouse Status</h5>
      <div>
          <span class="badge bg-primary">
              Total: {{ $warehouses->total() }}
          </span>
      </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-card mb-4">
    <form method="GET">
      <div class="row g-3">
          <div class="col-md-6">
              <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Search warehouse...">
          </div>
          <div class="col-md-4">
              <select name="status" class="form-select">
                  <option value="">All Status</option>
                  <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
          </div>
          <div class="col-md-2">
              <button class="btn btn-primary w-100">Filter</button>
          </div>
      </div>
    </form>
  </div>

  <!-- TABLE -->
  <div class="panel">
      <div class="table-responsive">
          <table class="table align-middle">
              <thead>
                  <tr>
                      <th>Warehouse</th>
                      <th>Manager</th>
                      <th>Contact</th>
                      <th>Status</th>
                      <th class="text-center">Action</th>
                  </tr>
              </thead>
              <tbody>
                  @forelse($warehouses as $warehouse)
                  <tr>
                      <td>{{ $warehouse->name }}</td>
                      <td>{{ $warehouse->manager_name }}</td>
                      <td>{{ $warehouse->contact }}</td>
                      <td>
                          <span class="status-badge
                              {{ $warehouse->status == 'Active'
                                  ? 'status-active'
                                  : 'status-inactive' }}">
                              {{ $warehouse->status }}
                          </span>
                      </td>
                      <td class="text-center">
                          <div class="form-check form-switch d-flex justify-content-center">
                              <input class="form-check-input toggle-status"
                                     type="checkbox"
                                     data-id="{{ $warehouse->id }}"
                                     {{ $warehouse->status == 'Active' ? 'checked' : '' }}>
                          </div>
                      </td>
                  </tr>
                  @empty
                  <tr>
                      <td colspan="5" class="text-center text-muted">
                          No Warehouses Found
                      </td>
                  </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
      <div class="mt-3">
          {{ $warehouses->links() }}
      </div>
  </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-status').forEach(toggle => {
    toggle.addEventListener('change', function() {
        let warehouseId = this.dataset.id;
        fetch(`/warehouse/${warehouseId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            // location.reload(); // Reloading might be disruptive, but keeping it as per original
            // Better to just update the badge
            const row = this.closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            if (this.checked) {
              statusBadge.textContent = "Active";
              statusBadge.className = "status-badge status-active";
            } else {
              statusBadge.textContent = "Inactive";
              statusBadge.className = "status-badge status-inactive";
            }
        });
    });
});
</script>
@endpush
