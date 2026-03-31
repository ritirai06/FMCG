@extends('layouts.app')

@section('title', 'Category Status | FMCG Admin')

@push('styles')
<style>
/* Table Card */
.table-card{
  background:var(--panel);
  border-radius:var(--radius);
  box-shadow:0 15px 40px rgba(0,0,0,.06);
  margin-top:24px;
  overflow:hidden;
  backdrop-filter:blur(10px);
  padding:20px;
}
.table thead{background:rgba(99,102,241,0.08);}
.table tbody tr:hover{background:rgba(99,102,241,.06);transform:scale(1.01);transition:0.3s;}
.category-img{width:45px;height:45px;border-radius:12px;object-fit:cover;}
.status-active{color:#22c55e;font-weight:600;}
.status-inactive{color:#ef4444;font-weight:600;}

/* Stats Cards */
.stats-container{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-top:24px;
}
.stat-card{
  background:var(--glass);
  backdrop-filter:blur(14px);
  border-radius:var(--radius);
  padding:20px;
  box-shadow:0 10px 40px rgba(0,0,0,.06);
  text-align:center;
}
.stat-card h6{color:var(--text-muted);font-size:12px;font-weight:600;margin-bottom:10px;}
.stat-card h3{color:var(--primary);font-weight:700;margin:0;}
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Category Status</h5>
    <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to List</a>
  </div>

  <!-- STATS CARDS -->
  <div class="stats-container">
    <div class="stat-card">
      <h6>TOTAL CATEGORIES</h6>
      <h3>{{ count($categories) }}</h3>
    </div>
    <div class="stat-card">
      <p style="color:#22c55e; margin-bottom: 5px; font-size: 12px; font-weight: 600;">ACTIVE CATEGORIES</p>
      <h3 style="color:#22c55e;">{{ count($categories->where('status','Active')) }}</h3>
    </div>
    <div class="stat-card">
      <p style="color:#ef4444; margin-bottom: 5px; font-size: 12px; font-weight: 600;">INACTIVE CATEGORIES</p>
      <h3 style="color:#ef4444;">{{ count($categories->where('status','Inactive')) }}</h3>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created Date</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
@forelse($categories as $category)
<tr>
  <td class="d-flex align-items-center gap-3">
    @if($category->image)
      <img src="{{ asset('storage/'.$category->image) }}" class="category-img">
    @else
      <img src="https://via.placeholder.com/45" class="category-img">
    @endif
    <div>
      <strong>{{ $category->name }}</strong><br>
      <small class="text-muted">{{ $category->slug }}</small>
    </div>
  </td>
  <td>{{ Str::limit($category->description ?? '-', 50) }}</td>
  <td>
    <select class="form-select form-select-sm status-select" data-category-id="{{ $category->id }}" style="max-width:140px;">
      <option value="Active" {{ $category->status == 'Active' ? 'selected' : '' }}>
        Active
      </option>
      <option value="Inactive" {{ $category->status == 'Inactive' ? 'selected' : '' }}>
        Inactive
      </option>
    </select>
  </td>
  <td>
    <small class="text-muted">{{ $category->created_at->format('M d, Y') }}</small>
  </td>
  <td class="text-end">
    <button class="btn btn-light btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}" title="Edit">
      <i class="bi bi-pencil text-primary"></i>
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted py-4">
    <i class="bi bi-inbox" style="font-size:32px;"></i><br>
    No categories found
  </td>
</tr>
@endforelse
</tbody>
      </table>
    </div>
  </div>

  <!-- EDIT MODALS -->
  @foreach($categories as $category)
  <div class="modal fade" id="editModal{{ $category->id }}">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <form class="edit-category-form" data-edit-category="{{ $category->id }}">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title text-primary">
              <i class="bi bi-pencil-square me-2"></i>Edit Category
            </h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label>Category Name</label>
              <input type="text"
                     name="name"
                     value="{{ $category->name }}"
                     class="form-control">
            </div>

            <div class="mb-3">
              <label>Description</label>
              <textarea name="description"
                        class="form-control">{{ $category->description }}</textarea>
            </div>

            <div class="mb-3">
              <label>Status</label>
              <select name="status" class="form-select">
                <option value="Active"
                  {{ $category->status=='Active'?'selected':'' }}>
                  Active
                </option>
                <option value="Inactive"
                  {{ $category->status=='Inactive'?'selected':'' }}>
                  Inactive
                </option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light"
                    data-bs-dismiss="modal">
                    Cancel
            </button>
            <button type="submit" class="btn btn-primary">
                Update
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
  @endforeach

@endsection

@push('scripts')
<script>
// Status dropdown change handler
document.addEventListener('DOMContentLoaded', function() {
  const statusSelects = document.querySelectorAll('.status-select');
  
  statusSelects.forEach(select => {
    select.addEventListener('change', function(e) {
      const categoryId = this.getAttribute('data-category-id');
      const newStatus = this.value;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
      
      const btn = this;
      btn.disabled = true;
      
      fetch(`/categories/update/${categoryId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `status=${encodeURIComponent(newStatus)}&_method=PUT`
      })
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(data => {
        if (data.success) {
          // Update the UI immediately
          const tr = btn.closest('tr');
          const statusCell = tr.querySelector('td:nth-child(3)');
          // No need to reload or anything, but let's keep it consistent
          btn.disabled = false;
        } else {
          alert('Update failed: ' + (data.message || 'Unknown error'));
          location.reload();
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
        location.reload();
      });
    });
  });
  
  // Edit form submission
  const editForms = document.querySelectorAll('.edit-category-form');
  editForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-edit-category');
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
      
      const btn = this.querySelector('button[type="submit"]');
      btn.disabled = true;
      
      const fd = new FormData(this);
      
      fetch(`/categories/update/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: fd
      })
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(data => {
        if (data.success) {
          const modalEl = document.getElementById('editModal' + id);
          const modal = bootstrap.Modal.getInstance(modalEl);
          if(modal) modal.hide();
          
          setTimeout(() => location.reload(), 300);
        } else {
          alert('Update failed: ' + (data.message || 'Unknown error'));
          btn.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
        btn.disabled = false;
      });
    });
  });
});
</script>
@endpush
